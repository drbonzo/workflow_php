<?php
namespace NorthslopePL\Workflow\Validator;

class WorkflowValidationError
{
	const ERROR_TYPE = 'error';
	const WARNING_TYPE = 'warning';

	/**
	 * WorkflowState, WorkflowTransition, Workflow
	 *
	 * @var object
	 */
	private $object;

	/**
	 * @see self::*_TYPE
	 * @var string
	 */
	private $errorType;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * WorkflowValidationError constructor.
	 * @param object|mixed $object
	 * @param string $message
	 */
	public function __construct($object, $message)
	{
		$this->object = $object;
		$this->message = $message;
		$this->errorType = self::ERROR_TYPE;
	}

	/**
	 * @return object
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * @param object|mixed $object
	 */
	public function setObject($object)
	{
		$this->object = $object;
	}

	/**
	 * @return string
	 */
	public function getErrorType()
	{
		return $this->errorType;
	}

	/**
	 * @param string $errorType
	 */
	public function setErrorType($errorType)
	{
		$this->errorType = $errorType;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function __toString()
	{
		return sprintf('%s in %s: %s', $this->errorType, get_class($this->object), $this->message);
	}
}
