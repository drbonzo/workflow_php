<?php
namespace NorthslopePL\Workflow\CodeGenerator;

class Transition
{
	/**
	 * @var string
	 */
	public $source;

	/**
	 * @var string
	 */
	public $destination;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string[]|null
	 */
	public $events = [];

	/**
	 * @var string|null
	 */
	public $guard = null;

	/**
	 * @var string|null
	 */
	public $run = null;

	//

	/**
	 * @var string
	 */
	public $namespace;

	/**
	 * @var string
	 */
	public $filename;
}
