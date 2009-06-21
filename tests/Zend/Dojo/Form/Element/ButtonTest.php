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

// Call Zend_Dojo_Form_Element_ButtonTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Element_ButtonTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_Form_Element_Button */
require_once 'Zend/Dojo/Form/Element/Button.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Translate */
require_once 'Zend/Translate.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_Form_Element_Dijit.
 *
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Dojo_Form_Element_ButtonTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Element_ButtonTest");
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

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
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

    public function getElement()
    {
        $element = new Zend_Dojo_Form_Element_Button('foo');
        return $element;
    }

    public function testGetLabelReturnsNameIfNoValuePresent()
    {
        $this->assertEquals($this->element->getName(), $this->element->getLabel());
    }

    public function testGetLabelReturnsTranslatedLabelIfTranslatorIsRegistered()
    {
        $translations = include dirname(__FILE__) . '/_files/locale/array.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $this->element->setTranslator($translate)
                      ->setLabel('submit');
        $test = $this->element->getLabel();
        $this->assertEquals($translations['submit'], $test);
    }

    public function testTranslatedLabelIsRendered()
    {
        $this->testGetLabelReturnsTranslatedLabelIfTranslatorIsRegistered();
        $this->element->setView($this->getView());
        $decorator = $this->element->getDecorator('DijitElement');
        $decorator->setElement($this->element);
        $html = $decorator->render('');
        $this->assertRegexp('/<(input|button)[^>]*?>Submit Button/', $html, 'Label: ' . $this->element->getLabel() . "\nHTML: " . $html);
    }

    public function testConstructorSetsLabelToNameIfNoLabelProvided()
    {
        $button = new Zend_Dojo_Form_Element_Button('foo');
        $this->assertEquals('foo', $button->getName());
        $this->assertEquals('foo', $button->getLabel());
    }

    public function testCanPassLabelAsParameterToConstructor()
    {
        $button = new Zend_Dojo_Form_Element_Button('foo', 'Label');
        $this->assertEquals('Label', $button->getLabel());
    }

    public function testLabelIsTranslatedWhenTranslationAvailable()
    {
        require_once 'Zend/Translate.php';
        $translations = array('Label' => 'This is the Submit Label');
        $translate = new Zend_Translate('array', $translations);
        $button = new Zend_Dojo_Form_Element_Button('foo', 'Label');
        $button->setTranslator($translate);
        $this->assertEquals($translations['Label'], $button->getLabel());
    }

    public function testIsCheckedReturnsFalseWhenNoValuePresent()
    {
        $this->assertFalse($this->element->isChecked());
    }

    public function testIsCheckedReturnsFalseWhenValuePresentButDoesNotMatchLabel()
    {
        $this->assertFalse($this->element->isChecked());
        $this->element->setValue('bar');
        $this->assertFalse($this->element->isChecked());
    }

    public function testIsCheckedReturnsTrueWhenValuePresentAndMatchesLabel()
    {
        $this->testIsCheckedReturnsFalseWhenNoValuePresent();
        $this->element->setValue('foo');
        $this->assertTrue($this->element->isChecked());
    }

    public function testShouldRenderButtonDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.Button"', $html);
    }

    /**
     * @group ZF-3961
     */
    public function testValuePropertyShouldNotBeRendered()
    {
        $this->element->setLabel('Button Label')
                      ->setView($this->getView());
        $html = $this->element->render();
        $this->assertContains('Button Label', $html, $html);
        $this->assertNotContains('value="', $html);
    }
}

// Call Zend_Dojo_Form_Element_ButtonTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Element_ButtonTest::main") {
    Zend_Dojo_Form_Element_ButtonTest::main();
}
