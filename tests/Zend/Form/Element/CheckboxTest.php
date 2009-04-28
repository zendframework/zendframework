<?php
// Call Zend_Form_Element_CheckboxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_CheckboxTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Form/Element/Checkbox.php';

/**
 * Test class for Zend_Form_Element_Checkbox
 */
class Zend_Form_Element_CheckboxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Element_CheckboxTest");
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
        $this->element = new Zend_Form_Element_Checkbox('foo');
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
        return new Zend_View();
    }

    public function testCheckboxElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testCheckboxElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testCheckboxElementUsesCheckboxHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formCheckbox', $helper);
    }

    public function testCheckedFlagIsFalseByDefault()
    {
        $this->assertFalse($this->element->checked);
    }

    public function testCheckedAttributeNotRenderedByDefault()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $html = $this->element->render($view);
        $this->assertNotContains('checked="checked"', $html);
    }

    public function testCheckedAttributeRenderedWhenCheckedFlagTrue()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $this->element->checked = true;
        $html = $this->element->render($view);
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

    public function testSetOptionsSetsInitialValueAccordingToCheckedAndUncheckedValues()
    {
        $options = array(
            'checkedValue'   => 'foo',
            'uncheckedValue' => 'bar',
        );

        $element = new Zend_Form_Element_Checkbox('test', $options);
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
            $element = new Zend_Form_Element_Checkbox('test', $current);
            $this->assertEquals($current['value'], $element->getValue());
            $this->assertEquals($current['checkedValue'], $element->getCheckedValue());
            $this->assertEquals($current['uncheckedValue'], $element->getUncheckedValue());
        }
    }

    public function testCheckedValueAlwaysRenderedAsCheckboxValue()
    {
        $this->element->setValue($this->element->getUncheckedValue());
        $html = $this->element->render($this->getView());
        if (!preg_match_all('/(<input[^>]+>)/', $html, $matches)) {
            $this->fail('Unexpected generated HTML: ' . $html);
        }
        $this->assertEquals(2, count($matches[1]));
        foreach ($matches[1] as $element) {
            if (strstr($element, 'hidden')) {
                $this->assertContains($this->element->getUncheckedValue(), $element);
            } else {
                $this->assertContains($this->element->getCheckedValue(), $element);
            }
        }
    }

    /**
     * Used by test methods susceptible to ZF-2794, marks a test as incomplete
     *
     * @link   http://framework.zend.com/issues/browse/ZF-2794
     * @return void
     */
    protected function _checkZf2794()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win' && version_compare(PHP_VERSION, '5.1.4', '=')) {
            $this->markTestIncomplete('Error occurs for PHP 5.1.4 on Windows');
        }
    }
}

// Call Zend_Form_Element_CheckboxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_CheckboxTest::main") {
    Zend_Form_Element_CheckboxTest::main();
}
