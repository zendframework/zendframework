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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Queue_AllTests::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/Queue/FactoryTest.php';

// Queue testing
require_once 'Zend/Queue/Queue1Test.php'; // Zend_Queue_Adapter_Array
require_once 'Zend/Queue/Queue2Test.php'; // Zend_Queue_Adapter_Null

// Message testing
require_once 'Zend/Queue/MessageTest.php';
require_once 'Zend/Queue/Message/IteratorTest.php';

// Adapter testing
require_once 'Zend/Queue/Adapter/ArrayTest.php';
require_once 'Zend/Queue/Adapter/MemcacheqTest.php';
require_once 'Zend/Queue/Adapter/NullTest.php';
require_once 'Zend/Queue/Adapter/DbTest.php';

// Stomp protocol testing
require_once 'Zend/Queue/Stomp/FrameTest.php';
require_once 'Zend/Queue/Stomp/ClientTest.php';

// Message Queues dependent on Stomp
require_once 'Zend/Queue/Adapter/ApachemqTest.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */
class Zend_Queue_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Queue');

        $suite->addTestSuite('Zend_Queue_FactoryTest');

        // Queue testing
        $suite->addTestSuite('Zend_Queue_Queue1Test');   // Zend_Queue_Adapter_Array
        $suite->addTestSuite('Zend_Queue_Queue2Test');  // Zend_Queue_Adapter_Null

        // Message testing
        $suite->addTestSuite('Zend_Queue_MessageTest');
        $suite->addTestSuite('Zend_Queue_Message_IteratorTest');

        // Adapter testing
        $suite->addTestSuite('Zend_Queue_Adapter_ArrayTest');
        if (extension_loaded('memcache')) {
            $suite->addTestSuite('Zend_Queue_Adapter_MemcacheqTest');
        }
        $suite->addTestSuite('Zend_Queue_Adapter_DbTest');
        $suite->addTestSuite('Zend_Queue_Adapter_NullTest');

        // Stomp protocol testing
        $suite->addTestSuite('Zend_Queue_Stomp_FrameTest');
        $suite->addTestSuite('Zend_Queue_Stomp_ClientTest');

        // Message Queues dependent on Stomp
        $suite->addTestSuite('Zend_Queue_Adapter_ApachemqTest');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Queue_AllTests::main') {
    Zend_Queue_AllTests::main();
}
