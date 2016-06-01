<?php
namespace NorthslopePL\Workflow\CodeGenerator;

class State
{
	/**
	 * @var string
	 */
	public $classname;

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string|null
	 */
	public $onEnterAction = null;

	/**
	 * @var string|null
	 */
	public $onExitAction = null;

	/**
	 * @var string[]
	 */
	public $onEnterEvents = [];

	/**
	 * @var string[]
	 */
	public $onExitEvents = [];

	/**
	 * @var bool
	 */
	public $isFinal = false;

	//

	/**
	 * @var string
	 */
	public $namespace;

	/**
	 * @var null
	 */
	public $filename;
}
