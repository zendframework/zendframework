<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\View\Helper\FormButton as FormButtonHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormButtonTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormButtonHelper();
        parent::setUp();
    }

    public function testCanEmitStartTagOnly()
    {
        $markup = $this->helper->openTag();
        $this->assertEquals('<button>', $markup);
    }

    public function testPassingArrayToOpenTagRendersAttributes()
    {
        $attributes = array(
            'name'  => 'my-button',
            'class' => 'email-button',
            'type'  => 'button',
        );
        $markup = $this->helper->openTag($attributes);

        foreach ($attributes as $key => $value) {
            $this->assertContains(sprintf('%s="%s"', $key, $value), $markup);
        }
    }

    public function testCanEmitCloseTagOnly()
    {
        $markup = $this->helper->closeTag();
        $this->assertEquals('</button>', $markup);
    }

    public function testPassingElementToOpenTagWillUseNameAttribute()
    {
        $element = new Element('foo');
        $markup = $this->helper->openTag($element);
        $this->assertContains('name="foo"', $markup);
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElementWhenPassedToOpenTag()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->openTag($element);
    }

    public function testOpenTagWithWrongElementRaisesException()
    {
        $element = new \arrayObject();
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException', 'ArrayObject');
        $this->helper->openTag($element);
    }

    public function testGeneratesSubmitTypeWhenProvidedAnElementWithNoTypeAttribute()
    {
        $element = new Element('foo');
        $markup  = $this->helper->openTag($element);
        $this->assertContains('<button ', $markup);
        $this->assertContains('type="submit"', $markup);
    }

    public function testGeneratesButtonTagWithElementsTypeAttribute()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'button');
        $markup  = $this->helper->openTag($element);
        $this->assertContains('<button ', $markup);
        $this->assertContains('type="button"', $markup);
    }

    public function inputTypes()
    {
        return array(
            array('submit', 'assertContains'),
            array('button', 'assertContains'),
            array('reset', 'assertContains'),
            array('lunar', 'assertNotContains'),
            array('name', 'assertNotContains'),
            array('username', 'assertNotContains'),
            array('text', 'assertNotContains'),
            array('checkbox', 'assertNotContains'),
        );
    }

    /**
     * @dataProvider inputTypes
     */
    public function testOpenTagOnlyAllowsValidButtonTypes($type, $assertion)
    {
        $element = new Element('foo');
        $element->setAttribute('type', $type);
        $markup   = $this->helper->openTag($element);
        $expected = sprintf('type="%s"', $type);
        $this->$assertion($expected, $markup);
    }

    public function validAttributes()
    {
        return array(
            array('name', 'assertContains'),
            array('accept', 'assertNotContains'),
            array('alt', 'assertNotContains'),
            array('autocomplete', 'assertNotContains'),
            array('autofocus', 'assertContains'),
            array('checked', 'assertNotContains'),
            array('dirname', 'assertNotContains'),
            array('disabled', 'assertContains'),
            array('form', 'assertContains'),
            array('formaction', 'assertContains'),
            array('formenctype', 'assertContains'),
            array('formmethod', 'assertContains'),
            array('formnovalidate', 'assertContains'),
            array('formtarget', 'assertContains'),
            array('height', 'assertNotContains'),
            array('list', 'assertNotContains'),
            array('max', 'assertNotContains'),
            array('maxlength', 'assertNotContains'),
            array('min', 'assertNotContains'),
            array('multiple', 'assertNotContains'),
            array('pattern', 'assertNotContains'),
            array('placeholder', 'assertNotContains'),
            array('readonly', 'assertNotContains'),
            array('required', 'assertNotContains'),
            array('size', 'assertNotContains'),
            array('src', 'assertNotContains'),
            array('step', 'assertNotContains'),
            array('value', 'assertContains'),
            array('width', 'assertNotContains'),
        );
    }

    public function getCompleteElement()
    {
        $element = new Element('foo');
        $element->setAttributes(array(
            'accept'             => 'value',
            'alt'                => 'value',
            'autocomplete'       => 'on',
            'autofocus'          => 'autofocus',
            'checked'            => 'checked',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'form'               => 'value',
            'formaction'         => 'value',
            'formenctype'        => 'value',
            'formmethod'         => 'value',
            'formnovalidate'     => 'value',
            'formtarget'         => 'value',
            'height'             => 'value',
            'id'                 => 'value',
            'list'               => 'value',
            'max'                => 'value',
            'maxlength'          => 'value',
            'min'                => 'value',
            'multiple'           => 'multiple',
            'name'               => 'value',
            'pattern'            => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'size'               => 'value',
            'src'                => 'value',
            'step'               => 'value',
            'width'              => 'value',
        ));
        $element->setValue('value');
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $element->setLabel('{button_content}');
        $markup  = $this->helper->render($element);
        switch ($attribute) {
            case 'value':
                $expect  = sprintf('%s="%s"', $attribute, $element->getValue());
                break;
            default:
                $expect  = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
                break;
        }
        $this->$assertion($expect, $markup);
    }

    public function testRaisesExceptionWhenLabelAttributeIsNotPresentInElement()
    {
        $element = new Element('foo');
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'label');
        $markup = $this->helper->render($element);
    }

    public function testPassingElementToRenderGeneratesButtonMarkup()
    {
        $element = new Element('foo');
        $element->setLabel('{button_content}');
        $markup = $this->helper->render($element);
        $this->assertContains('>{button_content}<', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('<button', $markup);
        $this->assertContains('</button>', $markup);
    }

    public function testPassingElementAndContentToRenderUsesContent()
    {
        $element = new Element('foo');
        $markup = $this->helper->render($element, '{button_content}');
        $this->assertContains('>{button_content}<', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('<button', $markup);
        $this->assertContains('</button>', $markup);
    }

    public function testCallingFromViewHelperCanHandleOpenTagAndCloseTag()
    {
        $helper = $this->helper;
        $markup = $helper()->openTag();
        $this->assertEquals('<button>', $markup);
        $markup = $helper()->closeTag();
        $this->assertEquals('</button>', $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element, '{button_content}');
        $this->assertContains('<button', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('>{button_content}<', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testDoesNotThrowExceptionIfNameIsZero()
    {
        $element = new Element(0);
        $markup = $this->helper->__invoke($element, '{button_content}');
        $this->assertContains('name="0"', $markup);
    }

    public function testCanTranslateContent()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');

        $mockTranslator = $this->getMock('Zend\I18n\Translator\Translator');
        $mockTranslator->expects($this->exactly(1))
            ->method('translate')
            ->will($this->returnValue('translated content'));

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);
        $this->assertContains('>translated content<', $markup);
    }

    public function testTranslatorMethods()
    {
        $translatorMock = $this->getMock('Zend\I18n\Translator\Translator');
        $this->helper->setTranslator($translatorMock, 'foo');

        $this->assertEquals($translatorMock, $this->helper->getTranslator());
        $this->assertEquals('foo', $this->helper->getTranslatorTextDomain());
        $this->assertTrue($this->helper->hasTranslator());
        $this->assertTrue($this->helper->isTranslatorEnabled());

        $this->helper->setTranslatorEnabled(false);
        $this->assertFalse($this->helper->isTranslatorEnabled());
    }
}
