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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

/**
 * Test class for Zend_View_Helper_FormSelect.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class FormSelectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->view   = new \Zend\View\Renderer\PhpRenderer();
        $this->helper = new \Zend\View\Helper\FormSelect();
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
        $html = $this->helper->__invoke('foo');
        $this->assertRegExp('#<select[^>]+name="foo"#', $html);
        $this->assertContains('</select>', $html);
        $this->assertNotContains('<option', $html);
    }

    public function testFormSelectWithOptionsCreatesPopulatedSelect()
    {
        $html = $this->helper->__invoke('foo', null, null, array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<select[^>]+name="foo"#', $html);
        $this->assertContains('</select>', $html);
        $this->assertRegExp('#<option[^>]+value="foo".*?>Foobar</option>#', $html);
        $this->assertRegExp('#<option[^>]+value="baz".*?>Bazbat</option>#', $html);
        $this->assertEquals(2, substr_count($html, '<option'));
    }

    public function testFormSelectWithOptionsAndValueSelectsAppropriateValue()
    {
        $html = $this->helper->__invoke('foo', 'baz', null, array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="baz"[^>]*selected.*?>Bazbat</option>#', $html);
    }

    public function testFormSelectWithMultipleAttributeCreatesMultiSelect()
    {
        $html = $this->helper->__invoke('foo', null, array('multiple' => true), array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<select[^>]+name="foo\[\]"#', $html);
        $this->assertRegExp('#<select[^>]+multiple="multiple"#', $html);
    }

    public function testFormSelectWithMultipleAttributeAndValuesCreatesMultiSelectWithSelectedValues()
    {
        $html = $this->helper->__invoke('foo', array('foo', 'baz'), array('multiple' => true), array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="foo"[^>]*selected.*?>Foobar</option>#', $html);
        $this->assertRegExp('#<option[^>]+value="baz"[^>]*selected.*?>Bazbat</option>#', $html);
    }

    /**
     * ZF-1930
     * @return void
     */
    public function testFormSelectWithZeroValueSelectsValue()
    {
        $html = $this->helper->__invoke('foo', 0, null, array('foo' => 'Foobar', 0 => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="0"[^>]*selected.*?>Bazbat</option>#', $html);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableEntireSelect()
    {
        $html = $this->helper->__invoke(array(
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
        $html = $this->helper->__invoke(array(
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

        $html = $this->helper->__invoke(
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
        $html = $this->helper->__invoke(array(
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
        $html = $this->helper->__invoke(array(
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
        $html = $this->helper->__invoke(array(
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
        $html = $this->helper->__invoke(array(
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
        $html = $this->helper->__invoke(array(
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
        $html = $this->helper->__invoke(array(
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
        $html = $this->helper->__invoke(array(
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

