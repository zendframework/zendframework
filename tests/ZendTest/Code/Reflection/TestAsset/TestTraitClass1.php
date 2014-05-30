<?php
//issue #6620
require_once 'TestTraitClass2.php';
class FooClass
{
	use TestTrait;

	/**
	* @var bool
	*/
	protected static $other = false;


	/**
	* Constructor
	*
	* @param bool $other
	*/
	public function __construct($other)
	{
		$this->other = $other;
	}
}
