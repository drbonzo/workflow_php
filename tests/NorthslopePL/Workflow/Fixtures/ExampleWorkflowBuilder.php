<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use NorthslopePL\Workflow\Workflow;
use NorthslopePL\Workflow\WorkflowBuilder;

class ExampleWorkflowBuilder implements WorkflowBuilder
{
	/**
	 * @var Workflow
	 */
	private $workflow;

	public function __construct(Workflow $workflow)
	{
		$this->workflow = $workflow;
	}

	public function buildWorkflow()
	{
		return $this->workflow;
	}

}
