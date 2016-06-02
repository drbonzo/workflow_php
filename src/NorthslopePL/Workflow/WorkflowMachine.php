<?php
namespace NorthslopePL\Workflow;

use NorthslopePL\Workflow\Exceptions\WorkflowLogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class WorkflowMachine
{
	/**
	 * @var EventDispatcher
	 */
	private $eventDispatcher;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var int
	 */
	private $nestingLevelForLogging = 0;

	public function __construct(EventDispatcher $eventDispatcher)
	{
		$this->eventDispatcher = $eventDispatcher;
	}

	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @param WorkflowCollection $workflowCollection
	 * @param WorkflowContextCollection $contextCollection
	 * @param string|null $eventName
	 */
	public function execute(WorkflowCollection $workflowCollection, WorkflowContextCollection $contextCollection, $eventName = null)
	{
		$this->nestingLevelForLogging++;
		$this->log(sprintf('---- Execute-Start: %s', $eventName ? $eventName : '(none)'));

		$transitionWasPerformed = false;

		foreach ($workflowCollection->getWorkflows() as $workflow) {

			$context = $contextCollection->getContext(get_class($workflow));

			$this->eventDispatcher->dispatch(WorkflowEvents::BEGIN_EXECUTION, new WorkflowEvent($context));
			$twp = $this->executeOnWorkflow($workflowCollection, $contextCollection, $workflow, $context, $eventName);
			$transitionWasPerformed = $transitionWasPerformed || $twp;
			$this->eventDispatcher->dispatch(WorkflowEvents::END_EXECUTION, new WorkflowEvent($context));

		}

		$this->log(sprintf('---- Execute-End: %s', $eventName ? $eventName : '(none)'));

		// if something has changed in any of workflows - then rerun all workflows
		// without an event to move all transitions that wiated for their guard to complete
		if ($transitionWasPerformed) {
			$this->execute($workflowCollection, $contextCollection, null);
		}

		$this->nestingLevelForLogging--;
	}

	/**
	 * @param WorkflowCollection $workflowCollection
	 * @param WorkflowContextCollection $contextCollection
	 * @param Workflow $workflow
	 * @param WorkflowContext $context
	 * @param string|null $eventName
	 *
	 * @return boolean
	 */
	private function executeOnWorkflow(WorkflowCollection $workflowCollection, WorkflowContextCollection $contextCollection, Workflow $workflow, WorkflowContext $context, $eventName)
	{
		$this->log(sprintf('Workflow-Start: %s, Event: %s', $workflow->getName(), $eventName));

		$transitionWasPerformed = false;
		do {

			if ($context->getCurrentStateId() === null) {
				$context->setCurrentStateId($workflow->getInitialState()->getStateId());
			}

			$currentState = $workflow->getStateForStateId($context->getCurrentStateId());

			$this->log(sprintf('    Current-State: %s (%s), Event: %s', $currentState->getStateId(), get_class($currentState), $eventName));

			$transitionToRun = $this->getRunnableTransition($workflow, $context, $eventName, $currentState);

			if ($transitionToRun === null) {
				$this->log(sprintf('    Transition: NULL'));
				break;
			}

			$this->log(sprintf('    Transition: %s, to state: %s', get_class($transitionToRun), $transitionToRun->getDestinationStateId()));

			//
			//
			$this->performTransition($workflowCollection, $contextCollection, $workflow, $context, $transitionToRun, $currentState);
			$transitionWasPerformed = true;
			//
			//

			$eventName = null;

		} while (true);

		$this->log(sprintf('Workflow-End:   %s, Event: %s', $workflow->getName(), $eventName));

		return $transitionWasPerformed;
	}

	/**
	 * @param Workflow $workflow
	 * @param WorkflowContext $context
	 * @param $eventName
	 * @param WorkflowState $currentState
	 *
	 * @return WorkflowTransition|null
	 */
	private function getRunnableTransition(Workflow $workflow, WorkflowContext $context, $eventName, WorkflowState $currentState)
	{
		$potentialTransitions = $this->getPotentialTransitions($workflow, $context, $eventName, $currentState);

		if ($potentialTransitions) {
			return $potentialTransitions[0];
		} else {
			return null;
		}
	}

	/**
	 * @param Workflow $workflow
	 * @param WorkflowContext $context
	 * @param $eventName
	 * @param WorkflowState $currentState
	 *
	 * @return WorkflowTransition[]
	 */
	private function getPotentialTransitions(Workflow $workflow, WorkflowContext $context, $eventName, WorkflowState $currentState)
	{
		$allTransitionsFromCurrentState = $workflow->getTransitionsFromStateId($currentState->getStateId());

		$potentialTransitions = [];
		foreach ($allTransitionsFromCurrentState as $transition) {

			if ($eventName === null && ($transition->getEventNames() === [])) {
				if ($transition->checkGuardCondition($context)) {
					$potentialTransitions[] = $transition;
				}
			} else if (in_array($eventName, $transition->getEventNames())) {
				if ($transition->checkGuardCondition($context)) {
					$potentialTransitions[] = $transition;
				}
			} else {
				// skip this transition
			}
		}

		return $potentialTransitions;
	}

	private function performTransition(WorkflowCollection $workflowCollection, WorkflowContextCollection $contextCollection, Workflow $workflow, WorkflowContext $context, WorkflowTransition $transition, WorkflowState $currentState)
	{
		if ($transition->startsFromAnyStateId()) {
			$sourceState = $currentState;
		} else {
			$sourceState = $workflow->getStateForStateId($transition->getSourceStateId());
		}
		
		$destinationState = $workflow->getStateForStateId($transition->getDestinationStateId());

		$this->log(sprintf('        Transition-Start: %s => %s', $sourceState->getStateId(), $destinationState->getStateId()));
		$this->eventDispatcher->dispatch(WorkflowEvents::BEFORE_TRANSITION, new WorkflowEvent($context));
		{
			$this->log(sprintf('        Source:onExitAction()'));
			$sourceState->onExitAction($context);

			foreach ($sourceState->getOnExitEvents() as $eventName) {
				$this->execute($workflowCollection, $contextCollection, $eventName);
			}

			$transition->run($context);

			$context->setCurrentStateId($destinationState->getStateId());
			$this->eventDispatcher->dispatch(WorkflowEvents::STATE_CHANGED, new WorkflowEvent($context));

			$this->log(
				sprintf(
					'Workflow: %s, From: %s, To: %s, with: %s',
					$workflow->getName(),
					$transition->getSourceStateId(),
					$transition->getDestinationStateId(),
					get_class($transition)
				),
				LogLevel::INFO
			);


			$this->log(sprintf('        Destination:onEnterAction()'));
			$destinationState->onEnterAction($context);

			foreach ($destinationState->getOnEnterEvents() as $eventName) {
				$this->execute($workflowCollection, $contextCollection, $eventName);
			}
		}
		$this->eventDispatcher->dispatch(WorkflowEvents::AFTER_TRANSITION, new WorkflowEvent($context));
	}

	/**
	 * @param WorkflowCollection $workflowCollection
	 *
	 * @return string[]
	 */
	public function getAllEventNames(WorkflowCollection $workflowCollection)
	{
		$eventNames = [];

		foreach ($workflowCollection->getWorkflows() as $workflow) {
			foreach ($workflow->getTransitions() as $transition) {
				$eventNames = array_merge($eventNames, $transition->getEventNames());
			}
		}

		$eventNames = array_unique($eventNames);

		return $eventNames;
	}

	/**
	 * This will also return all events that are NOT meant to be triggered by user, but by other workflows.
	 *
	 * @param WorkflowCollection $workflowCollection
	 * @param WorkflowContextCollection $contextCollection
	 *
	 * @return string[]
	 */
	public function getAvailableEventNames(WorkflowCollection $workflowCollection, WorkflowContextCollection $contextCollection)
	{
		$eventNames = [];

		foreach ($workflowCollection->getWorkflows() as $workflow) {

			$context = $contextCollection->getContext(get_class($workflow));

			$transitions = $workflow->getTransitionsFromStateId($context->getCurrentStateId());

			foreach ($transitions as $transition) {
				if ($transition->checkGuardCondition($context)) {
					$eventNames = array_merge($eventNames, $transition->getEventNames());
				}
			}
		}

		$eventNames = array_unique($eventNames);
		return $eventNames;
	}

	private function log($message, $logLevel = LogLevel::DEBUG)
	{
		if ($this->logger) {
			$message = str_repeat('    ', $this->nestingLevelForLogging) . $message;
			$this->logger->log($logLevel, $message);
		}
	}

}
