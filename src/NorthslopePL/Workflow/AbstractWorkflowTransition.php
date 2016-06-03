<?php
namespace NorthslopePL\Workflow;

use DateTime;

/**
 * Use this trait in your class to simplify it's code by using default configuration.
 */
trait AbstractWorkflowTransition
{
	public function startsFromAnyStateId()
	{
		return false;
	}

	/**
	 * @param WorkflowContext $context
	 *
	 * @return boolean
	 *
	 * true - guard condition is met - we can run this transition
	 * false - transition will not be run
	 *
	 * @Workflow-Guard None
	 */
	public function checkGuardCondition($context)
	{
		// no guard check, by default
		return true;
	}

	/**
	 * Code to run when this transition is triggered.
	 *
	 * @param WorkflowContext $context
	 *
	 * @return void
	 *
	 * @Workflow-Action None
	 *
	 * @codeCoverageIgnore
	 */
	public function run($context)
	{
	}

	/**
	 * @param WorkflowContext $context
	 * @param string $timeExpression - see http://php.net/manual/en/datetime.modify.php must be positive (like +5 days)
	 *
	 * @return bool
	 */
	protected function timeHasPassed($context, $timeExpression)
	{
		$lastStateChangeDateTime = $context->getLastStateChangedAt();
		if ($lastStateChangeDateTime === null) {
			return false;
		}

		$expirationTime = clone $lastStateChangeDateTime;
		$expirationTime->modify($timeExpression);
		$now = new DateTime('now');

		$timeHasPassed = ($now > $expirationTime);
		return $timeHasPassed;
	}
}
