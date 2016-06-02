<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Validator\WorkflowValidationError;
use PHPUnit_Framework_TestCase;
use stdClass;

class WorkflowValidationErrorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var stdClass
	 */
	private $object;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var WorkflowValidationError
	 */
	private $error;

	protected function setUp()
	{
		$this->object = new stdClass();
		$this->message = 'Something has failed';
		$this->error = new WorkflowValidationError($this->object, $this->message);
	}

	public function testGetObject()
	{
		$this->assertSame($this->object, $this->error->getObject());
	}

	public function testSetObject()
	{
		$otherObject = new stdClass();

		$this->error->setObject($otherObject);
		$this->assertSame($otherObject, $this->error->getObject());
	}

	public function testGetMessage()
	{
		$this->assertSame($this->message, $this->error->getMessage());
	}

	public function testSetMessage()
	{
		$otherMessage = 'Other message';
		$this->error->setMessage($otherMessage);

		$this->assertSame($otherMessage, $this->error->getMessage());
	}

	public function testDefaultErrorType()
	{
		$this->assertSame(WorkflowValidationError::ERROR_TYPE, $this->error->getErrorType());
	}

	public function testChangingErrorType()
	{
		$this->error->setErrorType(WorkflowValidationError::WARNING_TYPE);
		$this->assertSame(WorkflowValidationError::WARNING_TYPE, $this->error->getErrorType());

		$this->error->setErrorType(WorkflowValidationError::ERROR_TYPE);
		$this->assertSame(WorkflowValidationError::ERROR_TYPE, $this->error->getErrorType());
	}

	public function testToString()
	{
		$expected = 'error in stdClass: Something has failed';
		$this->assertEquals($expected, $this->error->__toString());
	}
}
