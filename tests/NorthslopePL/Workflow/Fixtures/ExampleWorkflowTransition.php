<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use NorthslopePL\Workflow\WorkflowTransition;

class ExampleWorkflowTransition implements WorkflowTransition
{
	/**
	 * @var string
	 */
	private $sourceStateId;

	/**
	 * @var string
	 */
	private $destinationStateId;

	/**
	 * @var bool
	 */
	private $startsFromAnyStateId = false;

	/**
	 * @var string[]
	 */
	private $eventNames = [];

	public function __construct($sourceStateId, $destinationStateId)
	{
		$this->sourceStateId = $sourceStateId;
		$this->destinationStateId = $destinationStateId;
	}

	public function getSourceStateId()
	{
		return $this->sourceStateId;
	}

	/**
	 * @return boolean
	 */
	public function startsFromAnyStateId()
	{
		return $this->startsFromAnyStateId;
	}

	/**
	 * @param boolean $startsFromAnyStateId
	 */
	public function setStartsFromAnyStateId($startsFromAnyStateId)
	{
		$this->startsFromAnyStateId = $startsFromAnyStateId;
	}

	public function getDestinationStateId()
	{
		return $this->destinationStateId;
	}

	/**
	 * @param string[] $eventNames
	 */
	public function setEventNames($eventNames)
	{
		$this->eventNames = $eventNames;
	}

	public function getEventNames()
	{
		return $this->eventNames;
	}

	public function checkGuardCondition($context)
	{
		return true;
	}

	public function run($context)
	{
	}

}
