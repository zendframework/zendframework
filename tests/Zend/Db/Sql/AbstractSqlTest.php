<?php

namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Expression,
    Zend\Db\Sql\ExpressionInterface,
    Zend\Db\Adapter\Driver\DriverInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Sql\Predicate;

class AbstractSqlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
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

    /**
     * @covers Zend\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWithDriverAndParameterizationTypePositional()
    {
//        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
//        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue(DriverInterface::PARAMETERIZATION_POSITIONAL));
//        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
//
//        $expression = new Expression('? > ? AND y < ?', array('x', 5, 10), array(Expression::TYPE_IDENTIFIER));
//        $sqlAndParams = $this->invokeProcessExpressionMethod($expression, $mockDriver);
//
//        $this->assertEquals('"x" > ? AND y < ?', $sqlAndParams['sql']);
//        $this->assertInternalType('array', $sqlAndParams['parameters']);
//        $this->assertEquals(
//            array(5, 10),
//            $sqlAndParams['parameters']
//        );
    }

    /**
     * @covers Zend\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWithDriverAndParameterizationTypeNamed()
    {
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue(DriverInterface::PARAMETERIZATION_NAMED));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnCallback(function ($x) {
            return ':' . $x;
        }));

        $expression = new Expression('? > ? AND y < ?', array('x', 5, 10), array(Expression::TYPE_IDENTIFIER));
        $sqlAndParams = $this->invokeProcessExpressionMethod($expression, $mockDriver);

        $this->assertRegExp('#"x" > :expr\d\d\d\dParam1 AND y < :expr\d\d\d\dParam2#', $sqlAndParams['sql']);
        $this->assertInternalType('array', $sqlAndParams['parameters']);

        // test keys and values
        preg_match('#expr(\d\d\d\d)Param1#', key($sqlAndParams['parameters']), $matches);
        $expressionNumber = $matches[1];

        $this->assertRegExp('#expr\d\d\d\dParam1#', key($sqlAndParams['parameters']));
        $this->assertEquals(5, current($sqlAndParams['parameters']));
        next($sqlAndParams['parameters']);
        $this->assertRegExp('#expr\d\d\d\dParam2#', key($sqlAndParams['parameters']));
        $this->assertEquals(10, current($sqlAndParams['parameters']));

        // ensure next invocation increases number by 1
        $sqlAndParamsNext = $this->invokeProcessExpressionMethod($expression, $mockDriver);
        preg_match('#expr(\d\d\d\d)Param1#', key($sqlAndParamsNext['parameters']), $matches);
        $expressionNumberNext = $matches[1];

        $this->assertEquals(1, (int) $expressionNumberNext - (int) $expressionNumber);
    }

    /**
     * @covers Zend\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWorksWithExpressionContainingStringParts()
    {
        $expression = new Predicate\Expression('x = ?', 5);

        $predicateSet = new Predicate\PredicateSet(array(new Predicate\PredicateSet(array($expression))));
        $sqlAndParams = $this->invokeProcessExpressionMethod($predicateSet);

        $this->assertEquals("(x = '5')", $sqlAndParams['sql']);
        $this->assertInternalType('array', $sqlAndParams['parameters']);
        $this->assertEmpty($sqlAndParams['parameters']);
    }

    protected function invokeProcessExpressionMethod(ExpressionInterface $expression, DriverInterface $driver = null)
    {
        $method = new \ReflectionMethod($this->abstractSql, 'processExpression');
        $method->setAccessible(true);
        return $method->invoke($this->abstractSql, $expression, new Sql92, $driver);
    }

}
