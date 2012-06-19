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
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\View\Helper\FormInput as FormInputHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormInputTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormInputHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testGeneratesTextInputTagWhenProvidedAnElementWithNoTypeAttribute()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="text"', $markup);
    }

    public function testGeneratesInputTagWithElementsTypeAttribute()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="email"', $markup);
    }

    public function inputTypes()
    {
        return array(
            array('text', 'assertContains'),
            array('button', 'assertContains'),
            array('checkbox', 'assertContains'),
            array('file', 'assertContains'),
            array('hidden', 'assertContains'),
            array('image', 'assertContains'),
            array('password', 'assertContains'),
            array('radio', 'assertContains'),
            array('reset', 'assertContains'),
            array('select', 'assertContains'),
            array('submit', 'assertContains'),
            array('color', 'assertContains'),
            array('date', 'assertContains'),
            array('datetime', 'assertContains'),
            array('datetime-local', 'assertContains'),
            array('email', 'assertContains'),
            array('month', 'assertContains'),
            array('number', 'assertContains'),
            array('range', 'assertContains'),
            array('search', 'assertContains'),
            array('tel', 'assertContains'),
            array('time', 'assertContains'),
            array('url', 'assertContains'),
            array('week', 'assertContains'),
            array('lunar', 'assertNotContains'),
            array('name', 'assertNotContains'),
            array('username', 'assertNotContains'),
            array('address', 'assertNotContains'),
            array('homepage', 'assertNotContains'),
        );
    }

    /**
     * @dataProvider inputTypes
     */
    public function testOnlyAllowsValidInputTypes($type, $assertion)
    {
        $element = new Element('foo');
        $element->setAttribute('type', $type);
        $markup   = $this->helper->render($element);
        $expected = sprintf('type="%s"', $type);
        $this->$assertion($expected, $markup);
    }

    public function validAttributes()
    {
        return array(
            array('accept', 'assertContains'),
            array('accesskey', 'assertContains'),
            array('alt', 'assertContains'),
            array('autocomplete', 'assertContains'),
            array('autofocus', 'assertContains'),
            array('checked', 'assertContains'),
            array('class', 'assertContains'),
            array('contenteditable', 'assertContains'),
            array('contextmenu', 'assertContains'),
            array('dir', 'assertContains'),
            array('dirname', 'assertContains'),
            array('disabled', 'assertContains'),
            array('draggable', 'assertContains'),
            array('dropzone', 'assertContains'),
            array('form', 'assertContains'),
            array('formaction', 'assertContains'),
            array('formenctype', 'assertContains'),
            array('formmethod', 'assertContains'),
            array('formnovalidate', 'assertContains'),
            array('formtarget', 'assertContains'),
            array('height', 'assertContains'),
            array('hidden', 'assertContains'),
            array('id', 'assertContains'),
            array('lang', 'assertContains'),
            array('list', 'assertContains'),
            array('max', 'assertContains'),
            array('maxlength', 'assertContains'),
            array('min', 'assertContains'),
            array('multiple', 'assertContains'),
            array('name', 'assertContains'),
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
            array('pattern', 'assertContains'),
            array('placeholder', 'assertContains'),
            array('readonly', 'assertContains'),
            array('required', 'assertContains'),
            array('size', 'assertContains'),
            array('spellcheck', 'assertContains'),
            array('src', 'assertContains'),
            array('step', 'assertContains'),
            array('style', 'assertContains'),
            array('tabindex', 'assertContains'),
            array('title', 'assertContains'),
            array('value', 'assertContains'),
            array('width', 'assertContains'),
            array('xml:base', 'assertContains'),
            array('xml:lang', 'assertContains'),
            array('xml:space', 'assertContains'),
            array('data-some-key', 'assertContains'),
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
            'accept'             => 'value',
            'accesskey'          => 'value',
            'alt'                => 'value',
            'autocomplete'       => 'on',
            'autofocus'          => 'autofocus',
            'checked'            => 'checked',
            'class'              => 'value',
            'contenteditable'    => 'value',
            'contextmenu'        => 'value',
            'dir'                => 'value',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'draggable'          => 'value',
            'dropzone'           => 'value',
            'form'               => 'value',
            'formaction'         => 'value',
            'formenctype'        => 'value',
            'formmethod'         => 'value',
            'formnovalidate'     => 'value',
            'formtarget'         => 'value',
            'height'             => 'value',
            'hidden'             => 'value',
            'id'                 => 'value',
            'lang'               => 'value',
            'list'               => 'value',
            'max'                => 'value',
            'maxlength'          => 'value',
            'min'                => 'value',
            'multiple'           => 'multiple',
            'name'               => 'value',
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
            'pattern'            => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'size'               => 'value',
            'spellcheck'         => 'value',
            'src'                => 'value',
            'step'               => 'value',
            'style'              => 'value',
            'tabindex'           => 'value',
            'title'              => 'value',
            'value'              => 'value',
            'width'              => 'value',
            'wrap'               => 'value',
            'xml:base'           => 'value',
            'xml:lang'           => 'value',
            'xml:space'          => 'value',
            'data-some-key'      => 'value',
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

    public function nonXhtmlDoctypes()
    {
        return array(
            array('HTML4_STRICT'),
            array('HTML4_LOOSE'),
            array('HTML4_FRAMESET'),
            array('HTML5'),
        );
    }

    /**
     * @dataProvider nonXhtmlDoctypes
     */
    public function testRenderingOmitsClosingSlashWhenDoctypeIsNotXhtml($doctype)
    {
        $element = new Element('foo');
        $this->renderer->doctype($doctype);
        $markup = $this->helper->render($element);
        $this->assertNotContains('/>', $markup);
    }

    public function xhtmlDoctypes()
    {
        return array(
            array('XHTML11'),
            array('XHTML1_STRICT'),
            array('XHTML1_TRANSITIONAL'),
            array('XHTML1_FRAMESET'),
            array('XHTML1_RDFA'),
            array('XHTML_BASIC1'),
            array('XHTML5'),
        );
    }

    /**
     * @dataProvider xhtmlDoctypes
     */
    public function testRenderingIncludesClosingSlashWhenDoctypeIsXhtml($doctype)
    {
        $element = new Element('foo');
        $this->renderer->doctype($doctype);
        $markup = $this->helper->render($element);
        $this->assertContains('/>', $markup);
    }

    public function booleanAttributeTypes()
    {
        return array(
            array('autocomplete', 'on', 'off'),
            array('autofocus', 'autofocus', ''),
            array('disabled', 'disabled', ''),
            array('multiple', 'multiple', ''),
            array('readonly', 'readonly', ''),
            array('required', 'required', ''),
            array('checked', 'checked', ''),
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

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<input', $markup);
        $this->assertContains('name="foo"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
