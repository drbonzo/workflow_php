<?php
namespace NorthslopePL\Workflow;

class WorkflowCollection
{
	/**
	 * @var Workflow[]
	 */
	private $workflows = [];

	/**
	 * @return Workflow[]
	 */
	public function getWorkflows()
	{
		return $this->workflows;
	}

	/**
	 * @param Workflow[] $workflows
	 */
	public function setWorkflows($workflows)
	{
		$this->workflows = $workflows;
	}

	/**
	 * @param Workflow $workflow
	 */
	public function addWorkflow(Workflow $workflow)
	{
		$this->workflows[] = $workflow;
	}

}
