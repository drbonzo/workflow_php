<?php
namespace Tests\NorthslopePL\Workflow;

use NorthslopePL\Workflow\WorkflowEvent;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\DefaultWorkflowContext;

class WorkflowEventTest extends PHPUnit_Framework_TestCase
{
	public function test()
	{
		$context = new DefaultWorkflowContext();
		$event = new WorkflowEvent($context);
		$this->assertSame($context, $event->getContext());
	}
}
