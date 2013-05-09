<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Filter;

use Zend\Log\Filter\SuppressFilter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class SuppressFilterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = new SuppressFilter();
    }

    public function testSuppressIsInitiallyOff()
    {
        $this->assertTrue($this->filter->filter(array()));
    }

    public function testSuppressByConstructorBoolean()
    {
        $this->filter = new SuppressFilter(true);
        $this->assertFalse($this->filter->filter(array()));
        $this->assertFalse($this->filter->filter(array()));
    }

    public function testSuppressByConstructorArray()
    {
        $this->filter = new SuppressFilter(array('suppress' => true));
        $this->assertFalse($this->filter->filter(array()));
        $this->assertFalse($this->filter->filter(array()));
    }

     public function testConstructorThrowsOnInvalidSuppressValue()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Suppress must be an boolean');
        new SuppressFilter('foo');
    }

    public function testSuppressOn()
    {
        $this->filter->suppress(true);
        $this->assertFalse($this->filter->filter(array()));
        $this->assertFalse($this->filter->filter(array()));
    }

    public function testSuppressOff()
    {
        $this->filter->suppress(false);
        $this->assertTrue($this->filter->filter(array()));
        $this->assertTrue($this->filter->filter(array()));
    }

    public function testSuppressCanBeReset()
    {
        $this->filter->suppress(true);
        $this->assertFalse($this->filter->filter(array()));
        $this->filter->suppress(false);
        $this->assertTrue($this->filter->filter(array()));
        $this->filter->suppress(true);
        $this->assertFalse($this->filter->filter(array()));
    }
}
