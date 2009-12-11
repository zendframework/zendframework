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
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Server_AllTests::main');
}

require_once 'Zend/Server/DefinitionTest.php';
require_once 'Zend/Server/Method/DefinitionTest.php';
require_once 'Zend/Server/Method/CallbackTest.php';
require_once 'Zend/Server/Method/PrototypeTest.php';

require_once 'Zend/Server/ReflectionTest.php';
require_once 'Zend/Server/Reflection/ClassTest.php';
require_once 'Zend/Server/Reflection/FunctionTest.php';
require_once 'Zend/Server/Reflection/MethodTest.php';
require_once 'Zend/Server/Reflection/NodeTest.php';
require_once 'Zend/Server/Reflection/ParameterTest.php';
require_once 'Zend/Server/Reflection/PrototypeTest.php';
require_once 'Zend/Server/Reflection/ReturnValueTest.php';

/**
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Server');

        $suite->addTestSuite('Zend_Server_DefinitionTest');
        $suite->addTestSuite('Zend_Server_Method_DefinitionTest');
        $suite->addTestSuite('Zend_Server_Method_CallbackTest');
        $suite->addTestSuite('Zend_Server_Method_PrototypeTest');
        $suite->addTestSuite('Zend_Server_ReflectionTest');
        $suite->addTestSuite('Zend_Server_Reflection_ClassTest');
        $suite->addTestSuite('Zend_Server_Reflection_FunctionTest');
        $suite->addTestSuite('Zend_Server_Reflection_MethodTest');
        $suite->addTestSuite('Zend_Server_Reflection_NodeTest');
        $suite->addTestSuite('Zend_Server_Reflection_ParameterTest');
        $suite->addTestSuite('Zend_Server_Reflection_PrototypeTest');
        $suite->addTestSuite('Zend_Server_Reflection_ReturnValueTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Server_AllTests::main') {
    Zend_Server_AllTests::main();
}
