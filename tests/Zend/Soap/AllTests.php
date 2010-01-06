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
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Soap_AllTests::main');
}

require_once 'Zend/Soap/ClientTest.php';
require_once 'Zend/Soap/ServerTest.php';
require_once 'Zend/Soap/WsdlTest.php';
require_once "Zend/Soap/Wsdl/ArrayOfTypeComplexStrategyTest.php";
require_once "Zend/Soap/Wsdl/ArrayOfTypeSequenceStrategyTest.php";
require_once 'Zend/Soap/AutoDiscoverTest.php';
require_once 'Zend/Soap/AutoDiscover/OnlineTest.php';

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 */
class Zend_Soap_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Soap');

        $suite->addTestSuite('Zend_Soap_ClientTest');
        $suite->addTestSuite('Zend_Soap_ServerTest');
        $suite->addTestSuite('Zend_Soap_WsdlTest');
        $suite->addTestSuite('Zend_Soap_Wsdl_ArrayOfTypeComplexStrategyTest');
        $suite->addTestSuite('Zend_Soap_Wsdl_ArrayOfTypeSequenceStrategyTest');
        $suite->addTestSuite('Zend_Soap_AutoDiscoverTest');
        $suite->addTestSuite('Zend_Soap_AutoDiscover_OnlineTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Soap_AllTests::main') {
    Zend_Soap_AllTests::main();
}
