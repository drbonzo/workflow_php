<?php
namespace NorthslopePL\Workflow\CodeGenerator;

use DirectoryIterator;

class CodeGenerator
{
	public function generate(\stdClass $workflowDescription, $targetDir)
	{
		$workflow = $this->buildWorkflow($workflowDescription, $targetDir);

		$this->checkDir($targetDir);
		$this->createFiles($workflow);
	}

	/**
	 * @param $workflowDescription
	 * @param $targetDir
	 * @return Workflow
	 */
	private function buildWorkflow($workflowDescription, $targetDir)
	{
		$workflow = new Workflow();
		$workflow->classname = $workflowDescription->Workflow->name;
		$workflow->namespace = $workflowDescription->Workflow->namespace;
		$workflow->eventsClassname = $workflowDescription->Workflow->eventsClassname;
		$workflow->eventsFilename = sprintf('%s/%s.php', $targetDir, $workflowDescription->Workflow->eventsClassname);
		$workflow->dir = $targetDir;
		$workflow->filename = sprintf('%s/%s.php', $targetDir, $workflow->classname);

		$workflow->states = $this->buildStates($workflowDescription->States, $workflow);
		$workflow->transitions = $this->buildTransitions($workflowDescription->Transitions, $workflow);

		$workflow->contextClassname = $workflow->classname . 'Context';
		$workflow->contextFilename = sprintf('%s/%sContext.php', $targetDir, $workflow->classname);

		$workflow->stateIdClassname = $workflow->classname . 'StateId';
		$workflow->stateIdFilename = sprintf('%s/%sStateId.php', $targetDir, $workflow->classname);

		$workflow->builderClassname = $workflow->classname . 'Builder';
		$workflow->builderFilename = sprintf('%s/%sBuilder.php', $targetDir, $workflow->classname);

		return $workflow;
	}

	/**
	 * @param $statesDescription
	 * @param Workflow $workflow
	 *
	 * @return State[]
	 */
	private function buildStates($statesDescription, Workflow $workflow)
	{
		$namespace = $workflow->namespace;

		$states = [];
		foreach ($statesDescription as $stateDefinition) {
			$state = new State();
			$state->classname = $stateDefinition->name;
			$state->id = $stateDefinition->id;

			if (isset($stateDefinition->onEnterAction)) {
				$state->onEnterAction = $stateDefinition->onEnterAction;
			}

			if (isset($stateDefinition->onExitAction)) {
				$state->onExitAction = $stateDefinition->onExitAction;
			}

			if (isset($stateDefinition->onEnterEvents)) {
				$state->onEnterEvents = $stateDefinition->onEnterEvents;
			}

			if (isset($stateDefinition->onExitEvents)) {
				$state->onExitEvents = $stateDefinition->onExitEvents;
			}

			if (property_exists($stateDefinition, 'isFinal')) {
				$state->isFinal = (boolean)$stateDefinition->isFinal;
			}

			$state->namespace = $namespace . '\States';
			$state->filename = sprintf('%s/States/%s.php', $workflow->dir, $state->classname);

			$states[] = $state;
		}

		return $states;
	}

	/**
	 * @param $transitionsDescription
	 * @param Workflow $workflow
	 *
	 * @return Transition[]
	 */
	private function buildTransitions($transitionsDescription, Workflow $workflow)
	{
		$transitions = [];

		foreach ($transitionsDescription as $transitionDescription) {
			$transition = new Transition();

			$transition->source = $transitionDescription->source;
			$transition->destination = $transitionDescription->destination;
			$transition->name = $transitionDescription->name;

			if (property_exists($transitionDescription, 'events') && is_array($transitionDescription->events) && count($transitionDescription->events) > 0) {
				$transition->events = $transitionDescription->events;
			}

			if (property_exists($transitionDescription, 'guard') && $transitionDescription->guard) {
				$transition->guard = $transitionDescription->guard;
			}

			if (property_exists($transitionDescription, 'run') && $transitionDescription->run) {
				$transition->run = $transitionDescription->run;
			}

			$transition->filename = sprintf('%s/Transitions/%s.php', $workflow->dir, $transition->name);
			$transition->namespace = $workflow->namespace . '\\Transitions';

			$transitions[] = $transition;
		}
		return $transitions;
	}

