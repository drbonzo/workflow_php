<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\WorkflowTransition;

class TransitionForValidationTesting implements WorkflowTransition
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
	 * @var array
	 */
	private $eventNames = [];

	/**
	 * @var bool
	 */
	private $startsFromAnyStateId = false;

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

	public function checkGuardCondition($context)
	{
		return true;
	}

	public function run($context)
	{
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

}
