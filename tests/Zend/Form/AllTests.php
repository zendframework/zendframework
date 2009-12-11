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
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_AllTests::main');
}

require_once 'Zend/Form/Decorator/AllTests.php';
require_once 'Zend/Form/DisplayGroupTest.php';
require_once 'Zend/Form/ElementTest.php';
require_once 'Zend/Form/Element/AllTests.php';
require_once 'Zend/Form/FormTest.php';

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Form');

        $suite->addTest(Zend_Form_Decorator_AllTests::suite());
        $suite->addTestSuite('Zend_Form_DisplayGroupTest');
        $suite->addTestSuite('Zend_Form_ElementTest');
        $suite->addTest(Zend_Form_Element_AllTests::suite());
        $suite->addTestSuite('Zend_Form_FormTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_AllTests::main') {
    Zend_Form_AllTests::main();
}
