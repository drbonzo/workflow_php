# Changelog

Versions

- development
  - [0.4.0](#version_0_4_0)
  - [0.3.0](#version_0_3_0)
  - [0.2.0](#version_0_2_0)
  - [0.1.0](#version_0_1_0)
- stable
  - none yet

<a name="version_0_4_0"></a>

## 0.4.0

_2016-06-09_

* Removed `AbstractWorkflowTransition`
* Removed methods from WorkflowContext
	- getValue()
	- setValue(...)
	- unsetValue(...)

<a name="version_0_3_0"></a>

## 0.3.0

_2016-06-03_

* Added support for time based guards. See `AbstractWorkflowTransition::timeHasPassed()`
* Removed `AbstractWorkflowContext::getLastStateChangedAt()` and `$lastStateChangedAt`, you need to implement it in your subclass
* Removed `AbstractWorkflowContext::getStateHistory()` - not needed... yet

<a name="version_0_2_0"></a>

## 0.2.0

_2016-06-02_

* Added support for wildcard transitions. Transitions that start from any State.
* Added AbstractWorkflowTransition
* Added and updated tests
* Improved DOT diagram generation
* Some small refactorings in methods, params
* Fixed validation messages

<a name="version_0_1_0"></a>

## 0.1.0 - Initial version

_2016-06-01_

