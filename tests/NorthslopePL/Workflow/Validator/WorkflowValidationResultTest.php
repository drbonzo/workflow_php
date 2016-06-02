<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Validator\WorkflowValidationError;
use NorthslopePL\Workflow\Validator\WorkflowValidationResult;
use PHPUnit_Framework_TestCase;
use stdClass;

class WorkflowValidationResultTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var WorkflowValidationResult
	 */
	private $result;

	/**
	 * @var stdClass
	 */
	private $object;

	/**
	 * @var WorkflowValidationError
	 */
	private $error_1;

	/**
	 * @var WorkflowValidationError
	 */
	private $error_2;

	/**
	 * @var WorkflowValidationError
	 */
	private $error_3;

	/**
	 * @var WorkflowValidationError
	 */
	private $error_4;

	protected function setUp()
	{
		$this->result = new WorkflowValidationResult();
		$this->object = new stdClass();
		$this->error_1 = new WorkflowValidationError($this->object, 'Error message 1');
		$this->error_2 = new WorkflowValidationError($this->object, 'Error message 2');
		$this->error_3 = new WorkflowValidationError($this->object, 'Error message 3');
		$this->error_4 = new WorkflowValidationError($this->object, 'Error message 4');
	}

	public function testByDefaultItIsValid()
	{
		$this->assertTrue($this->result->isValid());
	}

	public function testByDefaultHasNoValidationErrors()
	{
		$this->assertSame([], $this->result->getValidationErrors());
	}

	public function testIfHasValidationErrorsThenIsInvalid()
	{
		$this->result->setValidationErrors(
			[
				$this->error_1,
				$this->error_2,
				$this->error_3,
				$this->error_4
			]
		);

		$this->assertFalse($this->result->isValid());
	}

	public function testSettingsValidationErrors()
	{
		$this->result->setValidationErrors(
			[
				$this->error_1,
				$this->error_2,
				$this->error_3,
				$this->error_4
			]
		);

		$this->assertEquals(
			[
				$this->error_1,
				$this->error_2,
				$this->error_3,
				$this->error_4
			]
			,
			$this->result->getValidationErrors()
		);
	}

	public function testAddingValidationErrors()
	{
		$this->assertCount(0, $this->result->getValidationErrors());

		$this->result->setValidationErrors([$this->error_1]);
		$this->assertCount(1, $this->result->getValidationErrors());

		$this->result->addValidationError($this->error_2);
		$this->assertCount(2, $this->result->getValidationErrors());

		$this->result->addAllValidationErrors([$this->error_3, $this->error_4]);
		$this->assertCount(4, $this->result->getValidationErrors());

		$this->assertEquals(
			[
				$this->error_1,
				$this->error_2,
				$this->error_3,
				$this->error_4
			]
			,
			$this->result->getValidationErrors()
		);
	}

}
