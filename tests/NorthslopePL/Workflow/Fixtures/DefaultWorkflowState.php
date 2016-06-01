<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use NorthslopePL\Workflow\AbstractWorkflowState;
use NorthslopePL\Workflow\WorkflowState;

class DefaultWorkflowState implements WorkflowState
{
	public function getStateId()
	{
		return 'foo_state';
	}

	use AbstractWorkflowState;
}
