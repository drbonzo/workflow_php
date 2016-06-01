<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\AbstractWorkflowState;
use NorthslopePL\Workflow\WorkflowState;

class StateWithoutPHPDoc implements WorkflowState
{
	use AbstractWorkflowState;

	public function getStateId()
	{
		return 'state_A';
	}

	/**
	 * @param \NorthslopePL\Workflow\WorkflowContext $context
	 */
	public function onEnterAction($context)
	{
	}

	/**
	 * @param \NorthslopePL\Workflow\WorkflowContext $context
	 */
	public function onExitAction($context)
	{
	}

}
