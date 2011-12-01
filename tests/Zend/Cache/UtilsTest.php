<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend_Cache
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache;
use Zend\Cache\Utils;

/**
 * @category   Zend_Cache
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testGetDiskCapacity()
    {
        $this->_assertCapacity(Utils::getDiskCapacity(__DIR__));
    }

    public function testGetPhpMemoryCapacity()
    {
        $this->_assertCapacity(Utils::getPhpMemoryCapacity());
    }

    public function testGetSystemMemoryCapacity()
    {
        $this->_assertCapacity(Utils::getSystemMemoryCapacity());
    }

    protected function _assertCapacity($capacity)
    {
        $this->assertInternalType('array', $capacity);
        $this->assertArrayHasKey('total', $capacity);
        $this->assertArrayHasKey('free', $capacity);
        $this->assertInternalType('numeric', $capacity['total']);
        $this->assertInternalType('numeric', $capacity['free']);
        $this->assertTrue($capacity['total'] >= $capacity['free']);
    }

    public function testGenerateHash()
    {
        $this->assertEquals(md5('test'), Utils::generateHash('md5', 'test'));
    }

    public function testGenerateHashStrlenHex()
    {
        $this->assertEquals(dechex(strlen('test')), Utils::generateHash('strlen', 'test', false));
    }

    public function testGenerateHashStrlenRaw()
    {
        $this->assertEquals(pack('l', strlen('test')), Utils::generateHash('strlen', 'test', true));
    }

}
