<?php
namespace NorthslopePL\Workflow;

interface WorkflowTransition
{
	/**
	 * @return string
	 */
	public function getSourceStateId();

	/**
	 * @return string
	 */
	public function getDestinationStateId();

	/**
	 * Events that trigger this transition
	 *
	 * @return string[]
	 */
	public function getEventNames();

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
	public function checkGuardCondition($context);

	/**
	 * Code to run when this transition is triggered.
	 *
	 * @param WorkflowContext $context
	 *
	 * @return void
	 *
	 * @Workflow-Action None
	 */
	public function run($context);

}
