<?php
namespace Tests\NorthslopePL\Workflow;

use NorthslopePL\Workflow\Exceptions\WorkflowLogicException;
use NorthslopePL\Workflow\Workflow;
use NorthslopePL\Workflow\WorkflowState;
use NorthslopePL\Workflow\WorkflowTransition;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflow;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowState;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowTransition;

class WorkflowTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Workflow
	 */
	private $workflow;

	/**
	 * @var ExampleWorkflowState
	 */
	private $state_A;

	/**
	 * @var ExampleWorkflowState
	 */
	private $state_B;

	/**
	 * @var ExampleWorkflowState
	 */
	private $state_C;

	/**
	 * @var ExampleWorkflowState
	 */
	private $state_D;

	/**
	 * @var ExampleWorkflowState
	 */
	private $state_Error;

	/**
	 * @var ExampleWorkflowTransition
	 */
	private $transition_A_B;

	/**
	 * @var ExampleWorkflowTransition
	 */
	private $transition_A_C;

	/**
	 * @var ExampleWorkflowTransition
	 */
	private $transition_B_C;

	/**
	 * @var ExampleWorkflowTransition
	 */
	private $transition_B_D;

	/**
	 * @var ExampleWorkflowTransition
	 */
	private $transition_D_C;

	/**
	 * @var ExampleWorkflowTransition
	 */
	private $errorTransition;

	protected function setUp()
	{
		$this->initializeWorkflow();
	}

	private function initializeWorkflow()
	{
		$this->workflow = new ExampleWorkflow('ExampleWorkflow');

		$this->state_A = new ExampleWorkflowState('state_A');

		$this->state_B = new ExampleWorkflowState('state_B');

		$this->state_C = new ExampleWorkflowState('state_C');
		$this->state_C->setFinal(true);

		$this->state_D = new ExampleWorkflowState('state_D');

		$this->state_Error = new ExampleWorkflowState('state_Error');
		$this->state_Error->setFinal(true);

		$this->workflow->setStates(
			[
				$this->state_A,
				$this->state_B,
				$this->state_C,
				$this->state_D,
				$this->state_Error
			]
		);

		//

		$this->transition_A_B = new ExampleWorkflowTransition($this->state_A->getStateId(), $this->state_B->getStateId());
		$this->transition_A_C = new ExampleWorkflowTransition($this->state_A->getStateId(), $this->state_C->getStateId());
		$this->transition_B_C = new ExampleWorkflowTransition($this->state_B->getStateId(), $this->state_C->getStateId());
		$this->transition_B_D = new ExampleWorkflowTransition($this->state_B->getStateId(), $this->state_D->getStateId());
		$this->transition_D_C = new ExampleWorkflowTransition($this->state_D->getStateId(), $this->state_C->getStateId());
		$this->errorTransition = new ExampleWorkflowTransition(WorkflowTransition::__ANY_STATE, $this->state_Error->getStateId());
		$this->errorTransition->setStartsFromAnyStateId(true);

		$this->workflow->setTransitions(
			[
				$this->transition_A_B,
				$this->transition_A_C,
				$this->transition_B_C,
				$this->transition_B_D,
				$this->transition_D_C,
				$this->errorTransition
			]
		);
	}

	public function testGetName()
	{
		$this->assertSame('ExampleWorkflow', $this->workflow->getName());
	}

	public function testGetInitialState()
	{
		$this->assertNull($this->workflow->getInitialState());
		$this->workflow->setInitialState($this->state_A);
		$this->assertSame($this->state_A, $this->workflow->getInitialState());
	}

	public function testGetStateForStateIdForExistingState()
	{
		$this->assertTrue($this->workflow->hasStateForStateId('state_A'));
		$this->assertSame($this->state_A, $this->workflow->getStateForStateId('state_A'));

		$this->assertTrue($this->workflow->hasStateForStateId('state_B'));
		$this->assertSame($this->state_B, $this->workflow->getStateForStateId('state_B'));

		$this->assertTrue($this->workflow->hasStateForStateId('state_C'));
		$this->assertSame($this->state_C, $this->workflow->getStateForStateId('state_C'));

		$this->assertTrue($this->workflow->hasStateForStateId('state_D'));
		$this->assertSame($this->state_D, $this->workflow->getStateForStateId('state_D'));

		$this->assertTrue($this->workflow->hasStateForStateId('state_Error'));
		$this->assertSame($this->state_Error, $this->workflow->getStateForStateId('state_Error'));
	}

	public function testGetStateForStateIdForNotExistingState()
	{
		$this->assertFalse($this->workflow->hasStateForStateId('INVALID'));

		$this->setExpectedExceptionRegExp(WorkflowLogicException::class, '#WorkflowState not found for stateId: "INVALID"#');
		$this->workflow->getStateForStateId('INVALID');
	}

	public function testTransitions()
	{
		$expectedTransitions = [
			$this->transition_A_B,
			$this->transition_A_C,
			$this->transition_B_C,
			$this->transition_B_D,
			$this->transition_D_C,
			$this->errorTransition,
		];
		$this->assertEquals($expectedTransitions, $this->workflow->getTransitions());
	}

	/**
	 * @param WorkflowState $state
	 * @param boolean $mayBe
	 *
	 * @dataProvider stateMayBeASourceForWildcardTransitionDataProvider
	 */
	public function testStateMayBeASourceForWildcardTransition(WorkflowState $state, $mayBe)
	{
		$transition = $this->errorTransition;
		$this->assertTrue($transition->startsFromAnyStateId());
		$this->assertSame($mayBe, $this->workflow->stateMayBeASourceForWildcardTransition($state, $transition));
	}

	public function stateMayBeASourceForWildcardTransitionDataProvider()
	{
		$this->initializeWorkflow();

		return [
			[$this->state_A, true],
			[$this->state_B, true],
			[$this->state_C, false],// final state
			[$this->state_D, true],
			[$this->state_Error, false], // wildcard transition destination state
		];
	}

	/**
	 * @param WorkflowState $state
	 * @param boolean $mayBe
	 *
	 * @dataProvider stateMayBeASourceForWildcardTransitionWhichIsNotWildcardDataProvider
	 */
	public function testStateMayBeASourceForWildcardTransitionWhichIsNotWildcard(WorkflowState $state, $mayBe)
	{
		$transition = $this->transition_A_B;
		$this->assertFalse($transition->startsFromAnyStateId());
		$this->assertSame($mayBe, $this->workflow->stateMayBeASourceForWildcardTransition($state, $transition));
	}

	public function stateMayBeASourceForWildcardTransitionWhichIsNotWildcardDataProvider()
	{
		$this->initializeWorkflow();

		return [
			[$this->state_A, false],
			[$this->state_B, false],
			[$this->state_C, false],// final state
			[$this->state_D, false],
			[$this->state_Error, false], // wildcard transition destination state
		];
	}

	/**
	 * @param string $sourceStateId
	 * @param WorkflowTransition[] $expectedTransitions
	 *
	 * @dataProvider getTransitionsFromStateIdDataProvider
	 */
	public function testGetTransitionsFromStateId($sourceStateId, $expectedTransitions)
	{
		$actualTransitions = $this->workflow->getTransitionsFromStateId($sourceStateId);

		$this->assertEquals($expectedTransitions, $actualTransitions);
	}

	public function getTransitionsFromStateIdDataProvider()
	{
		$this->initializeWorkflow();

		return [
			[$this->state_A->getStateId(), [$this->transition_A_B, $this->transition_A_C, $this->errorTransition]],
			[$this->state_B->getStateId(), [$this->transition_B_C, $this->transition_B_D, $this->errorTransition]],
			[$this->state_C->getStateId(), []],
			[$this->state_D->getStateId(), [$this->transition_D_C, $this->errorTransition]],
			[$this->state_Error->getStateId(), []],
		];
	}
}
