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
use Zend\Form\View\Helper\FormCheckbox as FormCheckboxHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormCheckboxTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormCheckboxHelper();
        parent::setUp();
    }

    public function getElement() 
    {
        $element = new Element('foo');
        $options = array(
            'checkedValue'   => 'checked',
            'uncheckedValue' => 'unchecked',
        );
        $element->setAttribute('options', $options);
        return $element;
    }

    public function testUsesOptionsAttributeToGenerateCheckedAndUnCheckedValues()
    {
        $element = $this->getElement();
        $options = $element->getAttribute('options');
        $markup  = $this->helper->render($element);

        $this->assertContains('type="checkbox"', $markup);
        $this->assertContains('value="checked"', $markup);
        $this->assertContains('type="hidden"', $markup);
        $this->assertContains('value="unchecked"', $markup);
    }

    public function testUsesElementValueToDetermineCheckboxStatus()
    {
        $element = $this->getElement();
        $element->setAttribute('value', 'checked');
        $markup  = $this->helper->render($element);

        $this->assertRegexp('#value="checked"\s+checked="checked"#', $markup);
        $this->assertNotRegexp('#value="unchecked"\s+checked="checked"#', $markup);
    }

    public function testNoOptionsAttributeCreatesDefaultCheckedAndUncheckedValues()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertRegexp('#type="checkbox"\s+value="1"#', $markup);
        $this->assertRegexp('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }

    public function testSetUseHiddenElementDoesNotRenderHiddenInput()
    {
        $element = new Element('foo');
        $markup  = $this->helper->setUseHiddenElement(false)->render($element);
        $this->assertRegexp('#type="checkbox"\s+value="1"#', $markup);
        $this->assertNotRegexp('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }

    public function testSetUseHiddenElementAttributeDoesNotRenderHiddenInput()
    {
        $element = new Element('foo');
        $element->setAttribute('useHiddenElement', false);
        $markup  = $this->helper->render($element);
        $this->assertRegexp('#type="checkbox"\s+value="1"#', $markup);
        $this->assertNotRegexp('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }

}
