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

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element;
use Zend\Form\View\Helper\FormSelect as FormSelectHelper;

class FormSelectTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormSelectHelper();
        parent::setUp();
    }

    public function getElement() 
    {
        $element = new Element('foo');
        $options = array(
            array(
                'label' => 'This is the first label',
                'value' => 'value1',
            ),
            array(
                'label' => 'This is the second label',
                'value' => 'value2',
            ),
            array(
                'label' => 'This is the third label',
                'value' => 'value3',
            ),
        );
        $element->setAttribute('options', $options);
        return $element;
    }

    public function testCreatesSelectWithOptionsFromAttribute()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);

        $this->assertEquals(1, substr_count($markup, '<select'));
        $this->assertEquals(1, substr_count($markup, '</select>'));
        $this->assertEquals(3, substr_count($markup, '<option'));
        $this->assertEquals(3, substr_count($markup, '</option>'));
        $this->assertContains('>This is the first label<', $markup);
        $this->assertContains('>This is the second label<', $markup);
        $this->assertContains('>This is the third label<', $markup);
        $this->assertContains('value="value1"', $markup);
        $this->assertContains('value="value2"', $markup);
        $this->assertContains('value="value3"', $markup);
    }

    public function testCanMarkSingleOptionAsSelected()
    {
        $element = $this->getElement();
        $element->setAttribute('value', 'value2');

        $markup  = $this->helper->render($element);
        $this->assertRegexp('#option .*?value="value2" selected="selected"#', $markup);
        $this->assertNotRegexp('#option .*?value="value1" selected="selected"#', $markup);
        $this->assertNotRegexp('#option .*?value="value3" selected="selected"#', $markup);
    }

    public function testCanOnlyMarkSingleOptionAsSelectedIfMultipleAttributeIsDisabled()
    {
        $element = $this->getElement();
        $element->setAttribute('value', array('value1', 'value2'));

        $this->setExpectedException('Zend\Form\Exception\ExceptionInterface', 'multiple');
        $markup = $this->helper->render($element);
    }

    public function testCanMarkManyOptionsAsSelectedIfMultipleAttributeIsEnabled()
    {
        $element = $this->getElement();
        $element->setAttribute('multiple', true);
        $element->setAttribute('value', array('value1', 'value2'));
        $markup = $this->helper->render($element);

        $this->assertRegexp('#select .*?multiple="multiple"#', $markup);
        $this->assertRegexp('#option .*?value="value1" selected="selected"#', $markup);
        $this->assertRegexp('#option .*?value="value2" selected="selected"#', $markup);
        $this->assertNotRegexp('#option .*?value="value3" selected="selected"#', $markup);
    }

    public function testCanMarkOptionsAsDisabled()
    {
        $element = $this->getElement();
        $options = $element->getAttribute('options');
        $options[1]['disabled'] = true;
        $element->setAttribute('options', $options);

        $markup = $this->helper->render($element);
        $this->assertRegexp('#option .*?value="value2" .*?disabled="disabled"#', $markup);
    }

    public function testOptgroupsAreCreatedWhenAnOptionHasAnOptionsKey()
    {
        $element = $this->getElement();
        $options = $element->getAttribute('options');
        $options[1]['options'] = array(
            array(
                'label' => 'foo',
                'value' => 'bar',
            )
        );
        $element->setAttribute('options', $options);

        $markup = $this->helper->render($element);
        $this->assertRegexp('#optgroup[^>]*?label="This is the second label"[^>]*>\s*<option[^>]*?value="bar"[^>]*?>foo.*?</optgroup>#s', $markup);
    }

    public function testCanDisableAnOptgroup()
    {
        $element = $this->getElement();
        $options = $element->getAttribute('options');
        $options[1]['disabled'] = true;
        $options[1]['options']  = array(
            array(
                'label' => 'foo',
                'value' => 'bar',
            )
        );
        $element->setAttribute('options', $options);

        $markup = $this->helper->render($element);
        $this->assertRegexp('#optgroup .*?label="This is the second label"[^>]*?disabled="disabled"[^>]*?>\s*<option[^>]*?value="bar"[^>]*?>foo.*?</optgroup>#', $markup);
    }

    /**
     * @group ZF2-290
     */
    public function testFalseDisabledValueWillNotRenderOptionsWithDisabledAttribute()
    {
        $element = $this->getElement();
        $element->setAttribute('disabled', false);
        $markup = $this->helper->render($element);

        $this->assertNotContains('disabled', $markup);
    }

    /**
     * @group ZF2-290
     */
    public function testOmittingDisabledValueWillNotRenderOptionsWithDisabledAttribute()
    {
        $element = $this->getElement();
        $element->setAttribute('type', 'select');
        $markup = $this->helper->render($element);

        $this->assertNotContains('disabled', $markup);
    }

    public function testNameShouldHaveArrayNotationWhenMultipleIsSpecified()
    {
        $element = $this->getElement();
        $element->setAttribute('multiple', true);
        $element->setAttribute('value', array('value1', 'value2'));
        $markup = $this->helper->render($element);
        $this->assertRegexp('#<select[^>]*?(name="foo\[\]")#', $markup);
    }

    public function getScalarOptionsDataProvider()
    {
        return array(
            array(array('string'  => 'value')),
            array(array('int'     => 1)),
            array(array('int-neg' => -1)),
            array(array('hex'     => 0x1A)),
            array(array('oct'     => 0123)),
            array(array('float'   => 2.1)),
            array(array('float-e' => 1.2e3)),
            array(array('float-E' => 7E-10)),
            array(array('bool-t'  => true)),
            array(array('bool-f'  => false)),
        );
    }

    /**
     * @group ZF2-338
     * @dataProvider getScalarOptionsDataProvider
     */
    public function testScalarOptionValues($options)
    {
        $element = new Element('foo');
        $element->setAttribute('options', $options);
        $markup = $this->helper->render($element);
        list($label, $value) = each($options);
        $this->assertRegexp(sprintf('#option .*?value="%s"#', (string)$value), $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = $this->getElement();
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
