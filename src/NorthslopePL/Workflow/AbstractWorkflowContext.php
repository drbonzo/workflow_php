<?php
namespace NorthslopePL\Workflow;

trait AbstractWorkflowContext
{
	/**
	 * @var array|mixed[]
	 */
	protected $values = [];

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
}
