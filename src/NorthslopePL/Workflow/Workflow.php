<?php
namespace NorthslopePL\Workflow;

use NorthslopePL\Workflow\Exceptions\WorkflowLogicException;

abstract class Workflow
{
	/**
	 * @var WorkflowState[]
	 */
	private $states = [];

	/**
	 * @var WorkflowState
	 */
	private $initialState = null;

	/**
	 * @var WorkflowTransition[]
	 */
	private $transitions = [];

	/**
	 * @return string
	 */
	abstract public function getName();

	/**
	 * @return WorkflowTransition[]
	 */
	public function getTransitions()
	{
		return $this->transitions;
	}

	/**
	 * @param WorkflowTransition[] $transitions
	 */
	public function setTransitions($transitions)
	{
		$this->transitions = $transitions;
	}

	/**
	 * @return WorkflowState[]
	 */
	public function getStates()
	{
		return $this->states;
	}

	/**
	 * @param WorkflowState[] $states
	 */
	public function setStates($states)
	{
		$this->states = $states;
	}

	/**
	 * @return WorkflowState
	 */
	public function getInitialState()
	{
		return $this->initialState;
	}

	/**
	 * @param WorkflowState $initialState
	 */
	public function setInitialState($initialState)
	{
		$this->initialState = $initialState;
	}

	/**
	 * @param string $stateId
	 *
	 * @return WorkflowTransition[]
	 */
	public function getTransitionsFromStateId($stateId)
	{
		$foundTransitions = [];

		foreach ($this->transitions as $transition) {
			if ($transition->getSourceStateId() == $stateId) {
				$foundTransitions[] = $transition;
			}
		}

		return $foundTransitions;
	}

	/**
	 * @param $stateId
	 *
	 * @return WorkflowState
	 *
	 * @throws WorkflowLogicException
	 */
	public function getStateForStateId($stateId)
	{
		foreach ($this->states as $state) {
			if ($state->getStateId() == $stateId) {
				return $state;
			}
		}

		throw new WorkflowLogicException(sprintf('WorkflowState not found for stateId: "%s"', $stateId));
	}

	/**
	 * @param string $stateId
	 * @return bool
	 */
	public function hasStateForStateId($stateId)
	{
		foreach ($this->states as $state) {
			if ($state->getStateId() == $stateId) {
				return true;
			}
		}

		return false;
	}
}
