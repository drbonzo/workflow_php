<?php
namespace Tests\NorthslopePL\Workflow;

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
}
