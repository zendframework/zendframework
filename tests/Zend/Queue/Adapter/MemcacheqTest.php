<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Queue
 */

namespace ZendTest\Queue\Adapter;
use Zend\Queue\Adapter;

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
class MemcacheqTest extends AdapterTest
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
        return 'Memcacheq';
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
        $driverOptions = array();
        if (defined('TESTS_ZEND_QUEUE_MEMCACHEQ_HOST')) {
            $driverOptions['host'] = TESTS_ZEND_QUEUE_MEMCACHEQ_HOST;
        }
        if (defined('TESTS_ZEND_QUEUE_MEMCACHEQ_PORT')) {
            $driverOptions['port'] = TESTS_ZEND_QUEUE_MEMCACHEQ_PORT;
        }
        return array('driverOptions' => $driverOptions);
    }

    // test the constants
    public function testConst()
    {
        /**
         * @see Zend_Queue_Adapter_Memcacheq
         */
        $this->assertTrue(is_string(Adapter\Memcacheq::DEFAULT_HOST));
        $this->assertTrue(is_integer(Adapter\Memcacheq::DEFAULT_PORT));
        $this->assertTrue(is_string(Adapter\Memcacheq::EOL));
    }
}
