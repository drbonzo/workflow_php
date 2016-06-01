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
				$lines[] = sprintf('"%s" [label="%s", shape=doublecircle]', $state->getStateId(), $stateLabel);
			} else {
				$lines[] = sprintf('"%s" [label="%s"]', $state->getStateId(), $stateLabel);
			}
		}

		return join("\n", $lines);
	}

	private function buildTransitionsCode(Workflow $workflow)
	{
		$lines = [];
		foreach ($workflow->getTransitions() as $transition) {

			$eventsString = $this->buildEventsString($transition->getEventNames());
			$guardString = $this->buildGuardString($transition);
			$actionString = $this->buildActionString($transition);
			$label = sprintf('%s%s%s', $eventsString, $guardString, $actionString);
			$lines[] = sprintf('"%s" -> "%s" [label="%s"];', $transition->getSourceStateId(), $transition->getDestinationStateId(), $label);
		}

		return join("\n", $lines);
	}

	private function stateName($stateId)
	{
		return str_replace('_', "_\n", $stateId);
	}

	private function buildGuardString(WorkflowTransition $transition)
	{
		$guard = $this->getPHPDocValue($transition, 'checkGuardCondition', 'Workflow-Guard');
		if ($guard === null || strtolower($guard) === 'none') {
			return '';
		} else {
			return sprintf("\n[%s]", $guard);
		}
	}

	private function buildActionString(WorkflowTransition $transition)
	{
		$action = $this->getPHPDocValue($transition, 'run', 'Workflow-Action');
		if ($action === null || strtolower($action) === 'none') {
			return '';
		} else {
			return sprintf("\n/ %s", $action);
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

	private function buildStateString(WorkflowState $state)
	{
		$stateName = $this->stateName($state->getStateId());

		$onEnterEventsString = $this->buildEventsString($state->getOnEnterEvents());
		$onExitEventsString = $this->buildEventsString($state->getOnExitEvents());

		$onEnterAction = $this->getPHPDocValue($state, 'onEnterAction', 'Workflow-Action');
		$onExitAction = $this->getPHPDocValue($state, 'onExitAction', 'Workflow-Action');

		$label = $stateName;
		if ($onEnterEventsString) {
			$label .= sprintf("\n\n enter-events: [%s]", $onEnterEventsString);
		}

		if ($onEnterAction !== null && strtolower($onEnterAction) !== 'none') {
			$label .= sprintf("\n\n enter-action: %s", $onEnterAction);
		}

		if ($onExitEventsString) {
			$label .= sprintf("\n\n exit-events: [%s]", $onExitEventsString);
		}

		if ($onExitAction !== null && strtolower($onExitAction) !== 'none') {
			$label .= sprintf("\n\n exit-action: %s", $onExitAction);
		}

		// escape double-quotes
		$label = str_replace("\"", "\\\"", $label);
		return $label;
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
}
