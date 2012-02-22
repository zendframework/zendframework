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
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Dojo\Form\Element;

use Zend\Dojo\Form\Element\CheckBox as CheckBoxElement,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_Checkbox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class CheckBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::_unsetInstance();
        DojoHelper::setUseDeclarative();

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        $element = new CheckBoxElement(
            'foo',
            array(
                'label'          => 'CheckBox',
                'class'          => 'someclass',
                'style'          => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testCheckedFlagIsFalseByDefault()
    {
        $this->assertFalse($this->element->checked);
    }

    public function testCheckedAttributeNotRenderedByDefault()
    {
        $html = $this->element->render();
        $this->assertNotContains('checked="checked"', $html);
    }

    public function testCheckedAttributeRenderedWhenCheckedFlagTrue()
    {
        $this->element->checked = true;
        $html = $this->element->render();
        $this->assertContains('checked="checked"', $html);
    }

    public function testCheckedValueDefaultsToOne()
    {
        $this->assertEquals(1, $this->element->getCheckedValue());
    }

    public function testUncheckedValueDefaultsToZero()
    {
        $this->assertEquals(0, $this->element->getUncheckedValue());
    }

    public function testCanSetCheckedValue()
    {
        $this->testCheckedValueDefaultsToOne();
        $this->element->setCheckedValue('foo');
        $this->assertEquals('foo', $this->element->getCheckedValue());
    }

    public function testCanSetUncheckedValue()
    {
        $this->testUncheckedValueDefaultsToZero();
        $this->element->setUncheckedValue('foo');
        $this->assertEquals('foo', $this->element->getUncheckedValue());
    }

    public function testValueInitiallyUncheckedValue()
    {
        $this->assertEquals($this->element->getUncheckedValue(), $this->element->getValue());
    }

    public function testSettingValueToCheckedValueSetsWithEquivalentValue()
    {
        $this->testValueInitiallyUncheckedValue();
        $this->element->setValue($this->element->getCheckedValue());
        $this->assertEquals($this->element->getCheckedValue(), $this->element->getValue());
    }

    public function testSettingValueToAnythingOtherThanCheckedValueSetsAsUncheckedValue()
    {
        $this->testSettingValueToCheckedValueSetsWithEquivalentValue();
        $this->element->setValue('bogus');
        $this->assertEquals($this->element->getUncheckedValue(), $this->element->getValue());
    }

    public function testSettingCheckedFlagToTrueSetsValueToCheckedValue()
    {
        $this->testValueInitiallyUncheckedValue();
        $this->element->setChecked(true);
        $this->assertEquals($this->element->getCheckedValue(), $this->element->getValue());
    }

    public function testSettingCheckedFlagToFalseSetsValueToUncheckedValue()
    {
        $this->testSettingCheckedFlagToTrueSetsValueToCheckedValue();
        $this->element->setChecked(false);
        $this->assertEquals($this->element->getUncheckedValue(), $this->element->getValue());
    }

    public function testSettingValueToCheckedValueMarksElementAsChecked()
    {
        $this->testValueInitiallyUncheckedValue();
        $this->element->setValue($this->element->getCheckedValue());
        $this->assertTrue($this->element->checked);
    }

    public function testSettingValueToUncheckedValueMarksElementAsNotChecked()
    {
        $this->testSettingValueToCheckedValueMarksElementAsChecked();
        $this->element->setValue($this->element->getUncheckedValue());
        $this->assertFalse($this->element->checked);
    }

    public function testIsCheckedShouldReflectCurrentCheckedStatus()
    {
        $this->element->setChecked(true);
        $this->assertTrue($this->element->isChecked());
        $this->element->setChecked(false);
        $this->assertFalse($this->element->isChecked());
    }

    public function testSetOptionsSetsInitialValueAccordingToCheckedAndUncheckedValues()
    {
        $options = array(
            'checkedValue'   => 'foo',
            'uncheckedValue' => 'bar',
        );

        $element = new CheckBoxElement('test', $options);
        $this->assertEquals($options['uncheckedValue'], $element->getValue());
    }

    public function testSetOptionsSetsInitialValueAccordingToSubmittedValues()
    {
        $options = array(
            'test1' => array(
                'value'          => 'foo',
                'checkedValue'   => 'foo',
                'uncheckedValue' => 'bar',
            ),
            'test2' => array(
                'value'          => 'bar',
                'checkedValue'   => 'foo',
                'uncheckedValue' => 'bar',
            ),
        );

        foreach ($options as $current) {
            $element = new CheckBoxElement('test', $current);
            $this->assertEquals($current['value'], $element->getValue());
            $this->assertEquals($current['checkedValue'], $element->getCheckedValue());
            $this->assertEquals($current['uncheckedValue'], $element->getUncheckedValue());
        }
    }

    public function testShouldRenderCheckBoxDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.CheckBox"', $html);
    }

    /**
     * @group ZF-3879
     */
    public function testOptionsShouldNotBeRenderedAsElementAttribute()
    {
        $html = $this->element->render();
        $this->assertNotContains('options="', $html, $html);
    }

    /**
     * @group ZF-4274
     */
    public function testCheckedValuesCanBePassedInConstructor()
    {
        $element = new CheckBoxElement('myCheckbox',    array(
                    'checkedValue' => 'checkedVal',
                    'unCheckedValue' => 'UNCHECKED',
                ));
        $element->setView(new View\Renderer\PhpRenderer());
        $html = $element->render();
        $this->assertContains('value="checkedVal"', $html, $html);
    }
}
