<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use NorthslopePL\Workflow\AbstractWorkflowContext;
use NorthslopePL\Workflow\WorkflowContext;

class DefaultWorkflowContext implements WorkflowContext
{
	use AbstractWorkflowContext;

	public function setCurrentStateId($stateId)
	{
		$this->currentStateId = $stateId;
	}

}
