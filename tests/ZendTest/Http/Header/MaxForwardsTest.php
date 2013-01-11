<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\MaxForwards;

class MaxForwardsTest extends \PHPUnit_Framework_TestCase
{

    public function testMaxForwardsFromStringCreatesValidMaxForwardsHeader()
    {
        $maxForwardsHeader = MaxForwards::fromString('Max-Forwards: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $maxForwardsHeader);
        $this->assertInstanceOf('Zend\Http\Header\MaxForwards', $maxForwardsHeader);
    }

    public function testMaxForwardsGetFieldNameReturnsHeaderName()
    {
        $maxForwardsHeader = new MaxForwards();
        $this->assertEquals('Max-Forwards', $maxForwardsHeader->getFieldName());
    }

    public function testMaxForwardsGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('MaxForwards needs to be completed');

        $maxForwardsHeader = new MaxForwards();
        $this->assertEquals('xxx', $maxForwardsHeader->getFieldValue());
    }

    public function testMaxForwardsToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('MaxForwards needs to be completed');

        $maxForwardsHeader = new MaxForwards();

        // @todo set some values, then test output
        $this->assertEmpty('Max-Forwards: xxx', $maxForwardsHeader->toString());
    }

    /** Implmentation specific tests here */

}
