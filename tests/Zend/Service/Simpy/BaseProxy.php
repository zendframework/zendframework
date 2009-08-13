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
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: SimpyTest.php 11973 2008-10-15 16:00:56Z matthew $
 */

/**
 * @see Zend_Service_Simpy
 */
require_once 'Zend/Service/Simpy.php';

/**
 * @category   Zend
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Service_Simpy_BaseProxy
{
    /**
     * Simpy service consumer
     *
     * @var Zend_Service_Simpy
     */
    protected $_simpy;

    /**
     * Name of the current test case being executed
     *
     * @var string
     */
    protected $_test;

    /**
     * Mapping of methods to the number of calls made per method for the 
     * current test case being executed
     *
     * @var array
     */
    protected $_calls;

    /**
     * Constructor to initialize the service consumer.
     *
     * @param string $test Name of the test case currently being executed
     * @return void
     */
    public final function __construct($test)
    {
        $this->_test = $test;
        $this->_calls = array();

        if (defined('TESTS_ZEND_SERVICE_SIMPY_USERNAME')) {
            $username = TESTS_ZEND_SERVICE_SIMPY_USERNAME;
            $password = TESTS_ZEND_SERVICE_SIMPY_PASSWORD;
        } else {
            $username = null;
            $password = null;
        }

        $this->_simpy = new Zend_Service_Simpy($username, $password);

        $this->init();
    }

    /**
     * Initialize method to be overridden in subclasses if needed.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Returns the path to the file intended to contain the service consumer 
     * response for the current method call.
     *
     * @param string $name Name of the method being called
     * @return string File path
     */
    protected function _getFilePath($name)
    {
        if (!isset($this->_calls[$name])) {
            $this->_calls[$name] = 0;
        }

        $this->_calls[$name]++;

        $dir = dirname(__FILE__) . '/_files/';
        $file = $this->_test . '_' . $name . '_' . $this->_calls[$name];

        return $dir . $file;
    }
}
