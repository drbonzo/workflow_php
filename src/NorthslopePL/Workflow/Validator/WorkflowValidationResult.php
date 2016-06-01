<?php
namespace NorthslopePL\Workflow\Validator;

class WorkflowValidationResult
{
	/**
	 * @var WorkflowValidationError[]
	 */
	private $validationErrors = [];

	/**
	 * @return boolean
	 */
	public function isValid()
	{
		$isValid = empty($this->validationErrors);
		return $isValid;
	}

	/**
	 * @return WorkflowValidationError[]
	 */
	public function getValidationErrors()
	{
		return $this->validationErrors;
	}

	/**
	 * @param WorkflowValidationError[] $validationErrors
	 */
	public function setValidationErrors($validationErrors)
	{
		$this->validationErrors = $validationErrors;
	}

	/**
	 * @param WorkflowValidationError $validationError
	 */
	public function addValidationError(WorkflowValidationError $validationError)
	{
		$this->validationErrors[] = $validationError;
	}

	/**
	 * @param WorkflowValidationError[] $validationErrors
	 */
	public function addAllValidationErrors($validationErrors)
	{
		foreach ($validationErrors as $validationError) {
			$this->addValidationError($validationError);
		}
	}
}
