<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use NorthslopePL\Workflow\WorkflowContext;

class ExampleWorkflowStateWithEventsAndActions extends ExampleWorkflowState
{
	/**
	 * @return string[]
	 */
	public function getOnEnterEvents()
	{
		return $this->onEnterEvents;
	}

	/**
	 * @param string[] $onEnterEvents
	 */
	public function setOnEnterEvents($onEnterEvents)
	{
		$this->onEnterEvents = $onEnterEvents;
	}

	/**
	 * @return string[]
	 */
	public function getOnExitEvents()
	{
		return $this->onExitEvents;
	}

	/**
	 * @param string[] $onExitEvents
	 */
	public function setOnExitEvents($onExitEvents)
	{
		$this->onExitEvents = $onExitEvents;
	}

	/**
	 * @param WorkflowContext $context
	 *
	 * @return void
	 *
	 * @Workflow-Action Do some work
	 */
	public function onEnterAction($context)
	{
	}

	/**
	 * @param WorkflowContext $context
	 *
	 * @return void
	 *
	 * @Workflow-Action Do other work
	 */
	public function onExitAction($context)
	{
	}


}
