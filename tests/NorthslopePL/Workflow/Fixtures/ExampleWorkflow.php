<?php
namespace Tests\NorthslopePL\Workflow\Fixtures;

use NorthslopePL\Workflow\Workflow;

class ExampleWorkflow extends Workflow
{
	/**
	 * @var
	 */
	private $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

}
