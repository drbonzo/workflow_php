<?php
namespace NorthslopePL\Workflow;

/**
 * Use this trait in your class to simplify it's code by using default configuration.
 */
trait AbstractWorkflowTransition
{
	public function startsFromAnyStateId()
	{
		return false;
	}

	/**
	 * @param WorkflowContext $context
	 *
	 * @return boolean
	 *
	 * true - guard condition is met - we can run this transition
	 * false - transition will not be run
	 *
	 * @Workflow-Guard None
	 */
	public function checkGuardCondition($context)
	{
		// no guard check, by default
		return true;
	}

	/**
	 * Code to run when this transition is triggered.
	 *
	 * @param WorkflowContext $context
	 *
	 * @return void
	 *
	 * @Workflow-Action None
	 *
	 * @codeCoverageIgnore
	 */
	public function run($context)
	{
	}
}
