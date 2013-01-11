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

use Zend\Http\Header\Refresh;

class RefreshTest extends \PHPUnit_Framework_TestCase
{

    public function testRefreshFromStringCreatesValidRefreshHeader()
    {
        $refreshHeader = Refresh::fromString('Refresh: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $refreshHeader);
        $this->assertInstanceOf('Zend\Http\Header\Refresh', $refreshHeader);
    }

    public function testRefreshGetFieldNameReturnsHeaderName()
    {
        $refreshHeader = new Refresh();
        $this->assertEquals('Refresh', $refreshHeader->getFieldName());
    }

    public function testRefreshGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Refresh needs to be completed');

        $refreshHeader = new Refresh();
        $this->assertEquals('xxx', $refreshHeader->getFieldValue());
    }

    public function testRefreshToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Refresh needs to be completed');

        $refreshHeader = new Refresh();

        // @todo set some values, then test output
        $this->assertEmpty('Refresh: xxx', $refreshHeader->toString());
    }

    /** Implmentation specific tests here */

}
