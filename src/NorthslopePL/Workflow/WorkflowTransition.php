<?php
namespace NorthslopePL\Workflow;

interface WorkflowTransition
{
	/**
	 * @see startsFromAnyStateId()
	 * @var string
	 */
	const __ANY_STATE = '__ANY_STATE';

	/**
	 * @return string
	 */
	public function getSourceStateId();

	/**
	 * if true then
	 * - this transition starts from any state
	 * - set getSourceStateId() to return WorkflowTransition::__ANY_STATE
	 * - getEventNames() MUST return at least one event name
	 *
	 * @return boolean
	 */
	public function startsFromAnyStateId();

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
