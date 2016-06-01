<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\WorkflowContext;
use NorthslopePL\Workflow\WorkflowTransition;

class TransitionWithoutPHPDoc implements WorkflowTransition
{
	/**
	 * @var string
	 */
	private $destinationStateId;

	/**
	 * @var string
	 */
	private $sourceStateId;

	/**
	 * @var string[]
	 */
	private $eventNames;

	public function __construct($sourceStateId, $destinationStateId, $eventNames = [])
	{
		$this->destinationStateId = $destinationStateId;
		$this->sourceStateId = $sourceStateId;
		$this->eventNames = $eventNames;
	}

	public function getSourceStateId()
	{
		return $this->sourceStateId;
	}

	public function getDestinationStateId()
	{
		return $this->destinationStateId;
	}

	public function getEventNames()
	{
		return $this->eventNames;
	}

	/**
	 * @param WorkflowContext $context
	 * @return boolean
	 */
	public function checkGuardCondition($context)
	{
		return true;
	}

	/**
	 * @param WorkflowContext $context
	 */
	public function run($context)
	{
	}

}
