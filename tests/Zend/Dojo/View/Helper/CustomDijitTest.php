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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_View_Helper_CustomDijitTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_CustomDijitTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_View_Helper_CustomDijit */
require_once 'Zend/Dojo/View/Helper/CustomDijit.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_View_Helper_CustomDijit
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_CustomDijitTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Zend_Registry::_unsetInstance();
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative();

        $this->view   = $this->getView();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    /**
     * @expectedException Zend_Dojo_View_Exception
     */
    public function testHelperShouldRaiseExceptionIfNoDojoTypePassed()
    {
        $this->view->customDijit('foo');
    }

    public function testHelperInDeclarativeModeShouldGenerateDivWithPassedDojoType()
    {
        $content = $this->view->customDijit('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $this->assertContains('dojoType="custom.Dijit"', $content);
    }

    public function testHelperInDeclarativeModeShouldRegisterDojoTypeAsModule()
    {
        $content = $this->view->customDijit('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $dojo    = $this->view->dojo();
        $modules = $dojo->getModules();
        $this->assertContains('custom.Dijit', $modules);
    }

    public function testHelperInProgrammaticModeShouldRegisterDojoTypeAsModule()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $content = $this->view->customDijit('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $dojo    = $this->view->dojo();
        $modules = $dojo->getModules();
        $this->assertContains('custom.Dijit', $modules);
    }

    public function testHelperInProgrammaticModeShouldGenerateDivWithoutPassedDojoType()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $content = $this->view->customDijit('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $this->assertNotContains('dojoType="custom.Dijit"', $content);
    }

    public function testHelperShouldAllowCapturingContent()
    {
        $this->view->customDijit()->captureStart('foo', array('dojoType' => 'custom.Dijit'));
        echo "Captured content started\n";
        $content = $this->view->customDijit()->captureEnd('foo');
        $this->assertContains(">Captured content started\n<", $content);
    }

    public function testUsesDefaultDojoTypeWhenPresent()
    {
        $helper = new Zend_Dojo_View_Helper_CustomDijitTest_FooContentPane();
        $helper->setView($this->view);
        $content = $helper->fooContentPane('foo');
        $this->assertContains('dojoType="foo.ContentPane"', $content);
    }

    public function testCapturingUsesDefaultDojoTypeWhenPresent()
    {
        $helper = new Zend_Dojo_View_Helper_CustomDijitTest_FooContentPane();
        $helper->setView($this->view);
        $helper->fooContentPane()->captureStart('foo');
        echo "Captured content started\n";
        $content = $helper->fooContentPane()->captureEnd('foo');
        $this->assertContains(">Captured content started\n<", $content);
        $this->assertContains('dojoType="foo.ContentPane"', $content);
    }

    /**
     * @group ZF-7890
     */
    public function testHelperShouldAllowSpecifyingRootNode()
    {
        $content = $this->view->customDijit('foo', 'content', array(
            'dojoType' => 'custom.Dijit',
            'rootNode' => 'select',
        ));
        $this->assertContains('<select', $content);
    }
}

class Zend_Dojo_View_Helper_CustomDijitTest_FooContentPane
    extends Zend_Dojo_View_Helper_CustomDijit
{
    protected $_defaultDojoType = 'foo.ContentPane';

    public function fooContentPane($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        return $this->customDijit($id, $value, $params, $attribs);
    }
}

// Call Zend_Dojo_View_Helper_CustomDijitTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_CustomDijitTest::main") {
    Zend_Dojo_View_Helper_CustomDijitTest::main();
}