	private function checkDir($targetDir)
	{
		if (!file_exists($targetDir)) {
			throw new \RuntimeException(sprintf("Dir %s not found. Create it first", $targetDir));
		}

		$dirIterator = new DirectoryIterator($targetDir);

		foreach ($dirIterator as $fileInfo) {
			if ($fileInfo->isDot()) {
				continue;
			}

			throw new \RuntimeException(sprintf("Dir %s must be empty!", $targetDir));
		}
	}

	private function createFiles(Workflow $workflow)
	{
		$this->generateWorkflowFile($workflow);
		$this->generateWorkflowContext($workflow);
		$this->generateWorkflowStateId($workflow);

		$this->generateWorkflowEvents($workflow);

		$this->generateWorkflowStates($workflow);
		$this->generateWorkflowTransitions($workflow);

		$this->generateWorkflowBuilder($workflow);
	}

	private function generateWorkflowFile(Workflow $workflow)
	{
		$workflowCode = file_get_contents(__DIR__ . '/templates/Workflow_Template.txt');

		$searchAndReplace = [
			'{{Workflow_Namespace}}' => $workflow->namespace,
			'{{Workflow_Classname}}' => $workflow->classname,
		];
		$workflowCode = $this->fillCodeTemplate($workflowCode, $searchAndReplace);

		file_put_contents($workflow->filename, $workflowCode);
	}

	private function generateWorkflowContext(Workflow $workflow)
	{
		$workflowContextCode = file_get_contents(__DIR__ . '/templates/WorkflowContext_Template.txt');

		$searchAndReplace = [
			'{{Workflow_Namespace}}' => $workflow->namespace,
			'{{Workflow_ContextClassname}}' => $workflow->contextClassname,
		];
		$workflowContextCode = $this->fillCodeTemplate($workflowContextCode, $searchAndReplace);

		file_put_contents($workflow->contextFilename, $workflowContextCode);
	}

	private function generateWorkflowStateId(Workflow $workflow)
	{
		$code = file_get_contents(__DIR__ . '/templates/WorkflowStateId_Template.txt');

		$searchAndReplace = [
			'{{Workflow_Namespace}}' => $workflow->namespace,
			'{{Workflow_StateIdClassname}}' => $workflow->stateIdClassname
		];
		$stateIDConstants = [];
		foreach ($workflow->states as $state) {
			$stateIDConstants[] = sprintf("\tconst %s = '%s';", $state->id, $state->id);
		}
		$searchAndReplace['{{WorkflowStateId_Definition}}'] = join("\n", $stateIDConstants);

		$code = $this->fillCodeTemplate($code, $searchAndReplace);

		file_put_contents($workflow->stateIdFilename, $code);
	}

