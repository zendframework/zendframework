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
 * Zend_View_Helper_FormCheckboxTest
 *
 * Tests formCheckbox helper
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class FormCheckboxTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (\Zend\Registry::isRegistered('Zend_View_Helper_Doctype')) {
            $registry = \Zend\Registry::getInstance();
            unset($registry['Zend_View_Helper_Doctype']);
        }
        $this->view   = new \Zend\View\Renderer\PhpRenderer();
        $this->helper = new \Zend\View\Helper\FormCheckbox();
        $this->helper->setView($this->view);
    }

    public function testIdSetFromName()
    {
        $element = $this->helper->__invoke('foo');
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="foo"', $element);
    }

    public function testSetIdFromAttribs()
    {
        $element = $this->helper->__invoke('foo', null, array('id' => 'bar'));
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="bar"', $element);
    }

    /**
     * ZF-2513
     */
    public function testCanDisableCheckbox()
    {
        $html = $this->helper->__invoke(array(
            'name'   => 'foo',
            'value'  => 'bar',
            'attribs'=> array('disable' => true)
        ));
        $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * ZF-3505
     */
    public function testCheckboxNotDisabled()
    {
        $html = $this->helper->__invoke(array(
            'name'   => 'foo',
            'value'  => 'bar',
            'attribs'=> array('disable' => false)
        ));
        $this->assertNotContains('disabled="disabled"', $html);
    }

    public function testCanSelectCheckbox()
    {
        $html = $this->helper->__invoke(array(
            'name'   => 'foo',
            'value'  => 'bar',
            'attribs'=> array('checked' => true)
        ));
        $this->assertRegexp('/<input[^>]*?(checked="checked")/', $html);
        $count = substr_count($html, 'checked');
        $this->assertEquals(2, $count);
    }

    /**
     * ZF-1955
     */
    public function testNameBracketsStrippedWhenCreatingId()
    {
        $html = $this->helper->__invoke(array(
            'name'  => 'foo[]',
            'value' => 'bar'
        ));
        $this->assertRegexp('/<input[^>]*?(id="foo")/', $html);

        $html = $this->helper->__invoke(array(
            'name'  => 'foo[bar]',
            'value' => 'bar'
        ));
        $this->assertRegexp('/<input[^>]*?(id="foo-bar")/', $html);

        $html = $this->helper->__invoke(array(
            'name'  => 'foo[bar][baz]',
            'value' => 'bar'
        ));
        $this->assertRegexp('/<input[^>]*?(id="foo-bar-baz")/', $html);
    }

    /**
     * @grooup ZF-2230
     */
    public function testDoesNotRenderHiddenElementsForCheckboxArray()
    {
        $html = $this->helper->__invoke(array(
            'name'  => 'foo[]',
            'value' => 'bar'
        ));
        $this->assertNotRegexp('/<input[^>]*?(type="hidden")/', $html);
    }

    /**
     * @group ZF-3149
     */
    public function testShouldRenderHiddenElementShowingUncheckedOptionForNonArrayNames()
    {
        $html1 = $this->helper->__invoke(
            'foo',
            'bar',
            array('checked' => true),
            array(
                'checked'   => 'bar',
                'unChecked' => 'baz'
            )
        );
        $html2 = $this->helper->__invoke(
            'foo',
            'bar',
            array('checked' => true),
            array(
                'bar',
                'baz'
            )
        );
        $html3 = $this->helper->__invoke(
            'foo',
            'bar',
            array('checked' => false),
            array(
                'checked'   => 'bar',
                'unChecked' => 'baz'
            )
        );
        $html4 = $this->helper->__invoke(
            'foo',
            'bar',
            array('checked' => false),
            array(
                'bar',
                'baz'
            )
        );
        foreach (array('html1', 'html2', 'html3', 'html4') as $html) {
            if (!preg_match_all('/(<input [^>]+>)/', $$html, $matches)) {
                $this->fail('Unexpected output generated by helper');
            }
            $this->assertEquals(2, count($matches[1]));
            foreach ($matches[1] as $element) {
                if (strstr($element, 'hidden')) {
                    $this->assertContains('baz', $element, 'Failed using ' . $html);
                } else {
                    $this->assertContains('bar', $element, 'Failed using ' . $html);
                    $this->assertContains('checked', $element, 'Failed using ' . $html);
                }
            }
        }
    }

    /**
     * @group ZF-3149
     */
    public function testCheckedAttributeNotRenderedIfItEvaluatesToFalse()
    {
        $test = $this->helper->__invoke('foo', 'value', array('checked' => false));
        $this->assertNotContains('checked', $test);
    }

    public function testCanSpecifyValue()
    {
        $test = $this->helper->__invoke('foo', 'bar');
        $this->assertContains('value="bar"', $test);
    }

    /**
     * @group ZF-3149
     */
    public function testShouldCheckValueIfValueMatchesCheckedOption()
    {
        $test = $this->helper->__invoke('foo', 'bar', array(), array('bar', 'baz'));
        $this->assertContains('value="bar"', $test);
        $this->assertContains('checked', $test);

        $test = $this->helper->__invoke('foo', 'bar', array(), array('checked' => 'bar', 'unChecked' => 'baz'));
        $this->assertContains('value="bar"', $test);
        $this->assertContains('checked', $test);
    }

    /**
     * @group ZF-3149
     */
    public function testShouldOnlySetValueIfValueMatchesCheckedOption()
    {
        $test = $this->helper->__invoke('foo', 'baz', array(), array('bar', 'baz'));
        $this->assertContains('value="bar"', $test);
    }

    /**
     * @group ZF-3149
     */
    public function testShouldNotCheckValueIfValueDoesNotMatchCheckedOption()
    {
        $test = $this->helper->__invoke('foo', 'baz', array(), array('bar', 'baz'));
        $this->assertContains('value="bar"', $test);
        $this->assertNotContains('checked', $test);
    }

    public function testRendersAsHtmlByDefault()
    {
        $test = $this->helper->__invoke('foo', 'bar');
        $this->assertNotContains(' />', $test, $test);
    }

    public function testCanRendersAsXHtml()
    {
        $this->view->plugin('doctype')->__invoke('XHTML1_STRICT');
        $test = $this->helper->__invoke('foo', 'bar');
        $this->assertContains(' />', $test);
    }

   /**
    * @group ZF-6467
    */
   public function testShouldNotShowHiddenFieldIfDisableIsTrue()
   {
       $test = $this->helper->__invoke('foo', 'bar', array('disable' => true));
       $this->assertNotContains('type="hidden"', $test);
   }

   public function testIntValueIsChecked()
   {
       $test = $this->helper->__invoke('foo', '1', array(), array('checked'=>1, 'unchecked'=>0));
       $this->assertContains('checked="checked"', $test);

       $test = $this->helper->__invoke('foo', '1', array(), array(1,0));
       $this->assertContains('checked="checked"', $test);

       $test = $this->helper->__invoke('foo', 1, array(), array('checked'=>1, 'unchecked'=>0));
       $this->assertContains('checked="checked"', $test);

       $test = $this->helper->__invoke('foo', 1, array(), array(1,0));
       $this->assertContains('checked="checked"', $test);

       $test = $this->helper->__invoke('foo', 0, array(), array('checked'=>1, 'unchecked'=>0));
       $this->assertNotContains('checked="checked"', $test);

       $test = $this->helper->__invoke('foo', 0, array(), array(1,0));
       $this->assertNotContains('checked="checked"', $test);
   }
}
