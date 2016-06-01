<?php
namespace Tests\NorthslopePL\Workflow;

use NorthslopePL\Workflow\Exceptions\WorkflowLogicException;
use NorthslopePL\Workflow\WorkflowContext;
use NorthslopePL\Workflow\WorkflowContextCollection;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\DefaultWorkflowContext;

class WorkflowContextCollectionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var WorkflowContextCollection
	 */
	private $workflowContextCollection;

	/**
	 * @var WorkflowContext
	 */
	private $contextA;

	protected function setUp()
	{
		$this->contextA = new DefaultWorkflowContext();
		$this->workflowContextCollection = new WorkflowContextCollection();
		$this->workflowContextCollection->addContext('Workflow_A', $this->contextA);
	}

	public function testYouCanRetrieveExistingContext()
	{
		$this->assertSame($this->contextA, $this->workflowContextCollection->getContext('Workflow_A'));
	}

	public function testRetrivingNotExistingContextThrowsError()
	{
		$this->setExpectedExceptionRegExp(WorkflowLogicException::class, '#WorkflowContext not found for key "Workflow_INVALID"#');
		$this->workflowContextCollection->getContext('Workflow_INVALID');
	}
}
