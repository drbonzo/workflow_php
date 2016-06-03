<?php
namespace NorthslopePL\Workflow;

trait AbstractWorkflowContext
{
	/**
	 * @var string
	 */
	protected $currentStateId = null;

	/**
	 * @var array|mixed[]
	 */
	protected $values = [];

	/**
	 * @return string
	 */
	public function getCurrentStateId()
	{
		return $this->currentStateId;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function getValue($name)
	{
		return isset($this->values[$name]) ? $this->values[$name] : null;
	}

	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setValue($name, $value)
	{
		$this->values[$name] = $value;
	}


	/**
	 * @param string $name
	 */
	public function unsetValue($name)
	{
		if (isset($this->values[$name])) {
			unset($this->values[$name]);
		}
	}

	/**
	 * @return string[]
	 */
	public function getStateHistory()
	{
		return [];
	}
}
