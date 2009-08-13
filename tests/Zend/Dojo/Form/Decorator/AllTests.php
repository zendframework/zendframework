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
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Dojo_Form_Decorator_AllTests::main');
}

require_once 'Zend/Dojo/Form/Decorator/AccordionContainerTest.php';
require_once 'Zend/Dojo/Form/Decorator/AccordionPaneTest.php';
require_once 'Zend/Dojo/Form/Decorator/BorderContainerTest.php';
require_once 'Zend/Dojo/Form/Decorator/DijitContainerTest.php';
require_once 'Zend/Dojo/Form/Decorator/DijitElementTest.php';
require_once 'Zend/Dojo/Form/Decorator/DijitFormTest.php';
require_once 'Zend/Dojo/Form/Decorator/SplitContainerTest.php';
require_once 'Zend/Dojo/Form/Decorator/StackContainerTest.php';
require_once 'Zend/Dojo/Form/Decorator/TabContainerTest.php';

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Decorator_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Dojo_Form_Decorator');

        $suite->addTestSuite('Zend_Dojo_Form_Decorator_AccordionContainerTest');
        $suite->addTestSuite('Zend_Dojo_Form_Decorator_AccordionPaneTest');
        $suite->addTestSuite('Zend_Dojo_Form_Decorator_BorderContainerTest');
        $suite->addTestSuite('Zend_Dojo_Form_Decorator_DijitContainerTest');
        $suite->addTestSuite('Zend_Dojo_Form_Decorator_DijitElementTest');
        $suite->addTestSuite('Zend_Dojo_Form_Decorator_DijitFormTest');
        $suite->addTestSuite('Zend_Dojo_Form_Decorator_SplitContainerTest');
        $suite->addTestSuite('Zend_Dojo_Form_Decorator_StackContainerTest');
        $suite->addTestSuite('Zend_Dojo_Form_Decorator_TabContainerTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Dojo_Form_Decorator_AllTests::main') {
    Zend_Dojo_Form_Decorator_AllTests::main();
}
