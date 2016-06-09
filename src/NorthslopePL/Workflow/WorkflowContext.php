<?php
namespace NorthslopePL\Workflow;

use DateTime;

interface WorkflowContext
{
	/**
	 * @return string
	 */
	public function getCurrentStateId();

	/**
	 * @param string $stateId
	 */
	public function setCurrentStateId($stateId);

	/**
	 * @return DateTime
	 */
	public function getLastStateChangedAt();

}
