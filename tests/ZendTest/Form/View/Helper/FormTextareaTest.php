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
use Zend\Form\View\Helper\FormTextarea as FormTextareaHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class FormTextareaTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormTextareaHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testGeneratesEmptyTextareaWhenNoValueAttributePresent()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertRegexp('#<textarea.*?></textarea>#', $markup);
    }

    public function validAttributes()
    {
        return array(
            array('accesskey', 'assertContains'),
            array('class', 'assertContains'),
            array('contenteditable', 'assertContains'),
            array('contextmenu', 'assertContains'),
            array('dir', 'assertContains'),
            array('draggable', 'assertContains'),
            array('dropzone', 'assertContains'),
            array('hidden', 'assertContains'),
            array('id', 'assertContains'),
            array('lang', 'assertContains'),
            array('onabort', 'assertContains'),
            array('onblur', 'assertContains'),
            array('oncanplay', 'assertContains'),
            array('oncanplaythrough', 'assertContains'),
            array('onchange', 'assertContains'),
            array('onclick', 'assertContains'),
            array('oncontextmenu', 'assertContains'),
            array('ondblclick', 'assertContains'),
            array('ondrag', 'assertContains'),
            array('ondragend', 'assertContains'),
            array('ondragenter', 'assertContains'),
            array('ondragleave', 'assertContains'),
            array('ondragover', 'assertContains'),
            array('ondragstart', 'assertContains'),
            array('ondrop', 'assertContains'),
            array('ondurationchange', 'assertContains'),
            array('onemptied', 'assertContains'),
            array('onended', 'assertContains'),
            array('onerror', 'assertContains'),
            array('onfocus', 'assertContains'),
            array('oninput', 'assertContains'),
            array('oninvalid', 'assertContains'),
            array('onkeydown', 'assertContains'),
            array('onkeypress', 'assertContains'),
            array('onkeyup', 'assertContains'),
            array('onload', 'assertContains'),
            array('onloadeddata', 'assertContains'),
            array('onloadedmetadata', 'assertContains'),
            array('onloadstart', 'assertContains'),
            array('onmousedown', 'assertContains'),
            array('onmousemove', 'assertContains'),
            array('onmouseout', 'assertContains'),
            array('onmouseover', 'assertContains'),
            array('onmouseup', 'assertContains'),
            array('onmousewheel', 'assertContains'),
            array('onpause', 'assertContains'),
            array('onplay', 'assertContains'),
            array('onplaying', 'assertContains'),
            array('onprogress', 'assertContains'),
            array('onratechange', 'assertContains'),
            array('onreadystatechange', 'assertContains'),
            array('onreset', 'assertContains'),
            array('onscroll', 'assertContains'),
            array('onseeked', 'assertContains'),
            array('onseeking', 'assertContains'),
            array('onselect', 'assertContains'),
            array('onshow', 'assertContains'),
            array('onstalled', 'assertContains'),
            array('onsubmit', 'assertContains'),
            array('onsuspend', 'assertContains'),
            array('ontimeupdate', 'assertContains'),
            array('onvolumechange', 'assertContains'),
            array('onwaiting', 'assertContains'),
            array('spellcheck', 'assertContains'),
            array('style', 'assertContains'),
            array('tabindex', 'assertContains'),
            array('title', 'assertContains'),
            array('xml:base', 'assertContains'),
            array('xml:lang', 'assertContains'),
            array('xml:space', 'assertContains'),
            array('data-some-key', 'assertContains'),
            array('autofocus', 'assertContains'),
            array('cols', 'assertContains'),
            array('dirname', 'assertContains'),
            array('disabled', 'assertContains'),
            array('form', 'assertContains'),
            array('maxlength', 'assertContains'),
            array('name', 'assertContains'),
            array('placeholder', 'assertContains'),
            array('readonly', 'assertContains'),
            array('required', 'assertContains'),
            array('rows', 'assertContains'),
            array('wrap', 'assertContains'),
            array('content', 'assertNotContains'),
            array('option', 'assertNotContains'),
            array('optgroup', 'assertNotContains'),
            array('arbitrary', 'assertNotContains'),
            array('meta', 'assertNotContains'),
        );
    }

    public function getCompleteElement()
    {
        $element = new Element('foo');
        $element->setAttributes(array(
            'accesskey'          => 'value',
            'class'              => 'value',
            'contenteditable'    => 'value',
            'contextmenu'        => 'value',
            'dir'                => 'value',
            'draggable'          => 'value',
            'dropzone'           => 'value',
            'hidden'             => 'value',
            'id'                 => 'value',
            'lang'               => 'value',
            'onabort'            => 'value',
            'onblur'             => 'value',
            'oncanplay'          => 'value',
            'oncanplaythrough'   => 'value',
            'onchange'           => 'value',
            'onclick'            => 'value',
            'oncontextmenu'      => 'value',
            'ondblclick'         => 'value',
            'ondrag'             => 'value',
            'ondragend'          => 'value',
            'ondragenter'        => 'value',
            'ondragleave'        => 'value',
            'ondragover'         => 'value',
            'ondragstart'        => 'value',
            'ondrop'             => 'value',
            'ondurationchange'   => 'value',
            'onemptied'          => 'value',
            'onended'            => 'value',
            'onerror'            => 'value',
            'onfocus'            => 'value',
            'oninput'            => 'value',
            'oninvalid'          => 'value',
            'onkeydown'          => 'value',
            'onkeypress'         => 'value',
            'onkeyup'            => 'value',
            'onload'             => 'value',
            'onloadeddata'       => 'value',
            'onloadedmetadata'   => 'value',
            'onloadstart'        => 'value',
            'onmousedown'        => 'value',
            'onmousemove'        => 'value',
            'onmouseout'         => 'value',
            'onmouseover'        => 'value',
            'onmouseup'          => 'value',
            'onmousewheel'       => 'value',
            'onpause'            => 'value',
            'onplay'             => 'value',
            'onplaying'          => 'value',
            'onprogress'         => 'value',
            'onratechange'       => 'value',
            'onreadystatechange' => 'value',
            'onreset'            => 'value',
            'onscroll'           => 'value',
            'onseeked'           => 'value',
            'onseeking'          => 'value',
            'onselect'           => 'value',
            'onshow'             => 'value',
            'onstalled'          => 'value',
            'onsubmit'           => 'value',
            'onsuspend'          => 'value',
            'ontimeupdate'       => 'value',
            'onvolumechange'     => 'value',
            'onwaiting'          => 'value',
            'spellcheck'         => 'value',
            'style'              => 'value',
            'tabindex'           => 'value',
            'title'              => 'value',
            'xml:base'           => 'value',
            'xml:lang'           => 'value',
            'xml:space'          => 'value',
            'data-some-key'      => 'value',
            'autofocus'          => 'autofocus',
            'cols'               => 'value',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'form'               => 'value',
            'maxlength'          => 'value',
            'name'               => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'rows'               => 'value',
            'wrap'               => 'value',
            'content'            => 'value',
            'option'             => 'value',
            'optgroup'           => 'value',
            'arbitrary'          => 'value',
            'meta'               => 'value',
        ));
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        $expect  = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
        $this->$assertion($expect, $markup);
    }

    public function booleanAttributeTypes()
    {
        return array(
            array('autofocus', 'autofocus', ''),
            array('disabled', 'disabled', ''),
            array('readonly', 'readonly', ''),
            array('required', 'required', ''),
        );
    }

    /**
     * @dataProvider booleanAttributeTypes
     */
    public function testBooleanAttributeTypesAreRenderedCorrectly($attribute, $on, $off)
    {
        $element = new Element('foo');
        $element->setAttribute($attribute, true);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $on);
        $this->assertContains($expect, $markup, sprintf("Enabled value for %s should be '%s'; received %s", $attribute, $on, $markup));

        $element->setAttribute($attribute, false);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $off);
        if ($off !== '') {
            $this->assertContains($expect, $markup, sprintf("Disabled value for %s should be '%s'; received %s", $attribute, $off, $markup));
        } else {
            $this->assertNotContains($expect, $markup, sprintf("Disabled value for %s should not be rendered; received %s", $attribute, $markup));
        }
    }

    public function testRendersValueAttributeAsTextareaContent()
    {
        $element = new Element('foo');
        $element->setAttribute('value', 'Initial content');
        $markup = $this->helper->render($element);
        $this->assertContains('>Initial content</textarea>', $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<textarea', $markup);
        $this->assertContains('name="foo"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
