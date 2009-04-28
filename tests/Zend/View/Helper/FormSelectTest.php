<?php
// Call Zend_View_Helper_FormSelectTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormSelectTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View/Helper/FormSelect.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_FormSelect.
 */
class Zend_View_Helper_FormSelectTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormSelectTest");
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
        $this->view   = new Zend_View();
        $this->helper = new Zend_View_Helper_FormSelect();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper, $this->view);
    }

    public function testFormSelectWithNameOnlyCreatesEmptySelect()
    {
        $html = $this->helper->formSelect('foo');
        $this->assertRegExp('#<select[^>]+name="foo"#', $html);
        $this->assertContains('</select>', $html);
        $this->assertNotContains('<option', $html);
    }

    public function testFormSelectWithOptionsCreatesPopulatedSelect()
    {
        $html = $this->helper->formSelect('foo', null, null, array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<select[^>]+name="foo"#', $html);
        $this->assertContains('</select>', $html);
        $this->assertRegExp('#<option[^>]+value="foo".*?>Foobar</option>#', $html);
        $this->assertRegExp('#<option[^>]+value="baz".*?>Bazbat</option>#', $html);
        $this->assertEquals(2, substr_count($html, '<option'));
    }

    public function testFormSelectWithOptionsAndValueSelectsAppropriateValue()
    {
        $html = $this->helper->formSelect('foo', 'baz', null, array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="baz"[^>]*selected.*?>Bazbat</option>#', $html);
    }

    public function testFormSelectWithMultipleAttributeCreatesMultiSelect()
    {
        $html = $this->helper->formSelect('foo', null, array('multiple' => true), array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<select[^>]+name="foo\[\]"#', $html);
        $this->assertRegExp('#<select[^>]+multiple="multiple"#', $html);
    }

    public function testFormSelectWithMultipleAttributeAndValuesCreatesMultiSelectWithSelectedValues()
    {
        $html = $this->helper->formSelect('foo', array('foo', 'baz'), array('multiple' => true), array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="foo"[^>]*selected.*?>Foobar</option>#', $html);
        $this->assertRegExp('#<option[^>]+value="baz"[^>]*selected.*?>Bazbat</option>#', $html);
    }

    /**
     * ZF-1930
     * @return void
     */
    public function testFormSelectWithZeroValueSelectsValue()
    {
        $html = $this->helper->formSelect('foo', 0, null, array('foo' => 'Foobar', 0 => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="0"[^>]*selected.*?>Bazbat</option>#', $html);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableEntireSelect()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => 'Bar'
            ), 
            'attribs' => array(
               'disable' => true
            ),
        ));
        $this->assertRegexp('/<select[^>]*?disabled/', $html, $html);
        $this->assertNotRegexp('/<option[^>]*?disabled="disabled"/', $html, $html);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableIndividualSelectOptionsOnly()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => 'Bar'
            ), 
            'attribs' => array(
               'disable' => array('bar')
            ),
        ));
        $this->assertNotRegexp('/<select[^>]*?disabled/', $html, $html);
        $this->assertRegexp('/<option value="bar"[^>]*?disabled="disabled"/', $html, $html);

        $html = $this->helper->formSelect(
            'baz', 
            'foo',
            array(
               'disable' => array('bar')
            ),
            array(
                'foo' => 'Foo', 
                'bar' => 'Bar'
            )
        );
        $this->assertNotRegexp('/<select[^>]*?disabled/', $html, $html);
        $this->assertRegexp('/<option value="bar"[^>]*?disabled="disabled"/', $html, $html);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableMultipleSelectOptions()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => 'Bar',
                'baz' => 'Baz,'
            ), 
            'attribs' => array(
               'disable' => array('foo', 'baz')
            ),
        ));
        $this->assertNotRegexp('/<select[^>]*?disabled/', $html, $html);
        $this->assertRegexp('/<option value="foo"[^>]*?disabled="disabled"/', $html, $html);
        $this->assertRegexp('/<option value="baz"[^>]*?disabled="disabled"/', $html, $html);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableOptGroups()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => array(
                    '1' => 'one',
                    '2' => 'two'
                ),
                'baz' => 'Baz,'
            ), 
            'attribs' => array(
               'disable' => array('bar')
            ),
        ));
        $this->assertNotRegexp('/<select[^>]*?disabled/', $html, $html);
        $this->assertRegexp('/<optgroup[^>]*?disabled="disabled"[^>]*?"bar"[^>]*?/', $html, $html);
        $this->assertNotRegexp('/<option value="1"[^>]*?disabled="disabled"/', $html, $html);
        $this->assertNotRegexp('/<option value="2"[^>]*?disabled="disabled"/', $html, $html);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableOptGroupOptions()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => array(
                    '1' => 'one',
                    '2' => 'two'
                ),
                'baz' => 'Baz,'
            ), 
            'attribs' => array(
               'disable' => array('2')
            ),
        ));
        $this->assertNotRegexp('/<select[^>]*?disabled/', $html, $html);
        $this->assertNotRegexp('/<optgroup[^>]*?disabled="disabled"[^>]*?"bar"[^>]*?/', $html, $html);
        $this->assertNotRegexp('/<option value="1"[^>]*?disabled="disabled"/', $html, $html);
        $this->assertRegexp('/<option value="2"[^>]*?disabled="disabled"/', $html, $html);
    }

    public function testCanSpecifySelectMultipleThroughAttribute()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => 'Bar',
                'baz' => 'Baz,'
            ), 
            'attribs' => array(
               'multiple' => true
            ),
        ));
        $this->assertRegexp('/<select[^>]*?(multiple="multiple")/', $html, $html);
    }

    public function testSpecifyingSelectMultipleThroughAttributeAppendsNameWithBrackets()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => 'Bar',
                'baz' => 'Baz,'
            ), 
            'attribs' => array(
               'multiple' => true
            ),
        ));
        $this->assertRegexp('/<select[^>]*?(name="baz\[\]")/', $html, $html);
    }

    public function testCanSpecifySelectMultipleThroughName()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz[]', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => 'Bar',
                'baz' => 'Baz,'
            ), 
        ));
        $this->assertRegexp('/<select[^>]*?(multiple="multiple")/', $html, $html);
    }

    /**
     * ZF-1639
     */
    public function testNameCanContainBracketsButNotBeMultiple()
    {
        $html = $this->helper->formSelect(array(
            'name'    => 'baz[]', 
            'options' => array(
                'foo' => 'Foo', 
                'bar' => 'Bar',
                'baz' => 'Baz,'
            ), 
            'attribs' => array(
               'multiple' => false
            ),
        ));
        $this->assertRegexp('/<select[^>]*?(name="baz\[\]")/', $html, $html);
        $this->assertNotRegexp('/<select[^>]*?(multiple="multiple")/', $html, $html);
    }
}

// Call Zend_View_Helper_FormSelectTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormSelectTest::main") {
    Zend_View_Helper_FormSelectTest::main();
}
