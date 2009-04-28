<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_View_Filter_Foo
{
	public function filter($buffer)
	{
		return 'foo';
	}
}
