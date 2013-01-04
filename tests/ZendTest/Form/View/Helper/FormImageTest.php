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
use Zend\Form\View\Helper\FormImage as FormImageHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class FormImageTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormImageHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $element->setAttribute('src', 'foo.png');
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testRaisesExceptionWhenSrcIsNotPresentInElement()
    {
        $element = new Element('foo');
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'src');
        $this->helper->render($element);
    }

    public function testGeneratesImageInputTagWithElement()
    {
        $element = new Element('foo');
        $element->setAttribute('src', 'foo.png');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="image"', $markup);
        $this->assertContains('src="foo.png"', $markup);
    }

    public function testGeneratesImageInputTagRegardlessOfElementType()
    {
        $element = new Element('foo');
        $element->setAttribute('src', 'foo.png');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="image"', $markup);
        $this->assertContains('src="foo.png"', $markup);
    }

    public function validAttributes()
    {
        return array(
            array('name', 'assertContains'),
            array('accept', 'assertNotContains'),
            array('alt', 'assertContains'),
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
            array('height', 'assertContains'),
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
            array('src', 'assertContains'),
            array('step', 'assertNotContains'),
            array('value', 'assertNotContains'),
            array('width', 'assertContains'),
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
            // Intentionally allowing fall-through
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
        $element->setAttribute('src', 'foo.png');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<input', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('type="image"', $markup);
        $this->assertContains('src="foo.png"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
