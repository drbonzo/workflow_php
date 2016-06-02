<?php
namespace NorthslopePL\Workflow;

class WorkflowBuilderManager
{
	/**
	 * @var WorkflowBuilderDelegate
	 */
	private $workflowBuilderDelegate;

	public function __construct(WorkflowBuilderDelegate $workflowBuilderDelegate)
	{
		$this->workflowBuilderDelegate = $workflowBuilderDelegate;
	}

	/**
	 * @return WorkflowCollection
	 */
	public function buildWorkflowCollection()
	{
		$workflowBuilders = $this->workflowBuilderDelegate->getWorkflowBuilders();

		return $this->buildWorkflowCollectionFromWorkflowBuilders($workflowBuilders);
	}

	/**
	 * @return WorkflowCollection
	 */
	public function buildAllWorkflowCollection()
	{
		$workflowBuilders = $this->workflowBuilderDelegate->getAllWorkflowBuilders();

		return $this->buildWorkflowCollectionFromWorkflowBuilders($workflowBuilders);
	}

	/**
	 * @param WorkflowBuilder[] $workflowBuilders
	 *
	 * @return WorkflowCollection
	 */
	private function buildWorkflowCollectionFromWorkflowBuilders($workflowBuilders)
	{
		$workflowCollection = new WorkflowCollection();

		foreach ($workflowBuilders as $workflowBuilder) {
			$workflow = $workflowBuilder->buildWorkflow();
			$workflowCollection->addWorkflow($workflow);
		}

		return $workflowCollection;
	}

	/**
	 * @return WorkflowContextCollection
	 */
	public function buildWorkflowContextCollection()
	{
		return $this->workflowBuilderDelegate->getWorkflowContextCollection();
	}

}
