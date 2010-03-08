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

// Call Zend_Dojo_View_Helper_ValidationTextBoxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_ValidationTextBoxTest::main");
}


/** Zend_Dojo_View_Helper_ValidationTextBox */

/** Zend_View */

/** Zend_Registry */

/** Zend_Dojo_View_Helper_Dojo */

/**
 * Test class for Zend_Dojo_View_Helper_ValidationTextBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_ValidationTextBoxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_View_Helper_ValidationTextBoxTest");
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
        $this->helper = new Zend_Dojo_View_Helper_ValidationTextBox();
        $this->helper->setView($this->view);
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
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getElement()
    {
        return $this->helper->validationTextBox(
            'elementId',
            '2008-07-07',
            array(
                'required'    => true,
                'regExp'      => '[\w]+',
            ),
            array()
        );
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(dojoType="dijit.form.ValidationTextBox")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<input[^>]*(dojoType="dijit.form.ValidationTextBox")/', $html);
        $this->assertNotNull($this->view->dojo()->getDijit('elementId'));
    }

    public function testShouldCreateTextInput()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(type="text")/', $html);
    }

    /**
     * @group ZF-7660
     */
    public function testTrueRequiredParameterShouldBeRenderedAsStringValue()
    {
        $html = $this->getElement();
        $this->assertContains('required="true"', $html);
    }

    /**
     * @group ZF-7660
     */
    public function testFalseRequiredParameterShouldBeRenderedAsStringValue()
    {
        $html = $this->helper->validationTextBox(
            'elementId',
            '2008-07-07',
            array(
                'required'    => false,
                'regExp'      => '[\w]+',
            ),
            array()
        );
        $this->assertContains('required="false"', $html);
    }
}

// Call Zend_Dojo_View_Helper_ValidationTextBoxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_ValidationTextBoxTest::main") {
    Zend_Dojo_View_Helper_ValidationTextBoxTest::main();
}
