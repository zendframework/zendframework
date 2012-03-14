<?php

namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Expression,
    Zend\Db\Adapter\Driver\DriverInterface,
    Zend\Db\Adapter\Platform\Sql92;

class AbstractSqlTest extends \PHPUnit_Framework_TestCase
{

    protected $abstractSql = null;

    public function setup()
    {
        $this->abstractSql = $this->getMockForAbstractClass('Zend\Db\Sql\AbstractSql');

    }

    /**
     * @covers Zend\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWithoutDriver()
    {
        $expression = new Expression('? > ? AND y < ?', array('x', 5, 10), array(Expression::TYPE_IDENTIFIER));
        $sqlAndParams = $this->invokeProcessExpressionMethod($expression);
        $this->assertEquals("\"x\" > '5' AND y < '10'", $sqlAndParams['sql']);
        $this->assertInternalType('array', $sqlAndParams['parameters']);
        $this->assertEmpty($sqlAndParams['parameters']);
    }

    protected function invokeProcessExpressionMethod(Expression $expression, DriverInterface $driver = null)
    {
        $method = new \ReflectionMethod($this->abstractSql, 'processExpression');
        $method->setAccessible(true);
        return $method->invoke($this->abstractSql, $expression, new Sql92, $driver);
    }

}