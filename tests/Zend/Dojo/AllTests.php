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
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Dojo_AllTests::main');
}

require_once 'Zend/Dojo/BuildLayerTest.php';
require_once 'Zend/Dojo/DojoTest.php';
require_once 'Zend/Dojo/DataTest.php';
require_once 'Zend/Dojo/Form/AllTests.php';
require_once 'Zend/Dojo/View/AllTests.php';

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Dojo_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Dojo');

        $suite->addTestSuite('Zend_Dojo_BuildLayerTest');
        $suite->addTestSuite('Zend_Dojo_DojoTest');
        $suite->addTestSuite('Zend_Dojo_DataTest');
        $suite->addTest(Zend_Dojo_Form_AllTests::suite());
        $suite->addTest(Zend_Dojo_View_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Dojo_AllTests::main') {
    Zend_Dojo_AllTests::main();
}
