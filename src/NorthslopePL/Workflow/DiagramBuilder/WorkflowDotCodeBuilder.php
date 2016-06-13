<?php
namespace NorthslopePL\Workflow\DiagramBuilder;

use NorthslopePL\Workflow\Workflow;
use NorthslopePL\Workflow\WorkflowState;
use NorthslopePL\Workflow\WorkflowTransition;

class WorkflowDotCodeBuilder
{
	/**
	 * bash: dot -Tpng INPUT.gv -o OUTPUT.png
	 *
	 * @param Workflow $workflow
	 *
	 * @return string
	 */
	public function buildCode(Workflow $workflow)
	{
		$name = str_replace('\\', '_', $workflow->getName());

		$pattern = <<<GRAPHWIZ_PATTERN
digraph %s {

	splines="polyline"
	labelloc="t";
	label="%s";
	node [shape = circle];

%s
}

GRAPHWIZ_PATTERN;

		$statesAndTransitionsCode = '';
		$statesAndTransitionsCode .= $this->buildStatesCode($workflow);
		$statesAndTransitionsCode .= "\n\n";
		$statesAndTransitionsCode .= $this->buildTransitionsCode($workflow);

		$code = sprintf($pattern, $name, $this->stateName($name), $statesAndTransitionsCode);

		return $code;
	}

	private function buildStatesCode(Workflow $workflow)
	{
		$lines = [];
		foreach ($workflow->getStates() as $state) {

			$stateLabel = $this->buildStateString($state);
			if ($state->isFinal()) {
				$lines[] = sprintf("\t" . '"%s" [label="%s", shape=doublecircle]', $state->getStateId(), $stateLabel);
			} else {
				$lines[] = sprintf("\t" . '"%s" [label="%s"]', $state->getStateId(), $stateLabel);
			}
		}

		return join("\n", $lines);
	}


	private function stateName($stateId)
	{
		return str_replace('_', "_\n", $stateId);
	}

	private function buildStateString(WorkflowState $state)
	{
		$stateName = $this->stateName($state->getStateId());

		$onEnterEventsString = $this->buildEventsString($state->getOnEnterEvents());
		$onExitEventsString = $this->buildEventsString($state->getOnExitEvents());

		$onEnterAction = $this->getPHPDocValue($state, 'onEnterAction', 'Workflow-Action');
		$onExitAction = $this->getPHPDocValue($state, 'onExitAction', 'Workflow-Action');

		$label = $stateName;
		if ($onEnterAction !== null && strtolower($onEnterAction) !== 'none') {
			$label .= sprintf("\n\n enter-action: %s", $this->wordWrap($onEnterAction));
		}

		if ($onEnterEventsString) {
			$label .= sprintf("\n\n enter-events: [%s]", $this->wordWrap($onEnterEventsString));
		}

		if ($onExitAction !== null && strtolower($onExitAction) !== 'none') {
			$label .= sprintf("\n\n exit-action: %s", $this->wordWrap($onExitAction));
		}

		if ($onExitEventsString) {
			$label .= sprintf("\n\n exit-events: [%s]", $this->wordWrap($onExitEventsString));
		}

		// escape double-quotes
		$label = str_replace("\"", "\\\"", $label);
		return $label;
	}

	private function buildTransitionsCode(Workflow $workflow)
	{
		$lines = [];
		foreach ($workflow->getTransitions() as $transition) {

			if ($transition->startsFromAnyStateId()) {

				foreach ($workflow->getStates() as $state) {

					if ($workflow->stateMayBeASourceForWildcardTransition($state, $transition)) {
						$lines[] = $this->buildTransitionCode($transition, $state->getStateId(), $transition->getDestinationStateId());
					}
				}

			} else {

				$lines[] = $this->buildTransitionCode($transition, $transition->getSourceStateId(), $transition->getDestinationStateId());

			}
		}

		return join("\n", $lines);
	}

	/**
	 * @param WorkflowTransition $transition
	 * @param string $sourceStateId
	 * @param string $destinationStateId
	 * @return string
	 */
	private function buildTransitionCode(WorkflowTransition $transition, $sourceStateId, $destinationStateId)
	{
		$eventsString = $this->buildEventsString($transition->getEventNames());
		$guardString = $this->buildGuardString($transition);
		$actionString = $this->buildActionString($transition);
		$label = sprintf('%s%s%s', $this->wordWrap($eventsString), $this->wordWrap($guardString), $this->wordWrap($actionString));

		return sprintf("\t" . '"%s" -> "%s" [label="%s"];', $sourceStateId, $destinationStateId, $label);
	}

	private function buildGuardString(WorkflowTransition $transition)
	{
		$guard = $this->getPHPDocValue($transition, 'checkGuardCondition', 'Workflow-Guard');
		if ($guard !== null && strtolower($guard) !== 'none') {
			return sprintf("\n[%s]", $guard);
		} else {
			return '';
		}
	}

	private function buildActionString(WorkflowTransition $transition)
	{
		$action = $this->getPHPDocValue($transition, 'run', 'Workflow-Action');
		if ($action !== null && strtolower($action) !== 'none') {
			return sprintf("\n/ %s", $action);
		} else {
			return '';
		}
	}

	/**
	 * @param object $object
	 * @param string $methodName
	 * @param string $phpdocKeyName
	 *
	 * @return string|null
	 */
	private function getPHPDocValue($object, $methodName, $phpdocKeyName)
	{
		$reflectionClass = new \ReflectionObject($object);
		$reflectionMethod = $reflectionClass->getMethod($methodName);
		$phpdoc = $reflectionMethod->getDocComment();

		$pattern = '#@' . $phpdocKeyName . ' (.+)#';
		if (preg_match($pattern, $phpdoc, $matches)) {
			$value = trim($matches[1]);
			$value = ($value === '' ? null : $value);
			return $value;
		} else {
			return null;
		}
	}

	/**
	 * @param string[] $eventNames
	 *
	 * @return string
	 */
	private function buildEventsString($eventNames)
	{
		$events = [];
		foreach ($eventNames as $eventName) {
			$events[] = $this->stateName($eventName);
		}

		return join(", ", $events);
	}

	/**
	 * @param string $text
	 * @return string
	 */
	private function wordWrap($text)
	{
		if (empty($text)) {
			return $text;
		} else {
			return wordwrap($text, 20, "\n", false);
		}
	}
}
