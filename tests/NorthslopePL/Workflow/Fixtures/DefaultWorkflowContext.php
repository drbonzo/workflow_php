<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use DateTime;
use NorthslopePL\Workflow\WorkflowContext;

class DefaultWorkflowContext implements WorkflowContext
{
	/**
	 * @var string
	 */
	private $currentStateId = null;

	/**
	 * @var DateTime
	 */
	private $lastStateChangedAt = null;

	/**
	 * @return string
	 */
	public function getCurrentStateId()
	{
		return $this->currentStateId;
	}

	/**
	 * @param string $currentStateId
	 */
	public function setCurrentStateId($currentStateId)
	{
		$this->currentStateId = $currentStateId;
	}

	/**
	 * @return DateTime
	 */
	public function getLastStateChangedAt()
	{
		return $this->lastStateChangedAt;
	}

	/**
	 * @param DateTime $lastStateChangedAt
	 */
	public function setLastStateChangedAt($lastStateChangedAt)
	{
		$this->lastStateChangedAt = $lastStateChangedAt;
	}

	public function commit()
	{
	}

}
