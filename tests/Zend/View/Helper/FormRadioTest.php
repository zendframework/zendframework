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
use Zend\Filter;

/**
 * Zend_View_Helper_FormRadioTest
 *
 * Tests formRadio helper
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class FormRadioTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->view   = new \Zend\View\Renderer\PhpRenderer();
        $this->helper = new \Zend\View\Helper\FormRadio();
        $this->helper->setView($this->view);
    }

    public function testRendersRadioLabelsWhenRenderingMultipleOptions()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
        ));
        foreach ($options as $key => $value) {
            $this->assertRegexp('#<label.*?>.*?' . $value . '.*?</label>#', $html, $html);
            $this->assertRegexp('#<label.*?>.*?<input.*?</label>#', $html, $html);
        }
    }

    public function testCanSpecifyRadioLabelPlacement()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
            'attribs' => array('labelPlacement' => 'append')
        ));
        foreach ($options as $key => $value) {
            $this->assertRegexp('#<label.*?>.*?<input .*?' . $value . '</label>#', $html, $html);
        }

        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
            'attribs' => array('labelPlacement' => 'prepend')
        ));
        foreach ($options as $key => $value) {
            $this->assertRegexp('#<label.*?>' . $value . '<input .*?</label>#', $html, $html);
        }
    }

    /**
     * @group ZF-3206
     */
    public function testSpecifyingLabelPlacementShouldNotOverwriteValue()
    {
        $options = array(
            'bar' => 'Bar',
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
            'attribs' => array(
                'labelPlacement' => 'append',
            )
        ));
        $this->assertRegexp('#<input[^>]*(checked="checked")#', $html, $html);
    }

    public function testCanSpecifyRadioLabelAttribs()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
            'attribs' => array('labelClass' => 'testclass', 'label_id' => 'testid')
        ));

        foreach ($options as $key => $value) {
            $this->assertRegexp('#<label[^>]*?class="testclass"[^>]*>.*?' . $value . '#', $html, $html);
            $this->assertRegexp('#<label[^>]*?id="testid"[^>]*>.*?' . $value . '#', $html, $html);
        }
    }

    public function testCanSpecifyRadioSeparator()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
            'listsep' => '--FunkySep--',
        ));

        $this->assertContains('--FunkySep--', $html);
        $count = substr_count($html, '--FunkySep--');
        $this->assertEquals(2, $count);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableAllRadios()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
            'attribs' => array('disable' => true)
        ));

        $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $html, $html);
        $count = substr_count($html, 'disabled="disabled"');
        $this->assertEquals(3, $count);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableIndividualRadios()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
            'attribs' => array('disable' => array('bar'))
        ));

        $this->assertRegexp('/<input[^>]*?(value="bar")[^>]*(disabled="disabled")/', $html, $html);
        $count = substr_count($html, 'disabled="disabled"');
        $this->assertEquals(1, $count);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableMultipleRadios()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
            'attribs' => array('disable' => array('foo', 'baz'))
        ));

        foreach (array('foo', 'baz') as $test) {
            $this->assertRegexp('/<input[^>]*?(value="' . $test . '")[^>]*?(disabled="disabled")/', $html, $html);
        }
        $this->assertNotRegexp('/<input[^>]*?(value="bar")[^>]*?(disabled="disabled")/', $html, $html);
        $count = substr_count($html, 'disabled="disabled"');
        $this->assertEquals(2, $count);
    }

    public function testLabelsAreEscapedByDefault()
    {
        $options = array(
            'bar' => '<b>Bar</b>',
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'options' => $options,
        ));

        $this->assertNotContains($options['bar'], $html);
        $this->assertContains('&lt;b&gt;Bar&lt;/b&gt;', $html);
    }

    public function testXhtmlLabelsAreAllowed()
    {
        $options = array(
            'bar' => '<b>Bar</b>',
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'options' => $options,
            'attribs' => array('escape' => false)
        ));

        $this->assertContains($options['bar'], $html);
    }

    /**
     * ZF-1666
     */
    public function testDoesNotRenderHiddenElements()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'options' => $options,
        ));

        $this->assertNotRegexp('/<input[^>]*?(type="hidden")/', $html);
    }

    public function testSpecifyingAValueThatMatchesAnOptionChecksIt()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
        ));

        if (!preg_match('/(<input[^>]*?(value="bar")[^>]*>)/', $html, $matches)) {
            $this->fail('Radio for a given option was not found?');
        }
        $this->assertContains('checked="checked"', $matches[1], var_export($matches, 1));
    }

    public function testOptionsWithMatchesInAnArrayOfValuesAreChecked()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => array('foo', 'baz'),
            'options' => $options,
        ));

        foreach (array('foo', 'baz') as $value) {
            if (!preg_match('/(<input[^>]*?(value="' . $value . '")[^>]*>)/', $html, $matches)) {
                $this->fail('Radio for a given option was not found?');
            }
            $this->assertContains('checked="checked"', $matches[1], var_export($matches, 1));
        }
    }

    public function testEachRadioShouldHaveIdCreatedByAppendingFilteredValue()
    {
        $options = array(
            'foo bar' => 'Foo',
            'bar baz' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo[]',
            'value'   => 'bar',
            'options' => $options,
        ));

        $filter = new Filter\Alnum();
        foreach ($options as $key => $value) {
            $id = 'foo-' . $filter->filter($key);
            $this->assertRegexp('/<input([^>]*)(id="' . $id . '")/', $html);
        }
    }

    public function testEachRadioShouldUseAttributeIdWhenSpecified()
    {
        $options = array(
            'foo bar' => 'Foo',
            'bar baz' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo[bar]',
            'value'   => 'bar',
            'attribs' => array('id' => 'foo-bar'),
            'options' => $options,
        ));

        $filter = new Filter\Alnum();
        foreach ($options as $key => $value) {
            $id = 'foo-bar-' . $filter->filter($key);
            $this->assertRegexp('/<input([^>]*)(id="' . $id . '")/', $html);
        }
    }

    /**
     * @issue ZF-5681
     */
    public function testRadioLabelDoesNotContainHardCodedStyle()
    {
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'options' => $options,
        ));
        $this->assertNotContains('style="white-space: nowrap;"', $html);
    }

    public function testRadioLabelContainsForAttributeTag()
    {
        $options = array(
            'foo bar' => 'Foo',
            'bar baz' => 'Bar',
            'baz' => 'Baz'
        );
        $html = $this->helper->__invoke(array(
            'name'    => 'foo[bar]',
            'value'   => 'bar',
            'options' => $options,
        ));

        $filter = new Filter\Alnum();
        foreach ($options as $key => $value) {
            $id = 'foo-bar-' . $filter->filter($key);
            $this->assertRegexp('/<label([^>]*)(for="' . $id . '")/', $html);
        }
    }
}
