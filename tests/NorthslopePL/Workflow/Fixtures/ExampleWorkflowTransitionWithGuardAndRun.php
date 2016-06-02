<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use NorthslopePL\Workflow\WorkflowContext;

class ExampleWorkflowTransitionWithGuardAndRun extends ExampleWorkflowTransition
{
	/**
	 * @param WorkflowContext $context
	 * @return boolean
	 * @Workflow-Guard Do some condition check
	 */
	public function checkGuardCondition($context)
	{
		return true;
	}

	/**
	 * @param WorkflowContext $context
	 * @return void
	 * @Workflow-Action Do some work during transition
	 */
	public function run($context)
	{
	}

}
