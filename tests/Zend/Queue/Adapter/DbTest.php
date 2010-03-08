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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/** TestHelp.php */

/** Zend_Queue */

/** Zend_Queue */

/** Zend_Queue_Message_Test */

/** Base Adapter test class */

/**
 * @see Zend_Db_Select
 */

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class Zend_Queue_Adapter_DbTest extends Zend_Queue_Adapter_AdapterTest
{
    protected function setUp()
    {
        date_default_timezone_set('GMT');
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
        return 'Zend_Queue_Adapter_' . $this->getAdapterName();
    }

    public function getTestConfig()
    {
        $driverOptions = array();
        if (defined('TESTS_ZEND_QUEUE_DB')) {
            $driverOptions = Zend_Json::decode(TESTS_ZEND_QUEUE_DB);
        }

        return array(
            'options'       => array(Zend_Db_Select::FOR_UPDATE => true),
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
            $config['options'][Zend_Db_Select::FOR_UPDATE] = array();
            $queue = $this->createQueue(__FUNCTION__, $config);
            $this->fail('FOR_UPDATE accepted an array');
        } catch (Exception $e) {
            $this->assertTrue(true, 'FOR_UPDATE cannot be an array');
        }

        foreach (array('host', 'username', 'password', 'dbname') as $i => $arg) {
            try {
                $config = $this->getTestConfig();
                unset($config['driverOptions'][$arg]);
                $queue = $this->createQueue(__FUNCTION__, $config);
                $this->fail("$arg is required but was missing.");
            } catch (Exception $e) {
                $this->assertTrue(true, $arg . ' is required.');
            }
        }
    }
}

