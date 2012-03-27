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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use Zend\Form\Element\Submit as SubmitElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\Form\Decorator,
    Zend\Form\Form,
    Zend\Registry,
    Zend\Translator\Translator,
    Zend\Translator\Adapter\ArrayAdapter as ArrayTranslator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_Submit
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class SubmitTest extends \PHPUnit_Framework_TestCase
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
        Form::setDefaultTranslator(null);
        $this->element = new SubmitElement('foo');
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testSubmitElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof XhtmlElement);
    }

    public function testSubmitElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Element);
    }

    public function testSubmitElementUsesViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Decorator\ViewHelper);
    }

    public function testSubmitElementSpecifiesFormSubmitAsDefaultHelper()
    {
        $this->assertEquals('formSubmit', $this->element->helper);
    }

    public function testGetLabelReturnsNameIfNoValuePresent()
    {
        $this->assertEquals($this->element->getName(), $this->element->getLabel());
    }

    public function testGetLabelReturnsTranslatedLabelIfTranslatorIsRegistered()
    {
        $translations = include __DIR__ . '/../TestAsset/locale/array.php';
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
        $decorator = $this->element->getDecorator('ViewHelper');
        $decorator->setElement($this->element);
        $html = $decorator->render('');
        $this->assertRegexp('/<(input|button)[^>]*?value="Submit Button"/', $html);
    }

    public function testConstructorSetsLabelToNameIfNoLabelProvided()
    {
        $submit = new SubmitElement('foo');
        $this->assertEquals('foo', $submit->getName());
        $this->assertEquals('foo', $submit->getLabel());
    }

    public function testCanPassLabelAsParameterToConstructor()
    {
        $submit = new SubmitElement('foo', 'Label');
        $this->assertEquals('Label', $submit->getLabel());
    }

    public function testLabelIsTranslatedWhenTranslationAvailable()
    {
        $translations = array('Label' => 'This is the Submit Label');
        $translate = new Translator('ArrayAdapter', $translations);
        $submit = new SubmitElement('foo', 'Label');
        $submit->setTranslator($translate);
        $this->assertEquals($translations['Label'], $submit->getLabel());
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

    /**
     * Tests that the isChecked method works as expected when using a translator.
     * @group ZF-4073
     */
    public function testIsCheckedReturnsExpectedValueWhenUsingTranslator()
    {
        $translations = array('label' => 'translation');
        $translate = new Translator('ArrayAdapter', $translations);

        $submit = new SubmitElement('foo', 'label');
        $submit->setTranslator($translate);
        $submit->setValue($translations['label']);
        
        $this->assertTrue($submit->isChecked());

        $submit->setValue('label');
        $this->assertFalse($submit->isChecked());
    }

    /*
     * Tests if title attribute (tooltip) is translated if the default decorators are loaded.
     * These decorators should load the Tooltip decorator as the first decorator.
     * @group ZF-6151
     */
    public function testTitleAttributeGetsTranslated()
    {
        $this->element->setAttrib('title', 'bar');
        $translator = new ArrayTranslator(array("bar" => "baz"), 'de');
        $this->element->setTranslator($translator);
        $html = $this->element->render(new View());
        $this->assertContains('title', $html);
        $this->assertContains('baz', $html);
        $this->assertNotContains('bar', $html);
    }

    public function testTitleAttributeDoesNotGetTranslatedIfTranslatorIsDisabled()
    {
        $this->element->setAttrib('title', 'bar');
        $translator = new ArrayTranslator(array("bar" => "baz"), 'de');
        $this->element->setTranslator($translator);
        // now disable translator and see if that works
        $this->element->setDisableTranslator(true);
        $html = $this->element->render(new View());
        $this->assertContains('title', $html);
        $this->assertContains('bar', $html);
        $this->assertNotContains('baz', $html);
    }
    
    public function testSetDefaultIgnoredToTrueWhenNotDefined()
    {
        $this->assertTrue($this->element->getIgnore());
    }
    
    /**
     * Prove the fluent interface on Zend_Form_Element_Submit::loadDefaultDecorators
     *
     * @group ZF-9913
     * @return void
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->element, $this->element->loadDefaultDecorators());
    }
}
