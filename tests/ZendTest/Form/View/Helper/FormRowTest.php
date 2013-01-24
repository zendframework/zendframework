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

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element;
use Zend\Form\View\HelperConfig;
use Zend\Form\View\Helper\FormRow as FormRowHelper;
use Zend\View\Renderer\PhpRenderer;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class FormRowTest extends TestCase
{
    protected $helper;
    protected $renderer;

    public function setUp()
    {
        $this->helper = new FormRowHelper();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfig();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
    }

    public function testCanGenerateLabel()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->render($element);
        $this->assertContains('>The value for foo:<', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testCanCreateLabelValueBeforeInput()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $this->helper->setLabelPosition('prepend');
        $markup = $this->helper->render($element);
        $this->assertContains('<label><span>The value for foo:</span><', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testCanCreateLabelValueAfterInput()
    {
        $element = new Element('foo');
        $element->setOptions(array(
            'label' => 'The value for foo:',
        ));
        $this->helper->setLabelPosition('append');
        $markup = $this->helper->render($element);
        $this->assertContains('<label><input', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testCanRenderRowLabelAttributes()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $element->setLabelAttributes(array('class' => 'bar'));
        $this->helper->setLabelPosition('append');
        $markup = $this->helper->render($element);
        $this->assertContains("<label class=\"bar\">", $markup);
    }

    public function testCanCreateMarkupWithoutLabel()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'text');
        $markup = $this->helper->render($element);
        $this->assertRegexp('/<input name="foo" type="text"[^\/>]*\/?>/', $markup);
    }

    public function testCanHandleMultiCheckboxesCorrectly()
    {
        $options = array(
            'This is the first label' => 'value1',
            'This is the second label' => 'value2',
            'This is the third label' => 'value3',
        );

        $element = new Element\MultiCheckbox('foo');
        $element->setAttribute('type', 'multi_checkbox');
        $element->setAttribute('options', $options);
        $element->setLabel('This is a multi-checkbox');
        $markup = $this->helper->render($element);
        $this->assertContains("<fieldset>", $markup);
        $this->assertContains("<legend>", $markup);
        $this->assertContains("<label>", $markup);
    }


    public function testRenderAttributeId()
    {
        $element = new Element\Text('foo');
        $element->setAttribute('type', 'text');
        $element->setAttribute('id', 'textId');
        $element->setLabel('This is a text');
        $markup = $this->helper->render($element);
        $this->assertContains('<label for="textId">This is a text</label>', $markup);
        $this->assertContains('<input type="text" name="foo" id="textId"', $markup);
    }

    public function testCanRenderErrors()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'First error message',
            'Second error message',
            'Third error message',
        ));

        $markup = $this->helper->render($element);
        $this->assertRegexp('#<ul>\s*<li>First error message</li>\s*<li>Second error message</li>\s*<li>Third error message</li>\s*</ul>#s', $markup);
    }

    public function testDoesNotRenderErrorsListIfSetToFalse()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'First error message',
            'Second error message',
            'Third error message',
        ));

        $markup = $this->helper->setRenderErrors(false)->render($element);
        $this->assertRegexp('/<input name="foo" class="input-error" type="text" [^\/>]*\/?>/', $markup);
    }

    public function testCanModifyDefaultErrorClass()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'Error message'
        ));

        $markup = $this->helper->setInputErrorClass('custom-error-class')->render($element);
        $this->assertRegexp('/<input name="foo" class="custom-error-class" type="text" [^\/>]*\/?>/', $markup);
    }

    public function testDoesNotOverrideClassesIfAlreadyPresentWhenThereAreErrors()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'Error message'
        ));
        $element->setAttribute('class', 'foo bar');

        $markup = $this->helper->setInputErrorClass('custom-error-class')->render($element);
        $this->assertRegexp('/<input name="foo" class="foo bar custom-error-class" type="text" [^\/>]*\/?>/', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testLabelWillBeTranslated()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');

        $mockTranslator = $this->getMock('Zend\I18n\Translator\Translator');
        $mockTranslator->expects($this->any())
            ->method('translate')
            ->will($this->returnValue('translated content'));

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);
        $this->assertContains('>translated content<', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('</label>', $markup);

        // Additional coverage when element's id is set
        $element->setAttribute('id', 'foo');
        $markup = $this->helper->__invoke($element);
        $this->assertContains('>translated content<', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('</label>', $markup);
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

    public function testInvokeSetLabelPositionToAppend()
    {
        $element = new Element('foo');
        $this->helper->__invoke($element, 'append');

        $this->assertSame('append', $this->helper->getLabelPosition());
    }

    public function testSetLabelPositionInputNullRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->helper->setLabelPosition(null);
    }

    public function testGetLabelPositionReturnsDefaultPrepend()
    {
        $labelPosition = $this->helper->getLabelPosition();
        $this->assertEquals('prepend', $labelPosition);
    }

    public function testGetLabelPositionReturnsAppend()
    {
        $this->helper->setLabelPosition('append');
        $labelPosition = $this->helper->getLabelPosition();
        $this->assertEquals('append', $labelPosition);
    }

    public function testGetRenderErrorsReturnsDefaultTrue()
    {
        $renderErrors = $this->helper->getRenderErrors();
        $this->assertTrue($renderErrors);
    }

    public function testGetRenderErrorsSetToFalse()
    {
        $this->helper->setRenderErrors(false);
        $renderErrors = $this->helper->getRenderErrors();
        $this->assertFalse($renderErrors);
    }

    public function testSetLabelAttributes()
    {
        $this->helper->setLabelAttributes(array('foo', 'bar'));
        $this->assertEquals(array(0 => 'foo', 1 => 'bar'), $this->helper->getLabelAttributes());
    }

    public function testWhenUsingIdAndLabelBecomesEmptyRemoveSpan()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');

        $markup = $this->helper->__invoke($element);
        $this->assertContains('<span', $markup);
        $this->assertContains('</span>', $markup);

        $element->setAttribute('id', 'foo');

        $markup = $this->helper->__invoke($element);
        $this->assertNotContains('<span', $markup);
        $this->assertNotContains('</span>', $markup);
    }

    public function testShowErrorInMultiCheckbox()
    {
        $element = new Element\MultiCheckbox('hobby');
        $element->setLabel("Hobby");
        $element->setValueOptions(array(
            '0'=>'working',
            '1'=>'coding'
        ));
        $element->setMessages(array(
            'Error message'
        ));

        $markup = $this->helper->__invoke($element);
        $this->assertContains('<ul><li>Error message</li></ul>', $markup);
    }

    public function testShowErrorInRadio()
    {
        $element = new Element\Radio('direction');
        $element->setLabel("Direction");
        $element->setValueOptions(array(
            '0'=>'programming',
            '1'=>'design'
        ));
        $element->setMessages(array(
            'Error message'
        ));

        $markup = $this->helper->__invoke($element);
        $this->assertContains('<ul><li>Error message</li></ul>', $markup);
    }

    public function testErrorShowTwice()
    {
        $element = new  Element\Date('birth');
        $element->setFormat('Y-m-d');
        $element->setValue('2010-13-13');

        $validator = new \Zend\Validator\Date();
        $validator->isValid($element->getValue());
        $element->setMessages($validator->getMessages());

        $markup = $this->helper->__invoke($element);
        $this->assertEquals(2,  count(explode("<ul><li>The input does not appear to be a valid date</li></ul>", $markup)));
    }

    public function testInvokeWithNoRenderErrors()
    {
        $mock = $this->getMock(get_class($this->helper), array('setRenderErrors'));
        $mock->expects($this->never())
                ->method('setRenderErrors');

        $mock->__invoke(new Element('foo'));
    }

    public function testInvokeWithRenderErrorsTrue()
    {
        $mock = $this->getMock(get_class($this->helper), array('setRenderErrors'));
        $mock->expects($this->once())
                ->method('setRenderErrors')
                ->with(true);

        $mock->__invoke(new Element('foo'), null, true);
    }

    public function testAppendLabelEvenIfElementHasId()
    {
        $element  = new Element('foo');
        $element->setAttribute('id', 'bar');
        $element->setLabel('Baz');

        $this->helper->setLabelPosition('append');
        $markup = $this->helper->render($element);
        $this->assertEquals('<input name="foo" id="bar" type="text" value=""/><label for="bar">Baz</label>', $markup);
    }
}
