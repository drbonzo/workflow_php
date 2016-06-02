<?php
namespace Tests\NorthslopePL\Workflow;

use DateTime;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\DefaultWorkflowContext;

class AbstractWorkflowContextTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var DefaultWorkflowContext
	 */
	private $workflowContext;

	protected function setUp()
	{
		$this->workflowContext = new DefaultWorkflowContext();
	}

	public function testCurrentStateId()
	{
		$this->assertNull($this->workflowContext->getCurrentStateId());

		$stateId = 'foo_1';
		$this->workflowContext->setCurrentStateId($stateId);

		$this->assertSame($stateId, $this->workflowContext->getCurrentStateId());
	}

	public function testGetLastStateChangedAt()
	{
		$this->assertNull($this->workflowContext->getLastStateChangedAt());

		$dateTime = new DateTime();
		$this->workflowContext->setLastStateChangedAt($dateTime);

		$this->assertEquals($dateTime, $this->workflowContext->getLastStateChangedAt());
	}

	public function testValues()
	{
		$this->assertNull($this->workflowContext->getValue('foo'));

		$this->workflowContext->setValue('foo', 'bar');
		$this->assertSame('bar', $this->workflowContext->getValue('foo'));

		$this->workflowContext->unsetValue('foo');
		$this->assertNull($this->workflowContext->getValue('foo'));

		$this->workflowContext->unsetValue('foo');
		$this->assertNull($this->workflowContext->getValue('foo'));
	}

	public function testGetStateHistory()
	{
		$this->assertSame([], $this->workflowContext->getStateHistory());
	}
}
