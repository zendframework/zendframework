<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\View\Helper\FormCheckbox as FormCheckboxHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
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
        $element = new Element\Checkbox('foo');
        $options = array(
            'checked_value'   => 'checked',
            'unchecked_value' => 'unchecked',
        );
        $element->setOptions($options);
        return $element;
    }

    public function testUsesOptionsAttributeToGenerateCheckedAndUnCheckedValues()
    {
        $element = $this->getElement();
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
        $element = new Element\Checkbox('foo');
        $markup  = $this->helper->render($element);
        $this->assertRegexp('#type="checkbox".*?(value="1")#', $markup);
        $this->assertRegexp('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }

    public function testSetUseHiddenElementAttributeDoesNotRenderHiddenInput()
    {
        $element = new Element\Checkbox('foo');
        $element->setUseHiddenElement(false);
        $markup  = $this->helper->render($element);
        $this->assertRegexp('#type="checkbox".*?(value="1")#', $markup);
        $this->assertNotRegexp('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }
}
