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

	/**
	 * Saves changed in object covered by Workflow.
	 * May be called many times during on call to $workflowMachine->execute()
	 * 
	 * @return void
	 */
	public function commit();

}
