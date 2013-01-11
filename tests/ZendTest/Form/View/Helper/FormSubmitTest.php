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
use Zend\Form\View\Helper\FormSubmit as FormSubmitHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class FormSubmitTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormSubmitHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testGeneratesSubmitInputTagWithElement()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="submit"', $markup);
    }

    public function testGeneratesSubmitInputTagRegardlessOfElementType()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="submit"', $markup);
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

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<input', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('type="submit"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    /**
     * @group ZF2-450
     */
    public function testCanTranslateValue()
    {
        $element = new Element('foo');
        $element->setValue('Submit Label');

        $mockTranslator = $this->getMock('Zend\I18n\Translator\Translator');
        $mockTranslator->expects($this->exactly(1))
                       ->method('translate')
                       ->will($this->returnValue('translated content'));

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);
        $this->assertContains('value="translated content"', $markup);
    }
}