	private function generateWorkflowStates(Workflow $workflow)
	{
		mkdir($workflow->dir . '/States');

		foreach ($workflow->states as $state) {

			$code = file_get_contents(__DIR__ . '/templates/WorkflowState_Template.txt');
			$searchAndReplace = [

				'{{WorkflowState_Namespace}}' => $state->namespace,
				'{{Workflow_Namespace}}' => $workflow->namespace,
				'{{Workflow_StateIdClassname}}' => $workflow->stateIdClassname,
				'{{WorkflowState_Classname}}' => $state->classname,
				'{{WorkflowState_Id}}' => $state->id,
				'{{Workflow_ContextClassname}}' => $workflow->contextClassname,
				'{{Workflow_EventsClassname}}' => $workflow->eventsClassname,
				'{{WorkflowState_onEnterAction}}' => '',
				'{{WorkflowState_onExitAction}}' => '',
				'{{WorkflowState_onEnterEvents}}' => '',
				'{{WorkflowState_onExitEvents}}' => '',
				'{{WorkflowState_isFinal}}' => '',
			];

			if ($state->onEnterAction) {
				$onEnterActionCodeTemplate = file_get_contents(__DIR__ . '/templates/WorkflowState_onEnterAction_Template.txt');
				$searchAndReplace['{{WorkflowState_onEnterAction}}'] = sprintf($onEnterActionCodeTemplate, $state->onEnterAction);
			}

			if ($state->onExitAction) {
				$onExitActionCodeTemplate = file_get_contents(__DIR__ . '/templates/WorkflowState_onExitAction_Template.txt');
				$searchAndReplace['{{WorkflowState_onExitAction}}'] = sprintf($onExitActionCodeTemplate, $state->onExitAction);
			}

			if ($state->onEnterEvents) {
				$onEnterEventsCodeTemplate = file_get_contents(__DIR__ . '/templates/WorkflowState_onEnterEvents_Template.txt');
				$searchAndReplace['{{WorkflowState_onEnterEvents}}'] = sprintf($onEnterEventsCodeTemplate, $this->buildEventNames($workflow->eventsClassname, $state->onEnterEvents));
			}

			if ($state->onExitEvents) {
				$onExitEventsCodeTemplate = file_get_contents(__DIR__ . '/templates/WorkflowState_onExitEvents_Template.txt');
				$searchAndReplace['{{WorkflowState_onExitEvents}}'] = sprintf($onExitEventsCodeTemplate, $this->buildEventNames($workflow->eventsClassname, $state->onExitEvents));
			}

			if ($state->isFinal) {
				$isFinalCodeTemplate = file_get_contents(__DIR__ . '/templates/WorkflowState_isFinal_Template.txt');
				$searchAndReplace['{{WorkflowState_isFinal}}'] = sprintf($isFinalCodeTemplate, $state->isFinal);
			}

			$code = $this->fillCodeTemplate($code, $searchAndReplace);
			$code = $this->fillCodeTemplate($code, $searchAndReplace); // as replacements generate code to replace - see WorkflowState_isFinal_Template

			file_put_contents($state->filename, $code);
		}
	}

	private function generateWorkflowTransitions(Workflow $workflow)
	{
		/** @var State[] $states */
		$states = [];
		foreach ($workflow->states as $state) {
			$states[$state->classname] = $state;
		}
		mkdir($workflow->dir . '/Transitions');

		foreach ($workflow->transitions as $transition) {

			$code = file_get_contents(__DIR__ . '/templates/WorkflowTransition_Template.txt');
			$searchAndReplace = [
				'{{WorkflowTransition_Namespace}}' => $transition->namespace,
				'{{WorkflowTransition_Classname}}' => $transition->name,
				'{{WorkflowTransition_SourceStateClassname}}' => $transition->source,
				'{{WorkflowTransition_SourceStateNamespace}}' => $states[$transition->source]->namespace,
				'{{WorkflowTransition_DestinationStateClassname}}' => $transition->destination,
				'{{WorkflowTransition_DestinationStateNamespace}}' => $states[$transition->destination]->namespace,
				'{{WorkflowTransition_Events}}' => '',
				'{{WorkflowTransition_Guard}}' => $transition->guard ? $transition->guard : 'None',
				'{{WorkflowTransition_GuardTodo}}' => $transition->guard ? '// FIXME implement me' : '',
				'{{WorkflowTransition_Run}}' => $transition->run ? $transition->run : 'None',
				'{{WorkflowTransition_RunTodo}}' => $transition->run ? '// FIXME implement me' : '',
				'{{Workflow_Namespace}}' => $workflow->namespace,
				'{{Workflow_ContextClassname}}' => $workflow->contextClassname,
				'{{Workflow_EventsClassname}}' => $workflow->eventsClassname,
			];

			$searchAndReplace['{{WorkflowTransition_Events}}'] = $this->buildEventNames($workflow->eventsClassname, $transition->events);

			$code = $this->fillCodeTemplate($code, $searchAndReplace);
			$code = $this->fillCodeTemplate($code, $searchAndReplace);

			file_put_contents($transition->filename, $code);
		}
	}

