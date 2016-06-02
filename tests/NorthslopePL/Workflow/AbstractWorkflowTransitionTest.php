<?php
namespace Tests\NorthslopePL\Workflow;

use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\DefaultWorkflowTransition;

class AbstractWorkflowTransitionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var DefaultWorkflowTransition
	 */
	private $workflowTransition;

	protected function setUp()
	{
		$this->workflowTransition = new DefaultWorkflowTransition();
	}

	public function testStartsFromAnyStateId()
	{
		$this->assertFalse($this->workflowTransition->startsFromAnyStateId());
	}

	public function testCheckGuardCondition()
	{
		$this->assertTrue($this->workflowTransition->checkGuardCondition(null));
	}
}
