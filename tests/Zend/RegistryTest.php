<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_RegistryTest.php
 */

namespace ZendTest;

use Zend\Registry;

/**
 * @category   Zend
 * @package    Zend_Registry
 * @subpackage UnitTests
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
