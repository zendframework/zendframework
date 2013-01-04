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
use Zend\Form\View\Helper\FormFile as FormFileHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class FormFileTest extends CommonTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->helper = new FormFileHelper();
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element\File();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    /**
     * @return void
     */
    public function testGeneratesFileInputTagWithElement()
    {
        $element = new Element\File('foo');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="file"', $markup);
    }

    /**
     * @return void
     */
    public function testGeneratesFileInputTagRegardlessOfElementType()
    {
        $element = new Element\File('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="file"', $markup);
    }

    /**
     * @return void
     */
    public function testRendersElementWithFileArrayValue()
    {
        $element = new Element\File('foo');
        $element->setValue(array(
            'tmp_name' => '/tmp/foofile',
            'name'     => 'foofile',
            'type'     => 'text',
            'size'     => 200,
            'error'    => 2,
        ));
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="file"', $markup);
        $this->assertContains('value="foofile"', $markup);
    }

    /**
     * @return array
     */
    public function validAttributes()
    {
        return array(
            array('name', 'assertContains'),
            array('accept', 'assertContains'),
            array('alt', 'assertNotContains'),
            array('autocomplete', 'assertNotContains'),
            array('autofocus', 'assertContains'),
            array('checked', 'assertNotContains'),
            array('dirname', 'assertNotContains'),
            array('disabled', 'assertContains'),
            array('form', 'assertContains'),
            array('formaction', 'assertNotContains'),
            array('formenctype', 'assertNotContains'),
            array('formmethod', 'assertNotContains'),
            array('formnovalidate', 'assertNotContains'),
            array('formtarget', 'assertNotContains'),
            array('height', 'assertNotContains'),
            array('list', 'assertNotContains'),
            array('max', 'assertNotContains'),
            array('maxlength', 'assertNotContains'),
            array('min', 'assertNotContains'),
            array('multiple', 'assertNotContains'),
            array('pattern', 'assertNotContains'),
            array('placeholder', 'assertNotContains'),
            array('readonly', 'assertNotContains'),
            array('required', 'assertContains'),
            array('size', 'assertNotContains'),
            array('src', 'assertNotContains'),
            array('step', 'assertNotContains'),
            array('value', 'assertContains'),
            array('width', 'assertNotContains'),
        );
    }

    /**
     * @return Element\File
     */
    public function getCompleteElement()
    {
        $element = new Element\File('foo');
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
            'multiple'           => false,
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

    /**
     * @return void
     */
    public function testNameShouldHaveArrayNotationWhenMultipleIsSpecified()
    {
        $element = new Element\File('foo');
        $element->setAttribute('multiple', true);
        $markup = $this->helper->render($element);
        $this->assertRegexp('#<input[^>]*?(name="foo\[\]")#', $markup);
    }

    /**
     * @return void
     */
    public function testInvokeProxiesToRender()
    {
        $element = new Element\File('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<input', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('type="file"', $markup);
    }

    /**
     * @return void
     */
    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
