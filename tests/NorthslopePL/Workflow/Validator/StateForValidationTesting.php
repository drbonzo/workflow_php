<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\AbstractWorkflowState;
use NorthslopePL\Workflow\WorkflowState;

class StateForValidationTesting implements WorkflowState
{
	use AbstractWorkflowState;

	/**
	 * @var string
	 */
	private $stateId;

	/**
	 * @var boolean
	 */
	private $final;

	/**
	 * @var string[]
	 */
	private $onEnterEvents = [];

	/**
	 * @var string[]
	 */
	private $onExitEvents = [];

	public function __construct($stateId)
	{
		$this->stateId = $stateId;
		$this->final = false;
	}

	public function getStateId()
	{
		return $this->stateId;
	}

	/**
	 * @return boolean
	 */
	public function isFinal()
	{
		return $this->final;
	}

	/**
	 * @param boolean $final
	 */
	public function setFinal($final)
	{
		$this->final = $final;
	}

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

}
