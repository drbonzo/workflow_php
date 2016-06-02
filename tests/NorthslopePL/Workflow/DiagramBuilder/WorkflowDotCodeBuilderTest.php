<?php
namespace Tests\NorthslopePL\Workflow\DiagramBuilder;

use NorthslopePL\Workflow\DiagramBuilder\WorkflowDotCodeBuilder;
use NorthslopePL\Workflow\WorkflowTransition;
use PHPUnit_Framework_TestCase;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflow;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowState;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowStateWithEventsAndActions;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowTransition;
use Tests\NorthslopePL\Workflow\Fixtures\ExampleWorkflowTransitionWithGuardAndRun;

class WorkflowDotCodeBuilderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var WorkflowDotCodeBuilder
	 */
	private $dotCodeBuilder;

	protected function setUp()
	{
		$this->dotCodeBuilder = new WorkflowDotCodeBuilder();
	}

	public function testEmptyWorkflow()
	{
		$workflow = new ExampleWorkflow('Empty_Workflow');

		$code = $this->dotCodeBuilder->buildCode($workflow);
		$expectedCode = file_get_contents(__DIR__ . '/Fixtures/Empty_Workflow.gv');
		$this->assertEquals($expectedCode, $code);
	}

	public function testWorkflowWithSingleState()
	{
		$workflow = new ExampleWorkflow('SingleState_Workflow');
		$state_A = new ExampleWorkflowState('state_A');
		$workflow->setStates([$state_A]);
		$workflow->setInitialState($state_A);

		$code = $this->dotCodeBuilder->buildCode($workflow);
		$expectedCode = file_get_contents(__DIR__ . '/Fixtures/SingleState_Workflow.gv');
		$this->assertEquals($expectedCode, $code);
	}

	public function testWorkflowWithTwoStatesAndSingleTransition()
	{
		$workflow = new ExampleWorkflow('TwoStatesWithTransition_Workflow');
		$state_A = new ExampleWorkflowState('state_A');
		$state_B = new ExampleWorkflowState('state_B');
		$state_B->setFinal(true);

		$workflow->setStates([$state_A, $state_B]);
		$workflow->setInitialState($state_A);

		$transition_A_B = new ExampleWorkflowTransition($state_A->getStateId(), $state_B->getStateId());
		$workflow->setTransitions([$transition_A_B]);

		$code = $this->dotCodeBuilder->buildCode($workflow);
		$expectedCode = file_get_contents(__DIR__ . '/Fixtures/TwoStatesWithTransition_Workflow.gv');
		$this->assertEquals($expectedCode, $code);
	}

	public function testComplexWorkflow()
	{
		$workflow = new ExampleWorkflow('Complex_Workflow');

		$state_A = new ExampleWorkflowState('state_A');

		$state_B = new ExampleWorkflowState('state_B');

		$state_C = new ExampleWorkflowState('state_C');
		$state_C->setFinal(true);

		$state_D = new ExampleWorkflowState('state_D');

		$state_Error = new ExampleWorkflowState('state_Error');
		$state_Error->setFinal(true);

		$workflow->setStates(
			[
				$state_A,
				$state_B,
				$state_C,
				$state_D,
				$state_Error
			]
		);

		//

		$transition_A_B = new ExampleWorkflowTransition($state_A->getStateId(), $state_B->getStateId());
		$transition_A_C = new ExampleWorkflowTransition($state_A->getStateId(), $state_C->getStateId());
		$transition_B_C = new ExampleWorkflowTransition($state_B->getStateId(), $state_C->getStateId());
		$transition_B_D = new ExampleWorkflowTransition($state_B->getStateId(), $state_D->getStateId());
		$transition_D_C = new ExampleWorkflowTransition($state_D->getStateId(), $state_C->getStateId());
		$errorTransition = new ExampleWorkflowTransition(WorkflowTransition::__ANY_STATE, $state_Error->getStateId());
		$errorTransition->setStartsFromAnyStateId(true);

		$workflow->setTransitions(
			[
				$transition_A_B,
				$transition_A_C,
				$transition_B_C,
				$transition_B_D,
				$transition_D_C,
				$errorTransition
			]
		);

		//

		$code = $this->dotCodeBuilder->buildCode($workflow);
		$expectedCode = file_get_contents(__DIR__ . '/Fixtures/Complex_Workflow.gv');
		$this->assertEquals($expectedCode, $code);
	}

	public function testComplexWorkflowWithEventsGuardsActions()
	{
		$workflow = new ExampleWorkflow('Complex_Workflow_WithEventsGuardsActions');

		$state_A = new ExampleWorkflowState('state_A');

		$state_B = new ExampleWorkflowStateWithEventsAndActions('state_B');
		$state_B->setOnEnterEvents(['onEnterEvent1', 'onEnterEvent2']);
		$state_B->setOnExitEvents(['onExitEvent3', 'onExitEvent4']);

		$state_C = new ExampleWorkflowState('state_C');
		$state_C->setFinal(true);

		$state_D = new ExampleWorkflowState('state_D');

		$state_Error = new ExampleWorkflowState('state_Error');
		$state_Error->setFinal(true);

		$workflow->setStates(
			[
				$state_A,
				$state_B,
				$state_C,
				$state_D,
				$state_Error
			]
		);

		//

		$transition_A_B = new ExampleWorkflowTransition($state_A->getStateId(), $state_B->getStateId());
		$transition_A_C = new ExampleWorkflowTransitionWithGuardAndRun($state_A->getStateId(), $state_C->getStateId());
		$transition_A_C->setEventNames(['WhenFooBar', 'OtherBar']);
		$transition_B_C = new ExampleWorkflowTransition($state_B->getStateId(), $state_C->getStateId());
		$transition_B_D = new ExampleWorkflowTransition($state_B->getStateId(), $state_D->getStateId());
		$transition_D_C = new ExampleWorkflowTransition($state_D->getStateId(), $state_C->getStateId());
		$errorTransition = new ExampleWorkflowTransition(WorkflowTransition::__ANY_STATE, $state_Error->getStateId());
		$errorTransition->setStartsFromAnyStateId(true);

		$workflow->setTransitions(
			[
				$transition_A_B,
				$transition_A_C,
				$transition_B_C,
				$transition_B_D,
				$transition_D_C,
				$errorTransition
			]
		);

		$code = $this->dotCodeBuilder->buildCode($workflow);
		$expectedCode = file_get_contents(__DIR__ . '/Fixtures/Complex_Workflow_WithEventsGuardsActions.gv');
		$this->assertEquals($expectedCode, $code);
	}

}
