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
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Nirvanix_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @see Zend_Service_Nirvanix_NirvanixTest
 */
require_once 'Zend/Service/Nirvanix/NirvanixTest.php';

/**
 * @see Zend_Service_Nirvanix_ExceptionTest
 */
require_once 'Zend/Service/Nirvanix/ExceptionTest.php';

/**
 * @see Zend_Service_Nirvanix_ResponseTest
 */
require_once 'Zend/Service/Nirvanix/ResponseTest.php';

/**
 * @see Zend_Service_Nirvanix_Namespace_BaseTest
 */
require_once 'Zend/Service/Nirvanix/Namespace/BaseTest.php';

/**
 * @see Zend_Service_Nirvanix_Namespace_ImfsTest
 */
require_once 'Zend/Service/Nirvanix/Namespace/ImfsTest.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Nirvanix_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_Nirvanix');

        $suite->addTestSuite('Zend_Service_Nirvanix_NirvanixTest');
        $suite->addTestSuite('Zend_Service_Nirvanix_ExceptionTest');
        $suite->addTestSuite('Zend_Service_Nirvanix_ResponseTest');
        $suite->addTestSuite('Zend_Service_Nirvanix_Namespace_BaseTest');
        $suite->addTestSuite('Zend_Service_Nirvanix_Namespace_ImfsTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_Nirvanix_AllTests::main') {
    Zend_Service_Nirvanix_AllTests::main();
}
