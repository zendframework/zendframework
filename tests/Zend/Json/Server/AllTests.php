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
 * @package    Zend_Json
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Json_Server_AllTests::main');
}

require_once 'Zend/Json/Server/CacheTest.php';
require_once 'Zend/Json/Server/ErrorTest.php';
require_once 'Zend/Json/Server/RequestTest.php';
require_once 'Zend/Json/Server/ResponseTest.php';
require_once 'Zend/Json/Server/SmdTest.php';
require_once 'Zend/Json/Server/Smd/ServiceTest.php';

/**
 * @category   Zend
 * @package    Zend_Json
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Json
 */
class Zend_Json_Server_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Json_Server');

        $suite->addTestSuite('Zend_Json_Server_CacheTest');
        $suite->addTestSuite('Zend_Json_Server_ErrorTest');
        $suite->addTestSuite('Zend_Json_Server_RequestTest');
        $suite->addTestSuite('Zend_Json_Server_ResponseTest');
        $suite->addTestSuite('Zend_Json_Server_SmdTest');
        $suite->addTestSuite('Zend_Json_Server_Smd_ServiceTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Json_Server_AllTests::main') {
    Zend_Json_Server_AllTests::main();
}
