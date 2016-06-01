<?php
namespace NorthslopePL\Workflow\CodeGenerator;

class Workflow
{
	/**
	 * @var string
	 */
	public $classname;

	/**
	 * @var string
	 */
	public $namespace;

	/**
	 * @var string
	 */
	public $eventsClassname;

	//

	/**
	 * @var string
	 */
	public $eventsFilename;

	/**
	 * @var string
	 */
	public $dir;

	/**
	 * @var State[]
	 */
	public $states = [];

	/**
	 * @var Transition[]
	 */
	public $transitions = [];

	/**
	 * @var string
	 */
	public $filename;

	/**
	 * @var string
	 */
	public $contextClassname;

	/**
	 * @var string
	 */
	public $contextFilename;

	/**
	 * @var string
	 */
	public $stateIdClassname;

	/**
	 * @var string
	 */
	public $stateIdFilename;

	/**
	 * @var string
	 */
	public $builderClassname;

	/**
	 * @var string
	 */
	public $builderFilename;
}
