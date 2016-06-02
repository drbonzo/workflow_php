<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Workflow;
use NorthslopePL\Workflow\WorkflowCollection;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflow_1;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflow_2;

class WorkflowCollectionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var WorkflowCollection
	 */
	private $workflowCollection;

	/**
	 * @var Workflow
	 */
	private $workflow_1;

	/**
	 * @var Workflow
	 */
	private $workflow_2;

	protected function setUp()
	{
		$this->workflowCollection = new WorkflowCollection();
		$this->workflow_1 = new ExampleWorkflow_1();
		$this->workflow_2 = new ExampleWorkflow_2();
	}

	public function testByDefaultHasNoWorkflows()
	{
		$this->assertEquals([], $this->workflowCollection->getWorkflows());
	}

	public function testSettingWorkflows()
	{
		$this->workflowCollection->setWorkflows([$this->workflow_1, $this->workflow_2]);
		$this->assertEquals([$this->workflow_1, $this->workflow_2], $this->workflowCollection->getWorkflows());
	}

	public function testAddingWorkflows()
	{
		$this->workflowCollection->addWorkflow($this->workflow_1);
		$this->assertEquals([$this->workflow_1], $this->workflowCollection->getWorkflows());

		$this->workflowCollection->addWorkflow($this->workflow_2);
		$this->assertEquals([$this->workflow_1, $this->workflow_2], $this->workflowCollection->getWorkflows());
	}
}
