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
	 * @param string $name
	 * @return string
	 */
	public function getValue($name);

	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setValue($name, $value);

	/**
	 * @param string $name
	 */
	public function unsetValue($name);

	/**
	 * @return DateTime
	 */
	public function getLastStateChangedAt();

	/**
	 * @return string[]
	 */
	public function getStateHistory();

}
