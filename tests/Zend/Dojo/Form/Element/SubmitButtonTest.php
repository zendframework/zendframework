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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Dojo\Form\Element;

use Zend\Dojo\Form\Element\SubmitButton as SubmitButtonElement,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\Translator\Translator,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_SubmitButton.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class SubmitButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::_unsetInstance();
        DojoHelper::setUseDeclarative();

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        $element = new SubmitButtonElement('foo');
        return $element;
    }

    public function testGetLabelReturnsNameIfNoValuePresent()
    {
        $this->assertEquals($this->element->getName(), $this->element->getLabel());
    }

    public function testGetLabelReturnsTranslatedLabelIfTranslatorIsRegistered()
    {
        $translations = include __DIR__ . '/TestAsset/locale/array.php';
        $translate = new Translator('ArrayAdapter', $translations, 'en');
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
        $this->assertRegexp('/<(input|button)[^>]*?(value="Submit Button")/', $html, 'Label: ' . $this->element->getLabel() . "\nHTML: " . $html);
    }

    public function testConstructorSetsLabelToNameIfNoLabelProvided()
    {
        $button = new SubmitButtonElement('foo');
        $this->assertEquals('foo', $button->getName());
        $this->assertEquals('foo', $button->getLabel());
    }

    public function testCanPassLabelAsParameterToConstructor()
    {
        $button = new SubmitButtonElement('foo', 'Label');
        $this->assertEquals('Label', $button->getLabel());
    }

    public function testLabelIsTranslatedWhenTranslationAvailable()
    {
        $translations = array('Label' => 'This is the Submit Label');
        $translate = new Translator('ArrayAdapter', $translations);
        $button = new SubmitButtonElement('foo', 'Label');
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

    public function testShouldRenderSubmitInput()
    {
        $html = $this->element->render();
        $this->assertContains('type="submit"', $html);
    }

    /**
     * @group ZF-4977
     */
    public function testElementShouldRenderLabelAsInputValue()
    {
        $this->element->setLabel('Label!');
        $html = $this->element->render();
        $this->assertRegexp('/<input[^>]*(value="Label!")/', $html, $html);
    }
}
