<?php
namespace NorthslopePL\Workflow;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class WorkflowMachine
{
	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var int
	 */
	private $nestingLevelForLogging = 0;

	public function __construct()
	{
	}

	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @param WorkflowCollection $workflowCollection
	 * @param WorkflowContextCollection $contextCollection
	 * @param EventDispatcher $eventDispatcher
	 * @param string|null $eventName
	 */
	public function execute(WorkflowCollection $workflowCollection, WorkflowContextCollection $contextCollection, EventDispatcher $eventDispatcher, $eventName = null)
	{
		$this->nestingLevelForLogging++;
		$this->log(sprintf('---- Execute-Start: %s', $eventName ? $eventName : '(none)'));

		$transitionWasPerformed = false;

		// Try to execute each of the Workflows
		foreach ($workflowCollection->getWorkflows() as $workflow) {

			$context = $contextCollection->getContext(get_class($workflow));

			$eventDispatcher->dispatch(WorkflowEvents::BEGIN_EXECUTION, new WorkflowEvent($context));

			$twp = $this->executeOnWorkflow($workflowCollection, $contextCollection, $workflow, $context, $eventDispatcher, $eventName);
			$transitionWasPerformed = $transitionWasPerformed || $twp;

			$eventDispatcher->dispatch(WorkflowEvents::END_EXECUTION, new WorkflowEvent($context));

		}

		$this->log(sprintf('---- Execute-End: %s', $eventName ? $eventName : '(none)'));

		// If any Transition was performed in any of the Workflows - then rerun all Workflows again.
		// This time without an event.
		//
		// This allows to trigger other Transitions that waited for their Guard to be true.
		// As something has changed in the target object - some Guards may be now true.
		if ($transitionWasPerformed) {
			$this->execute($workflowCollection, $contextCollection, $eventDispatcher, null);
		}

		$this->nestingLevelForLogging--;
	}

	/**
	 * @param WorkflowCollection $workflowCollection
	 * @param WorkflowContextCollection $contextCollection
	 * @param Workflow $workflow
	 * @param WorkflowContext $context
	 * @param EventDispatcher $eventDispatcher
	 * @param string|null $eventName
	 *
	 * @return bool
	 */
	private function executeOnWorkflow(WorkflowCollection $workflowCollection, WorkflowContextCollection $contextCollection, Workflow $workflow, WorkflowContext $context, EventDispatcher $eventDispatcher, $eventName)
	{
		$this->log(sprintf('Workflow-Start: %s, Event: %s', $workflow->getName(), $eventName));

		$transitionWasPerformed = false;

		do {

			$this->ensureCurrentState($context, $workflow);

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
			$this->performTransition($workflowCollection, $contextCollection, $workflow, $context, $transitionToRun, $currentState, $eventDispatcher);

			// SAVE ALL CHANGES
			$context->commit();

			$transitionWasPerformed = true;
			//
			//

			$eventName = null;

		} while (true);

		$this->log(sprintf('Workflow-End:   %s, Event: %s', $workflow->getName(), $eventName));

		return $transitionWasPerformed;
	}

	private function ensureCurrentState(WorkflowContext $context, Workflow $workflow)
	{
		if ($context->getCurrentStateId() === null) {
			$context->setCurrentStateId($workflow->getInitialState()->getStateId());
		}
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
		$allTransitionsFromCurrentState = $workflow->getTransitionsFromState($currentState);

		$potentialTransitions = [];
		foreach ($allTransitionsFromCurrentState as $transition) {

			if ($this->eventTriggersTransition($eventName, $transition)) {
				if ($transition->checkGuardCondition($context)) {
					$potentialTransitions[] = $transition;
				}
			}
		}

		return $potentialTransitions;
	}

	/**
	 * @param $eventName
	 * @param WorkflowTransition $transition
	 *
	 * @return bool
	 */
	private function eventTriggersTransition($eventName, WorkflowTransition $transition)
	{
		if ($eventName === null && ($transition->getEventNames() === [])) {
			// if no Event was given, thent Transition must not any events triggering it
			return true;

		} else if (in_array($eventName, $transition->getEventNames())) {
			// if Event was given, then check if transition is triggered by that event
			// it's enough to trigger a transition with one of its all events
			return true;

		} else {
			// skip this transition
			return false;
		}
	}

	private function performTransition(WorkflowCollection $workflowCollection, WorkflowContextCollection $contextCollection, Workflow $workflow, WorkflowContext $context, WorkflowTransition $transition, WorkflowState $currentState, EventDispatcher $eventDispatcher)
	{
		$sourceState = $this->getSourceState($workflow, $transition, $currentState);

		$destinationState = $workflow->getStateForStateId($transition->getDestinationStateId());

		$this->log(sprintf('        Transition-Start: %s => %s', $sourceState->getStateId(), $destinationState->getStateId()));
		$eventDispatcher->dispatch(WorkflowEvents::BEFORE_TRANSITION, new WorkflowEvent($context));
		{
			// EXIT ACTION
			$this->log(sprintf('        Source:onExitAction()'));
			$sourceState->onExitAction($context);

			// EXIT EVENTS
			foreach ($sourceState->getOnExitEvents() as $eventName) {
				$this->execute($workflowCollection, $contextCollection, $eventDispatcher, $eventName);
			}

			// TRANSITON:RUN
			$transition->run($context);

			// CHANGE STATE
			$context->setCurrentStateId($destinationState->getStateId());
			$eventDispatcher->dispatch(WorkflowEvents::STATE_CHANGED, new WorkflowEvent($context));

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


			// ENTER ACTION
			$this->log(sprintf('        Destination:onEnterAction()'));
			$destinationState->onEnterAction($context);

			// ENTER EVENTS
			foreach ($destinationState->getOnEnterEvents() as $eventName) {
				$this->execute($workflowCollection, $contextCollection, $eventDispatcher, $eventName);
			}
		}
		$eventDispatcher->dispatch(WorkflowEvents::AFTER_TRANSITION, new WorkflowEvent($context));
	}

	/**
	 * Returns effective source state for the Transition.
	 *
	 * If this is a Wildcard Transition then $currentState is treated as sourceState - as such Transition can start from any State.
	 *
	 * @param Workflow $workflow
	 * @param WorkflowTransition $transition
	 * @param WorkflowState $currentState
	 *
	 * @return WorkflowState
	 */
	private function getSourceState(Workflow $workflow, WorkflowTransition $transition, WorkflowState $currentState)
	{
		if ($transition->startsFromAnyStateId()) {
			return $currentState;
		} else {
			return $workflow->getStateForStateId($transition->getSourceStateId());
		}
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

			$currentState = $workflow->getStateForStateId($context->getCurrentStateId());
			$transitions = $workflow->getTransitionsFromState($currentState);

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
