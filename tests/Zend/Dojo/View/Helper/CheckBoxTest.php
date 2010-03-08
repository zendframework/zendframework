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

// Call Zend_Dojo_View_Helper_CheckBoxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_CheckBoxTest::main");
}


/** Zend_Dojo_View_Helper_CheckBox */

/** Zend_View */

/** Zend_Registry */

/** Zend_Dojo_View_Helper_Dojo */

/**
 * Test class for Zend_Dojo_View_Helper_CheckBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_CheckBoxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_View_Helper_CheckBoxTest");
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
        $this->helper = new Zend_Dojo_View_Helper_CheckBox();
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
        return $this->helper->checkBox(
            'elementId',
            'foo',
            array(),
            array(),
            array(
                'checked'   => 'foo',
                'unChecked' => 'bar',
            )
        );
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(dojoType="dijit.form.CheckBox")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<input[^>]*(dojoType="dijit.form.CheckBox")/', $html);
        $this->assertNotNull($this->view->dojo()->getDijit('elementId'));
    }

    public function testShouldCreateHiddenElementWithUncheckedValue()
    {
        $html = $this->getElement();
        if (!preg_match('/(<input[^>]*(type="hidden")[^>]*>)/s', $html, $m)) {
            $this->fail('Missing hidden element with unchecked value');
        }
        $this->assertContains('value="bar"', $m[1]);
    }

    public function testShouldCheckElementWhenValueMatchesCheckedValue()
    {
        $html = $this->getElement();
        if (!preg_match('/(<input[^>]*(type="checkbox")[^>]*>)/s', $html, $m)) {
            $this->fail('Missing checkbox element: ' . $html);
        }
        $this->assertContains('checked="checked"', $m[1]);
    }

    /**
     * @see ZF-4006
     * @group ZF-4006
     */
    public function testElementShouldUseCheckedValueForCheckboxInput()
    {
        $html = $this->helper->checkBox('foo', '0', array(), array(), array(
            'checkedValue'   => '1',
            'unCheckedValue' => '0',
        ));
        if (!preg_match('#(<input[^>]*(?:type="checkbox")[^>]*>)#s', $html, $matches)) {
            $this->fail('Did not find checkbox in html: ' . $html);
        }
        $this->assertContains('value="1"', $matches[1]);
        $this->assertNotContains('checked', $matches[1]);
    }

    /**
     * @group ZF-3878
     */
    public function testElementShouldCreateAppropriateIdWhenNameIncludesArrayNotation()
    {
        $html = $this->helper->checkBox('foo[bar]', '0');
        $this->assertContains('id="foo-bar"', $html);
    }
}

// Call Zend_Dojo_View_Helper_CheckBoxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_CheckBoxTest::main") {
    Zend_Dojo_View_Helper_CheckBoxTest::main();
}
