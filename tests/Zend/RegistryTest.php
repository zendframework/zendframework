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
 * @category   Zend
 * @package    Zend_Registry
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest;

use Zend\Registry;

/**
 * @category   Zend
 * @package    Zend_Registry
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Registry
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->registry = new Registry();
    }

    public function testRegistryUninitIsRegistered()
    {
        $this->assertFalse($this->registry->isRegistered('objectname'));
    }

    public function testReturnsDefaultValueIfKeyDoesNotExist()
    {
        $this->assertEquals('foo bar', $this->registry->get('foo', 'foo bar'));
    }

    public function testRegistryStoresValues()
    {
        $this->registry->set('foo', 'bar');
        $this->assertEquals('bar', $this->registry->get('foo'));
    }

    public function testIsRegisteredTestsIfKeysAreRegistered()
    {
        $this->assertFalse($this->registry->isRegistered('foo'));
        $this->registry->set('foo', 'bar');
        $this->assertTrue($this->registry->isRegistered('foo'));
    }

    public function testRegistryActsAsAnArray()
    {
        $registry = $this->registry;
        $registry['emptyArray'] = array();

        $this->assertTrue(isset($registry['emptyArray']));
    }

    public function testRegistryTreatsMembersAsProperties()
    {
        $this->registry->foo = 'bar';
        $this->assertTrue(isset($this->registry->foo));
        $this->assertEquals('bar', $this->registry->foo);
    }
}
