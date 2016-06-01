<?php
namespace NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Workflow;

interface WorkflowValidator
{
	/**
	 * @param Workflow $workflow
	 *
	 * @return WorkflowValidationResult
	 */
	public function validate(Workflow $workflow);

}
