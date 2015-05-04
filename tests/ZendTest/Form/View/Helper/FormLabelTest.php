<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\View\Helper\FormLabel as FormLabelHelper;

class FormLabelTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormLabelHelper();
        parent::setUp();
    }

    public function testCanEmitStartTagOnly()
    {
        $markup = $this->helper->openTag();
        $this->assertEquals('<label>', $markup);
    }

    public function testOpenTagWithWrongElementRaisesException()
    {
        $element = new \arrayObject();
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException', 'ArrayObject');
        $this->helper->openTag($element);
    }

    public function testPassingArrayToOpenTagRendersAttributes()
    {
        $attributes = array(
            'class'     => 'email-label',
            'data-type' => 'label',
        );
        $markup = $this->helper->openTag($attributes);

        foreach ($attributes as $key => $value) {
            $this->assertContains(sprintf('%s="%s"', $key, $value), $markup);
        }
    }

    public function testCanEmitCloseTagOnly()
    {
        $markup = $this->helper->closeTag();
        $this->assertEquals('</label>', $markup);
    }

    public function testPassingElementToOpenTagWillUseNameInForAttributeIfNoIdPresent()
    {
        $element = new Element('foo');
        $markup = $this->helper->openTag($element);
        $this->assertContains('for="foo"', $markup);
    }

    public function testPassingElementToOpenTagWillUseIdInForAttributeWhenPresent()
    {
        $element = new Element('foo');
        $element->setAttribute('id', 'bar');
        $markup = $this->helper->openTag($element);
        $this->assertContains('for="bar"', $markup);
    }

    public function testPassingElementToInvokeWillRaiseExceptionIfNoNameOrIdAttributePresent()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'id');
        $markup = $this->helper->__invoke($element);
    }

    public function testPassingElementToInvokeWillRaiseExceptionIfNoLabelAttributePresent()
    {
        $element = new Element('foo');
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'label');
        $markup = $this->helper->__invoke($element);
    }

    public function testPassingElementToInvokeGeneratesLabelMarkup()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->__invoke($element);
        $this->assertContains('>The value for foo:<', $markup);
        $this->assertContains('for="foo"', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testPassingElementAndContentToInvokeUsesContentForLabel()
    {
        $element = new Element('foo');
        $markup = $this->helper->__invoke($element, 'The value for foo:');
        $this->assertContains('>The value for foo:<', $markup);
        $this->assertContains('for="foo"', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testPassingElementAndContentAndFlagToInvokeUsesLabelAttribute()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->__invoke($element, '<input type="text" id="foo" />', FormLabelHelper::PREPEND);
        $this->assertContains('>The value for foo:<input', $markup);
        $this->assertContains('for="foo"', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('></label>', $markup);
        $this->assertContains('<input type="text" id="foo" />', $markup);
    }

    public function testCanAppendLabelContentUsingFlagToInvoke()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->__invoke($element, '<input type="text" id="foo" />', FormLabelHelper::APPEND);
        $this->assertContains('"foo" />The value for foo:</label>', $markup);
        $this->assertContains('for="foo"', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('><input type="text" id="foo" />', $markup);
    }

    public function testsetLabelAttributes()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $element->setLabelAttributes(array('id' => 'bar'));
        $markup = $this->helper->__invoke($element, '<input type="text" id="foo" />', FormLabelHelper::APPEND);
        $this->assertContains('"foo" />The value for foo:</label>', $markup);
        $this->assertContains('id="bar" for="foo"', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('><input type="text" id="foo" />', $markup);
    }

    public function testPassingElementAndContextAndFlagToInvokeRaisesExceptionForMissingLabelAttribute()
    {
        $element = new Element('foo');
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'label');
        $markup = $this->helper->__invoke($element, '<input type="text" id="foo" />', FormLabelHelper::APPEND);
    }

    public function testCallingFromViewHelperCanHandleOpenTagAndCloseTag()
    {
        $helper = $this->helper;
        $markup = $helper()->openTag();
        $this->assertEquals('<label>', $markup);
        $markup = $helper()->closeTag();
        $this->assertEquals('</label>', $markup);
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

    public function testLabelIsEscapedByDefault()
    {
        $element = new Element('foo');
        $element->setLabel('The value <a>for</a> foo:');
        $markup = $this->helper->__invoke($element);
        $this->assertNotContains('<a>for</a>', $markup);
    }

    public function testCanDisableLabelHtmlEscape()
    {
        $element = new Element('foo');
        $element->setLabel('The value <a>for</a> foo:');
        $element->setLabelOptions(array('disable_html_escape' => true));
        $markup = $this->helper->__invoke($element);
        $this->assertContains('<a>for</a>', $markup);
    }

    public function testAlwaysWrapIsDisabledByDefault()
    {
        $element = new Element('foo');
        $this->assertEmpty($element->getLabelOption('always_wrap'));
    }

    public function testCanSetAlwaysWrap()
    {
        $element = new Element('foo');
        $element->setLabelOption('always_wrap', true);
        $this->assertTrue($element->getLabelOption('always_wrap'));
    }
}
