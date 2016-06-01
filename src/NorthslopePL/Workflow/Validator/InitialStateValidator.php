<?php
namespace NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Workflow;

class InitialStateValidator implements WorkflowValidator
{
	/**
	 * @param Workflow $workflow
	 *
	 * @return WorkflowValidationResult
	 */
	public function validate(Workflow $workflow)
	{
		$result = new WorkflowValidationResult();

		$this->checkInitialState($workflow, $result);

		return $result;
	}

	/**
	 * @param Workflow $workflow
	 * @param WorkflowValidationResult $result
	 */
	private function checkInitialState(Workflow $workflow, WorkflowValidationResult $result)
	{
		$initialState = $workflow->getInitialState();

		if ($initialState === null) {
			$result->addValidationError(new WorkflowValidationError($workflow, 'Workflow must have initialState set'));
		} else {
			if (!in_array($initialState, $workflow->getStates(), true)) {
				$result->addValidationError(new WorkflowValidationError($workflow, sprintf('Workflow.initialState "%s" must be withing Workflow.states', $initialState->getStateId())));
			}
		}
	}
}
