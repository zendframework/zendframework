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
 * @package    Zend_Queue
 * @subpackage UnitTests
 */

namespace ZendTest\Queue\Adapter;

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */


/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @group      Zend_Queue
 */
class ArrayTest extends AdapterTest
{
    /**
     * getAdapterName() is an method to help make AdapterTest work with any
     * new adapters
     *
     * You must overload this method
     *
     * @return string
     */
    public function getAdapterName()
    {
        return 'ArrayAdapter';
    }

    /**
     * getAdapterName() is an method to help make AdapterTest work with any
     * new adapters
     *
     * You may overload this method.  The default return is
     * 'Zend_Queue_Adapter_' . $this->getAdapterName()
     *
     * @return string
     */
    public function getAdapterFullName()
    {
        return '\Zend\Queue\Adapter\\' . $this->getAdapterName();
    }

    public function getTestConfig()
    {
        return array('driverOptions' => array());
    }

    // test the constants
    public function testConst()
    {
        $this->markTestSkipped('no constants to test');
    }

    // extra non standard tests
    public function test_magic()
    {
        $queue = $this->createQueue(__FUNCTION__);
        $adapter = $queue->getAdapter();

        $this->assertTrue(is_array($adapter->__sleep()));
        $data = serialize($adapter);
        $new = unserialize($data);
        $this->assertEquals($new->getData(), $adapter->getData());
    }

    public function test_get_setData()
    {
        $queue = $this->createQueue(__FUNCTION__);
        $adapter = $queue->getAdapter();

        $data = array('test' => 1);
        $adapter->setData($data);
        $got = $adapter->getData();
        $this->assertEquals($data['test'], $got['test']);
    }
}
