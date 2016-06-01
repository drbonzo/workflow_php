<?php
namespace NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Workflow;

class PHPDocValidator implements WorkflowValidator
{
	/**
	 * @param Workflow $workflow
	 *
	 * @return WorkflowValidationResult
	 */
	public function validate(Workflow $workflow)
	{
		$result = new WorkflowValidationResult();

		$this->checkStatesPHPDoc($workflow, $result);
		$this->checkTransitionsPHPDoc($workflow, $result);

		return $result;
	}

	private function checkStatesPHPDoc(Workflow $workflow, WorkflowValidationResult $result)
	{
		foreach ($workflow->getStates() as $state) {
			$reflectionClass = new \ReflectionObject($state);

			$onEnterActionMethod = $reflectionClass->getMethod('onEnterAction');
			$onEnterActionPhpDoc = $onEnterActionMethod->getDocComment();

			if (!preg_match('#@Workflow-Action (.+)#', $onEnterActionPhpDoc)) {
				$error = new WorkflowValidationError($state, sprintf('WorkflowState "%s" should have docComment with "@Workflow-Action" for onEnterAction() method', $state->getStateId()));
				$error->setErrorType(WorkflowValidationError::WARNING_TYPE);
				$result->addValidationError($error);
			}

			$onExitActionMethod = $reflectionClass->getMethod('onExitAction');
			$onExitActionPhpDoc = $onExitActionMethod->getDocComment();

			if (!preg_match('#@Workflow-Action (.+)#', $onExitActionPhpDoc)) {
				$error = new WorkflowValidationError($state, sprintf('WorkflowState "%s" should have docComment with "@Workflow-Action" for onExitAction() method', $state->getStateId()));
				$error->setErrorType(WorkflowValidationError::WARNING_TYPE);
				$result->addValidationError($error);
			}
		}
	}

	private function checkTransitionsPHPDoc(Workflow $workflow, WorkflowValidationResult $result)
	{
		foreach ($workflow->getTransitions() as $transition) {
			$reflectionClass = new \ReflectionObject($transition);

			$checkGuardConditionMethod = $reflectionClass->getMethod('checkGuardCondition');
			$checkGuardConditionPhpDoc = $checkGuardConditionMethod->getDocComment();

			if (!preg_match('#@Workflow-Guard (.+)#', $checkGuardConditionPhpDoc)) {
				$error = new WorkflowValidationError($transition, sprintf('WorkflowTransition %s ("%s" => "%s" should have docComment with "@Workflow-Guard" for checkGuardCondition() method', get_class($transition), $transition->getSourceStateId(), $transition->getDestinationStateId()));
				$error->setErrorType(WorkflowValidationError::WARNING_TYPE);
				$result->addValidationError($error);
			}

			$runMethod = $reflectionClass->getMethod('run');
			$runPhpDoc = $runMethod->getDocComment();

			if (!preg_match('#@Workflow-Action (.+)#', $runPhpDoc)) {
				$error = new WorkflowValidationError($transition, sprintf('WorkflowTransition %s ("%s" => "%s" should have docComment with "@Workflow-Action" for run() method', get_class($transition), $transition->getSourceStateId(), $transition->getDestinationStateId()));
				$error->setErrorType(WorkflowValidationError::WARNING_TYPE);
				$result->addValidationError($error);
			}
		}
	}
}
