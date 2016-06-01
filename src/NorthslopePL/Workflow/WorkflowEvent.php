<?php
namespace NorthslopePL\Workflow;

use Symfony\Component\EventDispatcher\Event;

class WorkflowEvent extends Event
{
	/**
	 * @var WorkflowContext
	 */
	private $context;

	public function __construct(WorkflowContext $context)
	{
		$this->context = $context;
	}

	/**
	 * @return WorkflowContext
	 */
	public function getContext()
	{
		return $this->context;
	}

}
