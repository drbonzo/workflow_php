# Changelog

# CURRENT

* Fixed WorkflowContext_Template.txt to contain new methods: commit(), getCurrentStateId(), getLastStateChangedAt(). Removed AbstractWorkflowContext from template.
## 0.6.1

_2016-06-14_

* Updated tests to phpunit 5.4

## 0.6.0

_2016-06-13_

* `WorkflowMachine` + `EventDispatcher`
  * Removed `EventDispatcher $eventDispatcher` from `WorkflowMachine::__construct()`
  * Added `EventDispatcher $eventDispatcher` to `WorkflowMachine::execute(...)`
  * this allows to call `execute()` with different `$eventDispatcher`
  * and you don't need to setup `$eventDispatcher` just to retrieve events from Workflows
* `WorkflowDotCodeBuilder` wraps Guards, Actions and Event names to 20 characters

## 0.5.0

_2016-06-10_

* WorkflowMachine calls WorkflowContext::commit() after performing single Transition to save changes in object being processed by the Workflow
  - Added WorkflowContext::commit()


## 0.4.0

_2016-06-09_

* Removed `AbstractWorkflowTransition`
* Removed methods from WorkflowContext
  - getValue()
  - setValue(...)
  - unsetValue(...)

## 0.3.0

_2016-06-03_

* Added support for time based guards. See `AbstractWorkflowTransition::timeHasPassed()`
* Removed `AbstractWorkflowContext::getLastStateChangedAt()` and `$lastStateChangedAt`, you need to implement it in your subclass
* Removed `AbstractWorkflowContext::getStateHistory()` - not needed... yet

## 0.2.0

_2016-06-02_

* Added support for wildcard transitions. Transitions that start from any State.
* Added AbstractWorkflowTransition
* Added and updated tests
* Improved DOT diagram generation
* Some small refactorings in methods, params
* Fixed validation messages

## 0.1.0 - Initial version

_2016-06-01_

