<?php
namespace NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Workflow;
use NorthslopePL\Workflow\WorkflowState;

class TransitionValidator implements WorkflowValidator
{
	/**
	 * @param Workflow $workflow
	 *
	 * @return WorkflowValidationResult
	 */
	public function validate(Workflow $workflow)
	{
		$result = new WorkflowValidationResult();

		$this->checkIfAllStatesCanBeReachedFromInitialState($workflow, $result);
		$this->checkIfAllFinalStatesHaveNoOutgoingTransitions($workflow, $result);
		$this->checkIfStatesForTransitionsExist($workflow, $result);

		return $result;
	}

	private function checkIfAllStatesCanBeReachedFromInitialState(Workflow $workflow, WorkflowValidationResult $result)
	{
		$initialState = $workflow->getInitialState();

		if ($initialState) {

			/**
			 * @var string[] $visitedStatesId
			 *
			 * index: WorkflowState.stateId
			 */
			$visitedStatesId = [];

			$this->checkIfStateIsConnectedToInitialState($workflow, $initialState, $visitedStatesId);

			foreach ($workflow->getStates() as $state) {
				$stateHasNotBeenVisited = !(isset($visitedStatesId[$state->getStateId()]));
				if ($stateHasNotBeenVisited) {
					$errorMessage = sprintf('WorkflowState "%s" is not connected to initialState "%s"', $state->getStateId(), $initialState->getStateId());
					$result->addValidationError(new WorkflowValidationError($state, $errorMessage));
				}
			}
		}
	}

	private function checkIfAllFinalStatesHaveNoOutgoingTransitions(Workflow $workflow, WorkflowValidationResult $result)
	{
		foreach ($workflow->getStates() as $state) {
			if ($state->isFinal()) {
				$outgoingTransitionsFromFinalState = $workflow->getTransitionsFromStateId($state->getStateId());

				if (!empty($outgoingTransitionsFromFinalState)) {

					foreach ($outgoingTransitionsFromFinalState as $transition) {
						$message = sprintf('WorkflowState "%s" is marked as final, so it cannot have outgoing transitions: %s( "%s" => "%s" )', $state->getStateId(), get_class($transition), $transition->getSourceStateId(), $transition->getDestinationStateId());
					$result->addValidationError(new WorkflowValidationError($state, $message));
				}
			}
		}
	}
	}

	private function checkIfStatesForTransitionsExist(Workflow $workflow, WorkflowValidationResult $result)
	{
		$stateIDs = [];
		foreach ($workflow->getStates() as $state) {
			$stateIDs[] = $state->getStateId();
		}

		foreach ($workflow->getTransitions() as $transition) {
			$sourceStateId = $transition->getSourceStateId();
			$destinationStateId = $transition->getDestinationStateId();

			if (!in_array($sourceStateId, $stateIDs)) {
				$messageForSource = sprintf('WorkflowTransition %s ("%s" => "%s") uses invalid sourceStateId: "%s"', get_class($transition), $sourceStateId, $destinationStateId, $sourceStateId);
				$result->addValidationError(new WorkflowValidationError($transition, $messageForSource));
			}

			if (!in_array($destinationStateId, $stateIDs)) {
				$messageForDestination = sprintf('WorkflowTransition %s ("%s" => "%s") uses invalid destinationStateId: "%s"', get_class($transition), $sourceStateId, $destinationStateId, $destinationStateId);
				$result->addValidationError(new WorkflowValidationError($transition, $messageForDestination));
			}
		}
	}

	/**
	 * @param Workflow $workflow
	 * @param WorkflowState $state
	 * @param string[] $visitedStatesId
	 */
	private function checkIfStateIsConnectedToInitialState(Workflow $workflow, WorkflowState $state, &$visitedStatesId)
	{
		$stateAlreadyVisited = (isset($visitedStatesId[$state->getStateId()]));

		if ($stateAlreadyVisited) {
			return;
		}

		$visitedStatesId[$state->getStateId()] = $state->getStateId();

		// We need to go deeper
		$transitions = $workflow->getTransitionsFromStateId($state->getStateId());

		foreach ($transitions as $transition) {
			$endState = $workflow->getStateForStateId($transition->getDestinationStateId());
			$this->checkIfStateIsConnectedToInitialState($workflow, $endState, $visitedStatesId);
		}
	}

}
