<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Validator\WorkflowValidationError;
use NorthslopePL\Workflow\Validator\WorkflowValidatorCollection;
use NorthslopePL\Workflow\Workflow;
use NorthslopePL\Workflow\WorkflowTransition;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowState;

class WorkflowValidatorForPHPDocWarningsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var WorkflowValidatorCollection
	 */
	private $validator;

	/**
	 * @var Workflow
	 */
	private $workflow;

	/**
	 * @var StateWithoutPHPDoc
	 */
	private $state_A;

	/**
	 * @var ExampleWorkflowState
	 */
	private $state_B;

	/**
	 * @var WorkflowTransition
	 */
	private $transition_A_B;

	protected function setUp()
	{
		$this->validator = new WorkflowValidatorCollection();
		$this->workflow = new WorkflowForValidationTesting();

		$this->state_A = new StateWithoutPHPDoc();
		$this->state_B = new ExampleWorkflowState('state_B');
		$this->state_B->setFinal(true);

		$this->transition_A_B = new TransitionWithoutPHPDoc('state_A', 'state_B');

		$this->workflow->setStates([$this->state_A, $this->state_B]);
		$this->workflow->setInitialState($this->state_A);
		$this->workflow->setTransitions([$this->transition_A_B]);
	}

	public function testWorkflowState_OnEnterAction_and_OnExitAction_ShouldHavePHPDoc()
	{
		$actualResult = $this->validator->validate($this->workflow);
		$actualValidationErrors = $actualResult->getValidationErrors();

		// $checkForObjectIdentity must be false as we have different object instances
		$expectedError_1 = new WorkflowValidationError($this->state_A, 'WorkflowState "state_A" should have docComment with "@Workflow-Action" for onEnterAction() method');
		$expectedError_1->setErrorType(WorkflowValidationError::WARNING_TYPE);

		$expectedError_2 = new WorkflowValidationError($this->state_A, 'WorkflowState "state_A" should have docComment with "@Workflow-Action" for onExitAction() method');
		$expectedError_2->setErrorType(WorkflowValidationError::WARNING_TYPE);

		$this->assertContains($expectedError_1, $actualValidationErrors, '', false, false);
		$this->assertContains($expectedError_2, $actualValidationErrors, '', false, false);
	}

	public function testWorkflowTransition_checkGuardCondition_ShouldHavePHPDoc()
	{
		$actualResult = $this->validator->validate($this->workflow);
		$actualValidationErrors = $actualResult->getValidationErrors();

		// $checkForObjectIdentity must be false as we have different object instances
		$expectedError_1 = new WorkflowValidationError($this->transition_A_B, 'WorkflowTransition Tests\\NorthslopePL\\Workflow\\Validator\\TransitionWithoutPHPDoc ("state_A" => "state_B" should have docComment with "@Workflow-Guard" for checkGuardCondition() method');
		$expectedError_1->setErrorType(WorkflowValidationError::WARNING_TYPE);

		$this->assertContains($expectedError_1, $actualValidationErrors, '', false, false);
	}

	public function testWorkflowTransition_run_ShouldHavePHPDoc()
	{
		$actualResult = $this->validator->validate($this->workflow);
		$actualValidationErrors = $actualResult->getValidationErrors();

		// $checkForObjectIdentity must be false as we have different object instances
		$expectedError_1 = new WorkflowValidationError($this->transition_A_B, 'WorkflowTransition Tests\NorthslopePL\Workflow\Validator\TransitionWithoutPHPDoc ("state_A" => "state_B" should have docComment with "@Workflow-Action" for run() method');
		$expectedError_1->setErrorType(WorkflowValidationError::WARNING_TYPE);

		$this->assertContains($expectedError_1, $actualValidationErrors, '', false, false);
	}
}
