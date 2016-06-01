<?php
namespace NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Workflow;

class WorkflowValidatorCollection implements WorkflowValidator
{
	/**
	 * @var WorkflowValidator[]
	 */
	private $validators = [];

	public function __construct()
	{
		$this->validators = [
			new InitialStateValidator(),
			new StateValidator(),
			new TransitionValidator(),
			new PHPDocValidator(),
		];
	}

	/**
	 * @param Workflow $workflow
	 * @return WorkflowValidationResult
	 */
	public function validate(Workflow $workflow)
	{
		$result = new WorkflowValidationResult();

		foreach ($this->validators as $validator) {
			$newErrors = $validator->validate($workflow);
			$result->addAllValidationErrors($newErrors->getValidationErrors());
		}

		return $result;
	}
}
