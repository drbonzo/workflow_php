<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use DateTime;
use NorthslopePL\Workflow\AbstractWorkflowContext;
use NorthslopePL\Workflow\WorkflowContext;

class DefaultWorkflowContext implements WorkflowContext
{
	use AbstractWorkflowContext;

	public function setCurrentStateId($stateId)
	{
		$this->currentStateId = $stateId;
	}

	public function setLastStateChangedAt(DateTime $dateTime)
	{
		$this->lastStateChangedAt = $dateTime;
	}

	/**
	 * @return DateTime
	 */
	public function getLastStateChangedAt()
	{
		return new DateTime();
	}

}
