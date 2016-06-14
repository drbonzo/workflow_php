<?php
namespace Tests\NorthslopePL\Workflow;

use NorthslopePL\Workflow\WorkflowBuilder;
use NorthslopePL\Workflow\WorkflowBuilderDelegate;
use NorthslopePL\Workflow\WorkflowBuilderManager;
use NorthslopePL\Workflow\WorkflowContext;
use NorthslopePL\Workflow\WorkflowContextCollection;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\DefaultWorkflowContext;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflow_1;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflow_2;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflow_3;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowBuilder;

class WorkflowBuilderManagerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var WorkflowBuilderDelegate|PHPUnit_Framework_MockObject_MockObject
	 */
	private $workflowBuilderDelegate;

	/**
	 * @var ExampleWorkflow_1
	 */
	private $workflow_1;

	/**
	 * @var ExampleWorkflow_2
	 */
	private $workflow_2;

	/**
	 * @var ExampleWorkflow_3
	 */
	private $workflow_3;

	/**
	 * @var WorkflowBuilder
	 */
	private $workflowBuilder_1;

	/**
	 * @var WorkflowBuilder
	 */
	private $workflowBuilder_2;

	/**
	 * @var WorkflowBuilder
	 */
	private $workflowBuilder_3;

	/**
	 * @var WorkflowContext
	 */
	private $workflowContext_1;

	/**
	 * @var WorkflowContext
	 */
	private $workflowContext_2;

	/**
	 * @var WorkflowContext
	 */
	private $workflowContext_3;

	public function setUp()
	{
		$this->workflow_1 = new ExampleWorkflow_1();
		$this->workflowBuilder_1 = new ExampleWorkflowBuilder($this->workflow_1);

		$this->workflow_2 = new ExampleWorkflow_2();
		$this->workflowBuilder_2 = new ExampleWorkflowBuilder($this->workflow_2);

		$this->workflow_3 = new ExampleWorkflow_3();
		$this->workflowBuilder_3 = new ExampleWorkflowBuilder($this->workflow_3);

		$this->workflowContext_1 = new DefaultWorkflowContext();
		$this->workflowContext_2 = new DefaultWorkflowContext();
		$this->workflowContext_3 = new DefaultWorkflowContext();

		//

		$this->workflowBuilderDelegate = $this->getMockBuilder(WorkflowBuilderDelegate::class)
			->enableOriginalConstructor()
			->setMethods(['getWorkflowBuilders', 'getAllWorkflowBuilders', 'getWorkflowContextCollection'])
			->getMock();

		//

		$this->workflowBuilderDelegate->method('getWorkflowBuilders')->willReturn([$this->workflowBuilder_1, $this->workflowBuilder_2]);

		$this->workflowBuilderDelegate->method('getAllWorkflowBuilders')->willReturn([$this->workflowBuilder_1, $this->workflowBuilder_2, $this->workflowBuilder_3]);

		$contextCollection = new WorkflowContextCollection();
		$contextCollection->addContext(ExampleWorkflow_1::class, $this->workflowContext_1);
		$contextCollection->addContext(ExampleWorkflow_2::class, $this->workflowContext_2);
		$this->workflowBuilderDelegate->method('getWorkflowContextCollection')->willReturn($contextCollection);
	}

	public function testBuildWorkflowCollection()
	{
		$workflowBuilderManager = new WorkflowBuilderManager($this->workflowBuilderDelegate);

		$this->workflowBuilderDelegate->expects($this->once())->method('getWorkflowBuilders');
		$workflowCollection = $workflowBuilderManager->buildWorkflowCollection();

		$this->assertEquals([$this->workflow_1, $this->workflow_2], $workflowCollection->getWorkflows());
	}

	public function testBuildAllWorkflowCollection()
	{
		$workflowBuilderManager = new WorkflowBuilderManager($this->workflowBuilderDelegate);

		$this->workflowBuilderDelegate->expects($this->once())->method('getAllWorkflowBuilders');
		$workflowCollection = $workflowBuilderManager->buildAllWorkflowCollection();

		$this->assertEquals([$this->workflow_1, $this->workflow_2, $this->workflow_3], $workflowCollection->getWorkflows());
	}

	public function testBuildContextCollection()
	{
		$workflowBuilderManager = new WorkflowBuilderManager($this->workflowBuilderDelegate);

		$this->workflowBuilderDelegate->expects($this->once())->method('getWorkflowContextCollection');
		$workflowContextCollection = $workflowBuilderManager->buildWorkflowContextCollection();

		$expectedContextCollection = new WorkflowContextCollection();
		$expectedContextCollection->addContext(ExampleWorkflow_1::class, $this->workflowContext_1);
		$expectedContextCollection->addContext(ExampleWorkflow_2::class, $this->workflowContext_2);

		$this->assertEquals($expectedContextCollection, $workflowContextCollection);
	}
}
