<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use NorthslopePL\Workflow\AbstractWorkflowTransition;
use NorthslopePL\Workflow\WorkflowTransition;

class DefaultWorkflowTransition implements WorkflowTransition
{
	use AbstractWorkflowTransition;

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function getSourceStateId()
	{
		return 'some_state_id';
	}

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function getDestinationStateId()
	{
		return 'some_other_state_id';
	}

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function getEventNames()
	{
		return [];
	}

}
