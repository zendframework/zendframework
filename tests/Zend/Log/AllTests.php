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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Log_AllTests::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/Log/LogTest.php';
require_once 'Zend/Log/Filter/ChainingTest.php';
require_once 'Zend/Log/Filter/PriorityTest.php';
require_once 'Zend/Log/Filter/MessageTest.php';
require_once 'Zend/Log/Filter/SuppressTest.php';
require_once 'Zend/Log/Formatter/SimpleTest.php';
require_once 'Zend/Log/Formatter/XmlTest.php';
require_once 'Zend/Log/Writer/DbTest.php';
if (PHP_OS != 'AIX') {
    require_once 'Zend/Log/Writer/FirebugTest.php';
}
require_once 'Zend/Log/Writer/MailTest.php';
require_once 'Zend/Log/Writer/MockTest.php';
require_once 'Zend/Log/Writer/NullTest.php';
require_once 'Zend/Log/Writer/StreamTest.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Log_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        // hack to allow us to view code coverage for Log.php
        PHPUnit_Util_Filter::removeFileFromFilter(dirname(__FILE__) . '/../../../library/Zend/Log.php', 'PEAR');

        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Log');

        $suite->addTestSuite('Zend_Log_LogTest');
        $suite->addTestSuite('Zend_Log_Filter_ChainingTest');
        $suite->addTestSuite('Zend_Log_Filter_PriorityTest');
        $suite->addTestSuite('Zend_Log_Filter_MessageTest');
        $suite->addTestSuite('Zend_Log_Filter_SuppressTest');
        $suite->addTestSuite('Zend_Log_Formatter_SimpleTest');
        $suite->addTestSuite('Zend_Log_Formatter_XmlTest');
        $suite->addTestSuite('Zend_Log_Writer_DbTest');
        if (PHP_OS != 'AIX') {
            $suite->addTestSuite('Zend_Log_Writer_FirebugTest');
        }
        $suite->addTestSuite('Zend_Log_Writer_MailTest');
        $suite->addTestSuite('Zend_Log_Writer_MockTest');
        $suite->addTestSuite('Zend_Log_Writer_NullTest');
        $suite->addTestSuite('Zend_Log_Writer_StreamTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Log_AllTests::main') {
    Zend_Log_AllTests::main();
}
