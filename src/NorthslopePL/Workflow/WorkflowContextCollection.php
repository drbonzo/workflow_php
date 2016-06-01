<?php
namespace NorthslopePL\Workflow;

use NorthslopePL\Workflow\Exceptions\WorkflowLogicException;

class WorkflowContextCollection
{
	/**
	 * @var WorkflowContext[]
	 */
	private $contexts = [];

	/**
	 * @param string $workflowClassName
	 * @param WorkflowContext $context
	 */
	public function addContext($workflowClassName, WorkflowContext $context)
	{
		$this->contexts[$workflowClassName] = $context;
	}

	/**
	 * @param string $workflowClassName
	 *
	 * @return WorkflowContext
	 *
	 * @throws WorkflowLogicException
	 */
	public function getContext($workflowClassName)
	{
		if (isset($this->contexts[$workflowClassName])) {
			return $this->contexts[$workflowClassName];
		} else {
			throw new WorkflowLogicException(sprintf('WorkflowContext not found for key "%s"', $workflowClassName));
		}
	}
}
