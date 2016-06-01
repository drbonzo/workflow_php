<?php
namespace NorthslopePL\Workflow;

interface WorkflowBuilder
{
	/**
	 * @return Workflow
	 */
	public function buildWorkflow();
}