	private function generateWorkflowEvents(Workflow $workflow)
	{
		$eventNames = [];

		foreach ($workflow->states as $state) {
			$eventNames = array_merge($eventNames, $state->onEnterEvents);
			$eventNames = array_merge($eventNames, $state->onExitEvents);
		}

		foreach ($workflow->transitions as $transition) {
			$eventNames = array_merge($eventNames, $transition->events);
		}

		$eventNames = array_unique($eventNames);

		$eventDefinitions = [];

		foreach ($eventNames as $eventName) {
			$eventDefinitions[] = sprintf("\tconst %s = '%s';", $eventName, $eventName);
		}

		$code = file_get_contents(__DIR__ . '/templates/WorkflowEvents_Template.txt');

		$searchAndReplace = [
			'{{WorkflowTransition_Namespace}}' => $workflow->namespace,
			'{{Workflow_EventsClassname}}' => $workflow->eventsClassname,
			'{{Workflow_EventsDefinition}}' => join("\n", $eventDefinitions),
		];

		$code = $this->fillCodeTemplate($code, $searchAndReplace);
		$code = $this->fillCodeTemplate($code, $searchAndReplace);

		file_put_contents($workflow->eventsFilename, $code);
	}

	/**
	 * @param string $codeTemplate
	 * @param array $searchAndReplace
	 * @return mixed
	 */
	private function fillCodeTemplate($codeTemplate, $searchAndReplace)
	{
		$code = str_replace(array_keys($searchAndReplace), array_values($searchAndReplace), $codeTemplate);
		return $code;
	}

	/**
	 * @param $eventsClassname
	 * @param $eventNames
	 * @return string
	 */
	private function buildEventNames($eventsClassname, $eventNames)
	{
		$fullEventNames = [];

		foreach ($eventNames as $eventName) {
			$fullEventNames[] = sprintf("\t\t\t%s::%s,", $eventsClassname, $eventName);
		}

		return join("\n", $fullEventNames);
	}

	private function generateWorkflowBuilder(Workflow $workflow)
	{
		$useDefinitions = [];
		$statesDefinitions = [];
		$transitionsDefinitions = [];

		foreach ($workflow->states as $state) {
			$useDefinitions[] = sprintf('use %s\\%s;', $state->namespace, $state->classname);
			$statesDefinitions[] = sprintf("\t\t\tnew %s(),", $state->classname);
		}

		foreach ($workflow->transitions as $transition) {
			$useDefinitions[] = sprintf('use %s\\%s;', $transition->namespace, $transition->name);
			$transitionsDefinitions[] = sprintf("\t\t\tnew %s(),", $transition->name);
		}

		$code = file_get_contents(__DIR__ . '/templates/WorkflowBuilder_Template.txt');

		$searchAndReplace = [
			'{{Workflow_Namespace}}' => $workflow->namespace,
			'{{Workflow_Classname}}' => $workflow->classname,
			'{{WorkflowBuilder_Classname}}' => $workflow->builderClassname,
			'{{Workflow_Use_StatesAndTransitions}}' => join("\n", $useDefinitions),
			'{{Workflow_States_Definition}}' => join("\n", $statesDefinitions),
			'{{Workflow_Transitions_Definition}}' => join("\n", $transitionsDefinitions),
		];

		$code = $this->fillCodeTemplate($code, $searchAndReplace);
		$code = $this->fillCodeTemplate($code, $searchAndReplace);

		file_put_contents($workflow->builderFilename, $code);
	}
}

