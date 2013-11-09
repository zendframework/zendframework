<?php
namespace ZendTest\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\Pdo\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testCurrent()
    {
        $stub = $this->getMock('PDOStatement');
        $stub
	    ->expects($this->any())
	    ->method('fetch')
	    ->will($this->returnCallback(function() {return uniqid();}));

	$result = new Result();
	$result->initialize($stub, null);

        $this->assertEquals($result->current(), $result->current());
    }
}
