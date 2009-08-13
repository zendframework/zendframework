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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_Form_Element_CheckBoxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Element_CheckBoxTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_Form_Element_CheckBox */
require_once 'Zend/Dojo/Form/Element/CheckBox.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_Form_Element_Checkbox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_CheckBoxTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Element_CheckBoxTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Zend_Registry::_unsetInstance();
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative();

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getElement()
    {
        $element = new Zend_Dojo_Form_Element_CheckBox(
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

        $element = new Zend_Dojo_Form_Element_CheckBox('test', $options);
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
            $element = new Zend_Dojo_Form_Element_CheckBox('test', $current);
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
    	$element = new Zend_Dojo_Form_Element_CheckBox('myCheckbox',    array(
			        'checkedValue' => 'checkedVal',
					'unCheckedValue' => 'UNCHECKED',
			    ));
		$element->setView(new Zend_View());
		$html = $element->render();
        $this->assertContains('value="checkedVal"', $html, $html);
    }
}

// Call Zend_Dojo_Form_Element_CheckBoxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Element_CheckBoxTest::main") {
    Zend_Dojo_Form_Element_CheckBoxTest::main();
}
