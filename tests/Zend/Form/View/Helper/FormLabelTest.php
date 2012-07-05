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
use Zend\Form\View\Helper\FormLabel as FormLabelHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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
}
