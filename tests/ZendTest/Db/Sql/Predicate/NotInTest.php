<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Predicate;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\NotIn;

class NotInTest extends TestCase
{
    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $in = new NotIn();
        $in->setIdentifier('foo.bar')
            ->setValueSet(array(1, 2, 3));
        $expected = array(array(
            '%s NOT IN (%s, %s, %s)',
            array('foo.bar', 1, 2, 3),
            array(NotIn::TYPE_IDENTIFIER, NotIn::TYPE_VALUE, NotIn::TYPE_VALUE, NotIn::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselect()
    {
        $select = new Select;
        $in = new NotIn('foo', $select);
        $expected = array(array(
            '%s NOT IN %s',
            array('foo', $select),
            array($in::TYPE_IDENTIFIER, $in::TYPE_VALUE)
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselectAndIdentifier()
    {
        $select = new Select;
        $in = new NotIn('foo', $select);
        $expected = array(array(
            '%s NOT IN %s',
            array('foo', $select),
            array($in::TYPE_IDENTIFIER, $in::TYPE_VALUE)
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselectAndArrayIdentifier()
    {
        $select = new Select;
        $in = new NotIn(array('foo', 'bar'), $select);
        $expected = array(array(
            '(%s, %s) NOT IN %s',
            array('foo', 'bar', $select),
            array($in::TYPE_IDENTIFIER, $in::TYPE_IDENTIFIER, $in::TYPE_VALUE)
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }
}
