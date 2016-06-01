<?php
namespace NorthslopePL\Workflow;

interface WorkflowBuilderDelegate
{
	/**
	 * @return WorkflowBuilder[]
	 */
	public function getWorkflowBuilders();

	/**
	 * @return WorkflowBuilder[]
	 */
	public function getAllWorkflowBuilders();

	/**
	 * @return WorkflowContextCollection
	 */
	public function getWorkflowContextCollection();
}
