<?php
namespace NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Workflow;
use NorthslopePL\Workflow\WorkflowState;

class StateValidator implements WorkflowValidator
{
	/**
	 * @param Workflow $workflow
	 *
	 * @return WorkflowValidationResult
	 */
	public function validate(Workflow $workflow)
	{
		$result = new WorkflowValidationResult();

		$this->checkIfHasAnyStates($workflow, $result);
		$this->checkIfStateIdsAreUnique($workflow, $result);
		$this->checkIfStateIdsValues($workflow, $result);
		$this->checkStatesEventNames($workflow, $result);
		$this->checkIfAllFinalStatesAreMarkedAsOnes($workflow, $result);

		return $result;
	}

	private function checkIfHasAnyStates(Workflow $workflow, WorkflowValidationResult $result)
	{
		$states = $workflow->getStates();

		if (empty($states)) {
			$result->addValidationError(new WorkflowValidationError($workflow, 'Workflow must have any states set'));
		}
	}

	private function checkIfStateIdsAreUnique(Workflow $workflow, WorkflowValidationResult $result)
	{
		$knownStateIds = [];

		foreach ($workflow->getStates() as $index => $state) {
			if (isset($knownStateIds[$state->getStateId()])) {
				$message = sprintf('WorkflowState at position [%d] has duplicated stateId: "%s"', $index, $state->getStateId());
				$result->addValidationError(new WorkflowValidationError($state, $message));
			} else {
				$knownStateIds[$state->getStateId()] = true;
			}
		}
	}

	private function checkIfStateIdsValues(Workflow $workflow, WorkflowValidationResult $result)
	{
		foreach ($workflow->getStates() as $state) {
			$stateId = $state->getStateId();
			if (!is_string($stateId)) {
				$message = sprintf('WorkflowState.stateId must be a string, found: %s, %s', gettype($stateId), $stateId);
				$result->addValidationError(new WorkflowValidationError($state, $message));
			}
		}
	}

	private function checkIfAllFinalStatesAreMarkedAsOnes(Workflow $workflow, WorkflowValidationResult $result)
	{
		$finalStates = $this->getAllFinalStates($workflow);

		foreach ($finalStates as $state) {
			if ($state->isFinal() !== true) {
				$message = sprintf('WorkflowState "%s" is a final state but is not marked as one - make isFinal() return true', $state->getStateId());
				$result->addValidationError(new WorkflowValidationError($state, $message));
			}
		}
	}

	/**
	 * @param Workflow $workflow
	 * @return WorkflowState[]
	 */
	private function getAllFinalStates(Workflow $workflow)
	{
		$finalStates = [];
		foreach ($workflow->getStates() as $state) {
			$transitionsFromState = $workflow->getTransitionsFromStateId($state->getStateId());
			$stateIsFinalAccordingToGraph = empty($transitionsFromState);
			if ($stateIsFinalAccordingToGraph) {
				$finalStates[] = $state;
			}
		}

		return $finalStates;
	}

	private function checkStatesEventNames(Workflow $workflow, WorkflowValidationResult $result)
	{
		foreach ($workflow->getStates() as $state) {
			if (!is_array($state->getOnEnterEvents())) {
				$result->addValidationError(new WorkflowValidationError($state, sprintf('WorkflowState "%s" onEnterEvents must be an array of strings', $state->getStateId())));
			}
			if (!is_array($state->getOnExitEvents())) {
				$result->addValidationError(new WorkflowValidationError($state, sprintf('WorkflowState "%s" onExitEvents must be an array of strings', $state->getStateId())));
			}
		}
	}
}
