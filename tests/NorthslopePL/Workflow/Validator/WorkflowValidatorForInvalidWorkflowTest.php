<?php
namespace Tests\NorthslopePL\Workflow\Validator;

use NorthslopePL\Workflow\Validator\WorkflowValidationError;
use NorthslopePL\Workflow\Validator\WorkflowValidatorCollection;
use NorthslopePL\Workflow\Workflow;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowState;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowTransition;

class WorkflowValidatorForInvalidWorkflowTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var WorkflowValidatorCollection
	 */
	private $validator;

	/**
	 * @var Workflow
	 */
	private $workflow;

	protected function setUp()
	{
		$this->validator = new WorkflowValidatorCollection();
		$this->workflow = new WorkflowForValidationTesting();
	}

	public function testEmptyWorkflow()
	{
		$this->workflow->setStates([]);
		$this->workflow->setTransitions([]);

		$actualResult = $this->validator->validate($this->workflow);

		$expectedErrors = [
			new WorkflowValidationError($this->workflow, 'Workflow must have initialState set'),
			new WorkflowValidationError($this->workflow, 'Workflow must have any states set'),
		];

		$this->assertEquals($expectedErrors, $actualResult->getValidationErrors());
	}

	public function testWorkflowStatesMustHaveUniqueIDs()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$state_C = new ExampleWorkflowState('state_A');
		$state_D = new ExampleWorkflowState('state_A');
		$state_E = new ExampleWorkflowState('state_B');

		$this->workflow->setStates([$state_A, $state_B, $state_C, $state_D, $state_E]);
		$this->workflow->setInitialState($state_A);

		$actualResult = $this->validator->validate($this->workflow);
		$actualValidationErrors = $actualResult->getValidationErrors();
		$this->assertContains(new WorkflowValidationError($state_C, 'WorkflowState at position [2] has duplicated stateId: "state_A"'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_D, 'WorkflowState at position [3] has duplicated stateId: "state_A"'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_E, 'WorkflowState at position [4] has duplicated stateId: "state_B"'), $actualValidationErrors, '', false, false);
	}

	public function testInitialStateMustBeWithinWorkflowStatuses()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$initialState = new ExampleWorkflowState('FOOBAR');

		$this->workflow->setStates([$state_A, $state_B]);
		$this->workflow->setInitialState($initialState);

		$actualResult = $this->validator->validate($this->workflow);
		$actualValidationErrors = $actualResult->getValidationErrors();
		$this->assertContains(new WorkflowValidationError($this->workflow, 'Workflow.initialState "FOOBAR" must be withing Workflow.states'), $actualValidationErrors, '', false, false);
	}

	public function testStateIdMustBeAStringCannotBeEmpty()
	{
		$state_int = new ExampleWorkflowState(1234);
		$state_null = new ExampleWorkflowState(null);
		$state_true = new ExampleWorkflowState(true);
		$state_false = new ExampleWorkflowState(false);
		$state_float = new ExampleWorkflowState(12.34);

		$this->workflow->setStates([$state_int, $state_null, $state_true, $state_false, $state_float]);
		$this->workflow->setInitialState($state_int);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();

		$this->assertContains(new WorkflowValidationError($state_int, 'WorkflowState.stateId must be a string, found: integer, 1234'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_null, 'WorkflowState.stateId must be a string, found: NULL, '), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_true, 'WorkflowState.stateId must be a string, found: boolean, 1'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_false, 'WorkflowState.stateId must be a string, found: boolean, '), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_float, 'WorkflowState.stateId must be a string, found: double, 12.34'), $actualValidationErrors, '', false, false);
	}

	// WORKFLOW WITH MISSING CONNECTIONS FROM INITIAL STATE TO OTHER STATES

	public function testWorkflowWithoutTransitions()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$this->workflow->setStates([$state_A, $state_B]);
		$this->workflow->setInitialState($state_A);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();
		$this->assertContains(new WorkflowValidationError($state_B, 'WorkflowState "state_B" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
	}

	public function testWorkflowWithoutSomeTransitions()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$state_C = new ExampleWorkflowState('state_C');
		$state_D = new ExampleWorkflowState('state_D');
		$this->workflow->setStates(
			[
				$state_A,
				$state_B,
				$state_C,
				$state_D
			]
		);
		$this->workflow->setInitialState($state_A);

		$transition_A_B = new ExampleWorkflowTransition('state_A', 'state_B', null);
		$transition_C_D = new ExampleWorkflowTransition('state_C', 'state_D', null);
		$this->workflow->setTransitions(
			[
				$transition_A_B,
				$transition_C_D,
			]
		);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();
		// $checkForObjectIdentity must be false as we have different object instances
		$this->assertContains(new WorkflowValidationError($state_C, 'WorkflowState "state_C" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_D, 'WorkflowState "state_D" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
	}

	public function testWorkflowWithoutSomeTransitions2()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$state_C = new ExampleWorkflowState('state_C');
		$state_D = new ExampleWorkflowState('state_D');
		$state_E = new ExampleWorkflowState('state_E');
		$this->workflow->setStates(
			[
				$state_A,
				$state_B,
				$state_C,
				$state_D,
				$state_E,
			]
		);
		$this->workflow->setInitialState($state_A);

		$transition_A_B = new ExampleWorkflowTransition('state_A', 'state_B', []);
		$this->workflow->setTransitions(
			[
				$transition_A_B,
			]
		);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();

		// $checkForObjectIdentity must be false as we have different object instances
		$this->assertContains(new WorkflowValidationError($state_C, 'WorkflowState "state_C" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_D, 'WorkflowState "state_D" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_E, 'WorkflowState "state_E" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
	}

	public function testWorkflowWithoutSomeTransitions3()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$state_C = new ExampleWorkflowState('state_C');
		$state_D = new ExampleWorkflowState('state_D');
		$this->workflow->setStates(
			[
				$state_A,
				$state_B,
				$state_C,
				$state_D,
			]
		);
		$this->workflow->setInitialState($state_A);

		$transition_B_C = new ExampleWorkflowTransition('state_B', 'state_C', []);
		$transition_C_D = new ExampleWorkflowTransition('state_C', 'state_D', []);
		$transition_D_B = new ExampleWorkflowTransition('state_D', 'state_B', []);

		$this->workflow->setTransitions(
			[
				$transition_B_C,
				$transition_C_D,
				$transition_D_B,
			]
		);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();

		// $checkForObjectIdentity must be false as we have different object instances
		$this->assertContains(new WorkflowValidationError($state_B, 'WorkflowState "state_B" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_C, 'WorkflowState "state_C" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_D, 'WorkflowState "state_D" is not connected to initialState "state_A"'), $actualValidationErrors, '', false, false);
	}

	// OTHER INVALID WORKFLOWS

	public function testWorkflowWithFinalStatesNotMarkedAsFinalOnes()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$state_C = new ExampleWorkflowState('state_C');
		$state_C->setFinal(true);

		$this->workflow->setStates(
			[
				$state_A,
				$state_B,
				$state_C,
			]
		);
		$this->workflow->setInitialState($state_A);

		$transition_A_B = new ExampleWorkflowTransition('state_A', 'state_B', []);
		$transition_A_C = new ExampleWorkflowTransition('state_A', 'state_C', []);

		$this->workflow->setTransitions(
			[
				$transition_A_B,
				$transition_A_C,
			]
		);

		// B and C are final states, but only C is marked as one
		$this->assertFalse($state_B->isFinal());
		$this->assertTrue($state_C->isFinal());

		//

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();
		// $checkForObjectIdentity must be false as we have different object instances
		$this->assertContains(new WorkflowValidationError($state_B, 'WorkflowState "state_B" is a final state but is not marked as one - make isFinal() return true'), $actualValidationErrors, '', false, false);
	}

	public function testStateMarkedAsFinalCannotHaveOutgoingTransitions()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$state_C = new ExampleWorkflowState('state_C');
		$state_D = new ExampleWorkflowState('state_D');
		$this->workflow->setStates(
			[
				$state_A,
				$state_B,
				$state_C,
				$state_D,
			]
		);
		$this->workflow->setInitialState($state_A);

		$transition_A_B = new ExampleWorkflowTransition('state_A', 'state_B', []);
		$transition_A_C = new ExampleWorkflowTransition('state_A', 'state_C', []);
		$transition_A_D = new ExampleWorkflowTransition('state_A', 'state_D', []);
		$transition_C_D = new ExampleWorkflowTransition('state_C', 'state_D', []); // invalid

		$this->workflow->setTransitions(
			[
				$transition_A_B,
				$transition_A_C,
				$transition_A_D,
				$transition_C_D,
			]
		);

		$state_B->setFinal(true);
		$state_C->setFinal(true);
		$state_D->setFinal(true);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();

		// $checkForObjectIdentity must be false as we have different object instances
		$this->assertContains(new WorkflowValidationError($state_C, 'WorkflowState "state_C" is marked as final, so it cannot have outgoing transitions: Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowTransition( "state_C" => "state_D" )'), $actualValidationErrors, '', false, false);
	}

	public function testWorkflowStateEventsMustBeArrays()
	{
		$state_A = new ExampleWorkflowState('state_A');
		/** @noinspection PhpParamsInspection */
		$state_A->setOnEnterEvents('foo');
		/** @noinspection PhpParamsInspection */
		$state_A->setOnExitEvents('foo');

		$this->workflow->setStates([$state_A]);
		$this->workflow->setInitialState($state_A);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();
		$this->assertContains(new WorkflowValidationError($state_A, 'WorkflowState "state_A" onEnterEvents must be an array of strings'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($state_A, 'WorkflowState "state_A" onExitEvents must be an array of strings'), $actualValidationErrors, '', false, false);
	}

	public function testTransitionsStatesShouldBePresentInTheWorkflow()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$this->workflow->setStates(
			[
				$state_A,
				$state_B,
			]
		);
		$this->workflow->setInitialState($state_A);

		$transition_A_B = new ExampleWorkflowTransition('state_A', 'state_B', null);
		$transition_C_D = new ExampleWorkflowTransition('state_C', 'state_D', null);
		$this->workflow->setTransitions(
			[
				$transition_A_B,
				$transition_C_D,
			]
		);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();
		// $checkForObjectIdentity must be false as we have different object instances
		$this->assertContains(new WorkflowValidationError($transition_C_D, 'WorkflowTransition Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowTransition ("state_C" => "state_D") uses invalid sourceStateId: "state_C"'), $actualValidationErrors, '', false, false);
		$this->assertContains(new WorkflowValidationError($transition_C_D, 'WorkflowTransition Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowTransition ("state_C" => "state_D") uses invalid destinationStateId: "state_D"'), $actualValidationErrors, '', false, false);
	}

	public function testWildcardTransitionWithoutEvent()
	{
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');

		$this->workflow->setStates([$state_A, $state_B]);
		$this->workflow->setInitialState($state_A);

		$noEvents = [];
		$wildcardTransition = new ExampleWorkflowTransition($state_A->getStateId(), $state_B->getStateId(), $noEvents);
		$wildcardTransition->setStartsFromAnyStateId(true);
		$this->workflow->setTransitions([$wildcardTransition]);

		$actualResult = $this->validator->validate($this->workflow);

		$actualValidationErrors = $actualResult->getValidationErrors();
		$this->assertContains(new WorkflowValidationError($wildcardTransition, 'WorkflowTransition Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowTransition ("state_A" => "state_B") must be triggered by at least one event'), $actualValidationErrors, '', false, false);
	}

}
