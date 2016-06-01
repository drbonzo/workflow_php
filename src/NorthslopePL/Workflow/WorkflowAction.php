<?php
namespace NorthslopePL\Workflow;

interface WorkflowAction
{
	/**
	 * @return string[]
	 */
	public function getAllowedWorkflowStateIds();
}
