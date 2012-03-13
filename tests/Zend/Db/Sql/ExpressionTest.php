<?php
namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Expression;

/**
 * This is a unit testing test case.
 * A unit here is a method, there will be at least one test per method
 *
 * Expression is a value object with no dependencies/collaborators, therefore, no fixure needed
 */
class ExpressionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Zend\Db\Sql\Expression::setExpression
     * @return Expression
     */
    public function testSetExpression()
    {
        $expression = new Expression();
        $return = $expression->setExpression('Foo Bar');
        $this->assertInstanceOf('Zend\Db\Sql\Expression', $return);
        return $return;
    }

    /**
     * @covers Zend\Db\Sql\Expression::getExpression
     * @depends testSetExpression
     */
    public function testGetExpression(Expression $expression)
    {
        $this->assertEquals('Foo Bar', $expression->getExpression());
    }

    /**
     * @covers Zend\Db\Sql\Expression::setParameters
     */
    public function testSetParameters()
    {
        $expression = new Expression();
        $return = $expression->setParameters('foo');
        $this->assertInstanceOf('Zend\Db\Sql\Expression', $return);
        return $return;
    }

    /**
     * @covers Zend\Db\Sql\Expression::getParameters
     * @depends testSetParameters
     */
    public function testGetParameters(Expression $expression)
    {
        $this->assertEquals('foo', $expression->getParameters());
    }

    /**
     * @covers Zend\Db\Sql\Expression::setTypes
     * @todo   Implement testSetTypes().
     */
    public function testSetTypes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Zend\Db\Sql\Expression::getTypes
     * @todo   Implement testGetTypes().
     */
    public function testGetTypes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Zend\Db\Sql\Expression::getExpressionData
     * @todo   Implement testGetExpressionData().
     */
    public function testGetExpressionData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
