<?php
namespace Tests\NorthslopePL\Workflow;

use NorthslopePL\Workflow\Exceptions\WorkflowLogicException;
use NorthslopePL\Workflow\Workflow;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Validator\StateForValidationTesting;
use Tests\NorthslopePL\Workflow\Validator\WorkflowForValidationTesting;

class WorkflowTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Workflow
	 */
	private $workflow;

	protected function setUp()
	{
		$this->workflow = new WorkflowForValidationTesting();
	}

	public function testGetStateForStateIdForExistingState()
	{
		$state_A = new StateForValidationTesting('state_A');
		$state_B = new StateForValidationTesting('state_B');
		$state_C = new StateForValidationTesting('state_C');

		$this->workflow->setStates([$state_A, $state_B, $state_C]);

		$this->assertSame($state_A, $this->workflow->getStateForStateId('state_A'));
		$this->assertSame($state_B, $this->workflow->getStateForStateId('state_B'));
		$this->assertSame($state_C, $this->workflow->getStateForStateId('state_C'));
	}

	public function testGetStateForStateIdForNotExistingState()
	{
		$state_A = new StateForValidationTesting('state_A');
		$state_B = new StateForValidationTesting('state_B');
		$state_C = new StateForValidationTesting('state_C');

		$this->workflow->setStates([$state_A, $state_B, $state_C]);

		$this->setExpectedExceptionRegExp(WorkflowLogicException::class, '#WorkflowState not found for stateId: "INVALID"#');
		$this->workflow->getStateForStateId('INVALID');
	}
}
