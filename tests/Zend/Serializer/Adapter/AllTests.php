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
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Serializer_Adapter_AllTests::main');
}

/**
 * @see Zend_Serializer_Adapter_PhpSerializeTest
 */
require_once dirname(__FILE__) . '/PhpSerializeTest.php';

/**
 * @see Zend_Serializer_Adapter_PhpCodeTest
 */
require_once dirname(__FILE__) . '/PhpCodeTest.php';

/**
 * @see Zend_Serializer_Adapter_JsonTest
 */
require_once dirname(__FILE__) . '/JsonTest.php';

/**
 * @see Zend_Serializer_Adapter_Amf0Test
 */
require_once dirname(__FILE__) . '/Amf0Test.php';

/**
 * @see Zend_Serializer_Adapter_Amf3Test
 */
require_once dirname(__FILE__) . '/Amf3Test.php';

/**
 * @see Zend_Serializer_Adapter_WddxTest
 */
require_once dirname(__FILE__) . '/WddxTest.php';

/**
 * @see Zend_Serializer_Adapter_IgbinaryTest
 */
require_once dirname(__FILE__) . '/IgbinaryTest.php';

/**
 * @see Zend_Serializer_Adapter_PythonPickleSerializeProtocol0Test
 */
require_once dirname(__FILE__) . '/PythonPickleSerializeProtocol0Test.php';

/**
 * @see Zend_Serializer_Adapter_PythonPickleSerializeProtocol1Test
 */
require_once dirname(__FILE__) . '/PythonPickleSerializeProtocol1Test.php';

/**
 * @see Zend_Serializer_Adapter_PythonPickleUnserializeTest
 */
require_once dirname(__FILE__) . '/PythonPickleUnserializeTest.php';

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Serializer_Adapter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend');

        $suite->addTestSuite('Zend_Serializer_Adapter_PhpSerializeTest');
        $suite->addTestSuite('Zend_Serializer_Adapter_PhpCodeTest');
        $suite->addTestSuite('Zend_Serializer_Adapter_JsonTest');
        $suite->addTestSuite('Zend_Serializer_Adapter_Amf0Test');
        $suite->addTestSuite('Zend_Serializer_Adapter_Amf3Test');

        if (!defined('TESTS_ZEND_SERIALIZER_ADAPTER_WDDX_ENABLED') || !TESTS_ZEND_SERIALIZER_ADAPTER_WDDX_ENABLED) {
            $skippedTest = new Zend_Serializer_Adapter_WddxSkipTest();
            $skippedTest->message = 'this Adapter is not enabled in TestConfiguration.php';
            $suite->addTest($skippedTest);
        } elseif (!extension_loaded('wddx')) {
            $skippedTest = new Zend_Serializer_Adapter_WddxSkipTest();
            $skippedTest->message = 'extension "wddx" is not loaded';
            $suite->addTest($skippedTest);
        } else {
            $suite->addTestSuite('Zend_Serializer_Adapter_WddxTest');
        }

        if (!defined('TESTS_ZEND_SERIALIZER_ADAPTER_IGBINARY_ENABLED') || !TESTS_ZEND_SERIALIZER_ADAPTER_IGBINARY_ENABLED) {
            $skippedTest = new Zend_Serializer_Adapter_IgbinarySkipTest();
            $skippedTest->message = 'this Adapter is not enabled in TestConfiguration.php';
            $suite->addTest($skippedTest);
        } elseif (!extension_loaded('igbinary')) {
            $skippedTest = new Zend_Serializer_Adapter_IgbinarySkipTest();
            $skippedTest->message = 'extension "igbinary" is not loaded';
            $suite->addTest($skippedTest);
        } else {
            $suite->addTestSuite('Zend_Serializer_Adapter_IgbinaryTest');
        }

        $suite->addTestSuite('Zend_Serializer_Adapter_PythonPickleSerializeProtocol0Test');
        $suite->addTestSuite('Zend_Serializer_Adapter_PythonPickleSerializeProtocol1Test');
        $suite->addTestSuite('Zend_Serializer_Adapter_PythonPickleUnserializeTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Serializer_Adapter_AllTests::main') {
    Zend_Serializer_Adapter_AllTests::main();
}
