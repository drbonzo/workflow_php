<?php
namespace NorthslopePL\Workflow;

interface WorkflowState
{
	/**
	 * @return string
	 */
	public function getStateId();

	/**
	 * This must return true if your State is a final state.
	 * If not, and this state connects to no other state - then validator will raise an error.
	 *
	 * Example:
	 *
	 * A -> B
	 *      B -> C [final=true]
	 *      B -> D [final=false]
	 *      B -> E no need, 'E' has outgoing connections
	 *           E -> C
	 *
	 * C is correct - C has no outgoing and is marked as final
	 * D is incorrect - as final == false, and D has no outgoing connections to other state
	 *
	 * @return bool
	 */
	public function isFinal();

	/**
	 * @param WorkflowContext $context
	 *
	 * @return void
	 *
	 * @Workflow-Action None
	 */
	public function onEnterAction($context);

	/**
	 * @param WorkflowContext $context
	 *
	 * @return void
	 *
	 * @Workflow-Action None
	 */
	public function onExitAction($context);

	/**
	 * @return string[]
	 */
	public function getOnEnterEvents();

	/**
	 * @return string[]
	 */
	public function getOnExitEvents();

}
