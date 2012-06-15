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
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\View\Helper\FormButton as FormButtonHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

    public function testRaisesExceptionWhenLabelAttributeIsNotPresentInElement()
    {
        $element = new Element('foo');
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'label');
        $markup = $this->helper->render($element);
    }

    public function testPassingElementToRenderGeneratesButtonMarkup()
    {
        $element = new Element('foo');
        $element->setAttribute('label', '{button_content}');
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
}
