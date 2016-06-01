<?php
namespace NorthslopePL\Workflow;

/**
 * Use this trait in your class to simplify it's code by using default configuration.
 */
trait AbstractWorkflowState
{
	/**
	 * @return bool
	 * @see WorkflowState::isFinal()
	 */
	public function isFinal()
	{
		return false;
	}

	/**
	 * @inheritdoc
	 * @see WorkflowState::onEnterAction()
	 * @Workflow-Action None
	 */
	public function onEnterAction($context)
	{
		// by default does nothing
	}

	/**
	 * @inheritdoc
	 * @see WorkflowState::onExitAction()
	 * @Workflow-Action None
	 */
	public function onExitAction($context)
	{
		// by default does nothing
	}

	/**
	 * @return string[]
	 *
	 * @see WorkflowState::getOnEnterEvents()
	 */
	public function getOnEnterEvents()
	{
		return []; // by default no events
	}

	/**
	 * @return string[]
	 *
	 * @see WorkflowState::getOnExitEvents()
	 */
	public function getOnExitEvents()
	{
		return []; // by default no events
	}

}
