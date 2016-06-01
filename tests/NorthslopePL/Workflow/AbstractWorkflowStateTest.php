<?php
namespace Tests\NorthslopePL\Workflow;

use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\DefaultWorkflowState;

class AbstractWorkflowStateTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var DefaultWorkflowState
	 */
	private $workflowState;

	protected function setUp()
	{
		$this->workflowState = new DefaultWorkflowState();
	}

	public function testIsFinal()
	{
		$this->assertFalse($this->workflowState->isFinal());
	}

	public function testGetOnEnterEvents()
	{
		$this->assertSame([], $this->workflowState->getOnEnterEvents());
	}

	public function testGetOnExitEvents()
	{
		$this->assertSame([], $this->workflowState->getOnExitEvents());
	}
}
