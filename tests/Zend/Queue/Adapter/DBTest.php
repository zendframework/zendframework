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

use Zend\Db\Select;

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
class DBTest extends AdapterTest
{
    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    protected function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->_originaltimezone);
    }

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
        return 'Db';
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
        if (defined('TESTS_ZEND_QUEUE_DB')) {
            $driverOptions = \Zend\Json\Json::decode(TESTS_ZEND_QUEUE_DB);
        }

        return array(
            'options'       => array(Select::FOR_UPDATE => true),
            'driverOptions' => $driverOptions,
        );
    }

    // test the constants
    public function testConst()
    {
        $this->markTestSkipped('no constants to test');
    }

    // additional non-standard tests

    public function test_constructor2()
    {
        try {
            $config = $this->getTestConfig();
            /**
             * @see Zend_Db_Select
             */
            $config['options'][Select::FOR_UPDATE] = array();
            $queue = $this->createQueue(__FUNCTION__, $config);
            $this->fail('FOR_UPDATE accepted an array');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'FOR_UPDATE cannot be an array');
        }

        foreach (array('host', 'username', 'password', 'dbname') as $i => $arg) {
            try {
                $config = $this->getTestConfig();
                unset($config['driverOptions'][$arg]);
                $queue = $this->createQueue(__FUNCTION__, $config);
                $this->fail("$arg is required but was missing.");
            } catch (\Exception $e) {
                $this->assertTrue(true, $arg . ' is required.');
            }
        }
    }
}

