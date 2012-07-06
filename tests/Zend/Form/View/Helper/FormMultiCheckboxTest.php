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
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Zend\Form\View\Helper\FormMultiCheckbox as FormMultiCheckboxHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormMultiCheckboxTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormMultiCheckboxHelper();
        parent::setUp();
    }

    public function getElement()
    {
        $element = new MultiCheckboxElement('foo');
        $options = array(
            'This is the first label' => 'value1',
            'This is the second label' => 'value2',
            'This is the third label' => 'value3',
        );
        $element->setAttribute('options', $options);
        return $element;
    }

    public function getElementWithOptionSpec()
    {
        $element = new MultiCheckboxElement('foo');
        $options = array(
            'This is the first label' => 'value1',
            'This is the second label' => array(
                'value'           => 'value2',
                'label'           => 'This is the second label (overridden)',
                'disabled'        => false,
                'label_attributes' => array('class' => 'label-class'),
                'attributes'      => array('class' => 'input-class'),
            ),
            'This is the third label' => 'value3',
        );
        $element->setAttribute('options', $options);
        return $element;
    }

    public function testUsesOptionsAttributeToGenerateCheckBoxes()
    {
        $element = $this->getElement();
        $options = $element->getAttribute('options');
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo'));
        $this->assertEquals(3, substr_count($markup, 'type="checkbox"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $label => $value) {
            $this->assertContains(sprintf('>%s</label>', $label), $markup);
            $this->assertContains(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesOptionsAttributeWithOptionSpecToGenerateCheckBoxes()
    {
        $element = $this->getElementWithOptionSpec();
        $options = $element->getAttribute('options');
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo'));
        $this->assertEquals(3, substr_count($markup, 'type="checkbox"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        $this->assertContains(
            sprintf('>%s</label>', 'This is the first label'), $markup
        );
        $this->assertContains(sprintf('value="%s"', 'value1'), $markup);

        $this->assertContains(
            sprintf('>%s</label>', 'This is the second label (overridden)'), $markup
        );
        $this->assertContains(sprintf('value="%s"', 'value2'), $markup);
        $this->assertEquals(1, substr_count($markup, 'class="label-class"'));
        $this->assertEquals(1, substr_count($markup, 'class="input-class"'));

        $this->assertContains(
            sprintf('>%s</label>', 'This is the third label'), $markup
        );
        $this->assertContains(sprintf('value="%s"', 'value3'), $markup);

    }

    public function testGenerateCheckBoxesAndHiddenElement()
    {
        $element = $this->getElement();
        $element->setUseHiddenElement(true);
        $element->setUncheckedValue('none');
        $options = $element->getAttribute('options');
        $markup  = $this->helper->render($element);

        $this->assertEquals(4, substr_count($markup, 'name="foo'));
        $this->assertEquals(1, substr_count($markup, 'type="hidden"'));
        $this->assertEquals(1, substr_count($markup, 'value="none"'));
        $this->assertEquals(3, substr_count($markup, 'type="checkbox"'));
        $this->assertEquals(4, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $label => $value) {
            $this->assertContains(sprintf('>%s</label>', $label), $markup);
            $this->assertContains(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesElementValueToDetermineCheckboxStatus()
    {
        $element = $this->getElement();
        $element->setAttribute('value', array('value1', 'value3'));
        $markup  = $this->helper->render($element);

        $this->assertRegexp('#value="value1"\s+checked="checked"#', $markup);
        $this->assertNotRegexp('#value="value2"\s+checked="checked"#', $markup);
        $this->assertRegexp('#value="value3"\s+checked="checked"#', $markup);
    }

    public function testAllowsSpecifyingSeparator()
    {
        $element = $this->getElement();
        $this->helper->setSeparator('<br />');
        $markup  = $this->helper->render($element);
        $this->assertEquals(2, substr_count($markup, '<br />'));
    }

    public function testAllowsSpecifyingLabelPosition()
    {
        $element = $this->getElement();
        $options = $element->getAttribute('options');
        $this->helper->setLabelPosition(FormMultiCheckboxHelper::LABEL_PREPEND);
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo'));
        $this->assertEquals(3, substr_count($markup, 'type="checkbox"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $label => $value) {
            $this->assertContains(sprintf('<label>%s<', $label), $markup);
        }
    }

    public function testAllowsSpecifyingLabelAttributes()
    {
        $element = $this->getElement();

        $markup  = $this->helper
            ->setLabelAttributes(array('class' => 'checkbox'))
            ->render($element);

        $this->assertEquals(3, substr_count($markup, '<label class="checkbox"'));
    }

    public function testAllowsSpecifyingLabelAttributesInElementAttributes()
    {
        $element = $this->getElement();
        $element->setLabelAttributes(array('class' => 'checkbox'));
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, '<label class="checkbox"'));
    }

    public function testIdShouldNotBeRenderedForEachRadio()
    {
        $element = $this->getElement();
        $element->setAttribute('id', 'foo');
        $markup  = $this->helper->render($element);
        $this->assertTrue(1 >= substr_count($markup, 'id="foo"'));
    }

    public function testIdShouldBeRenderedOnceIfProvided()
    {
        $element = $this->getElement();
        $element->setAttribute('id', 'foo');
        $markup  = $this->helper->render($element);
        $this->assertEquals(1, substr_count($markup, 'id="foo"'));
    }

    public function testNameShouldHaveBracketsAppended()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains('foo[]', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = $this->getElement();
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testEnsureUseHiddenElementMethodExists()
    {
        $element = new Element();
        $element->setName('codeType');
        $element->setOptions(array('label' => 'Code Type'));
        $element->setAttributes(array(
            'type' => 'radio',
            'options' => array(
                'Markdown' => 'markdown',
                'HTML'     => 'html',
                'Wiki'     => 'wiki',
            ),
            'value' => array('markdown'),
        ));

        $markup = $this->helper->render($element);
        $this->assertNotContains('type="hidden"', $markup);
        // Lack of error also indicates this test passes
    }
}
