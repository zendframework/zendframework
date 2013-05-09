<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\View\Helper;

use Zend\Form\Element\Radio as RadioElement;
use Zend\Form\View\Helper\FormRadio as FormRadioHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class FormRadioTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormRadioHelper();
        parent::setUp();
    }

    public function getElement()
    {
        $element = new RadioElement('foo');
        $options = array(
            'value1' => 'This is the first label',
            'value2' => 'This is the second label',
            'value3' => 'This is the third label',
        );
        $element->setValueOptions($options);
        return $element;
    }

    public function getElementWithOptionSpec()
    {
        $element = new RadioElement('foo');
        $options = array(
            'value1' => 'This is the first label',
            1 => array(
                'value'           => 'value2',
                'label'           => 'This is the second label (overridden)',
                'disabled'        => false,
                'label_attributes' => array('class' => 'label-class'),
                'attributes'      => array('class' => 'input-class'),
            ),
            'value3' => 'This is the third label',
        );
        $element->setValueOptions($options);
        return $element;
    }

    public function testUsesOptionsAttributeToGenerateRadioOptions()
    {
        $element = $this->getElement();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo"'));
        $this->assertEquals(3, substr_count($markup, 'type="radio"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            $this->assertContains(sprintf('>%s</label>', $label), $markup);
            $this->assertContains(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesOptionsAttributeWithOptionSpecToGenerateRadioOptions()
    {
        $element = $this->getElementWithOptionSpec();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo'));
        $this->assertEquals(3, substr_count($markup, 'type="radio"'));
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

    public function testGenerateRadioOptionsAndHiddenElement()
    {
        $element = $this->getElement();
        $element->setUseHiddenElement(true);
        $element->setUncheckedValue('none');
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertEquals(4, substr_count($markup, 'name="foo'));
        $this->assertEquals(1, substr_count($markup, 'type="hidden"'));
        $this->assertEquals(1, substr_count($markup, 'value="none"'));
        $this->assertEquals(3, substr_count($markup, 'type="radio"'));
        $this->assertEquals(4, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            $this->assertContains(sprintf('>%s</label>', $label), $markup);
            $this->assertContains(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesElementValueToDetermineRadioStatus()
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
        $options = $element->getValueOptions();
        $this->helper->setLabelPosition(FormRadioHelper::LABEL_PREPEND);
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo"'));
        $this->assertEquals(3, substr_count($markup, 'type="radio"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            $this->assertContains(sprintf('<label>%s<', $label), $markup);
        }
    }

    public function testDoesNotRenderCheckedAttributeIfNotPassed()
    {
        $element = $this->getElement();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertNotContains('checked', $markup);
    }

    public function testAllowsSpecifyingLabelAttributes()
    {
        $element = $this->getElement();

        $markup  = $this->helper
            ->setLabelAttributes(array('class' => 'radio'))
            ->render($element);

        $this->assertEquals(3, substr_count($markup, '<label class="radio"'));
    }

    public function testAllowsSpecifyingLabelAttributesInElementAttributes()
    {
        $element = $this->getElement();
        $element->setLabelAttributes(array('class' => 'radio'));

        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, '<label class="radio"'));
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

    public function testNameShouldNotHaveBracketsAppended()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertNotContains('foo[]', $markup);
    }

    public function testCanTranslateContent()
    {
        $element = new RadioElement('foo');
        $element->setValueOptions(array(
            array(
                'label' => 'label1',
                'value' => 'value1',
            ),
        ));
        $markup = $this->helper->render($element);

        $mockTranslator = $this->getMock('Zend\I18n\Translator\Translator');
        $mockTranslator->expects($this->exactly(1))
        ->method('translate')
        ->will($this->returnValue('translated content'));

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);
        $this->assertContains('>translated content<', $markup);
    }

    public function testTranslatorMethods()
    {
        $translatorMock = $this->getMock('Zend\I18n\Translator\Translator');
        $this->helper->setTranslator($translatorMock, 'foo');

        $this->assertEquals($translatorMock, $this->helper->getTranslator());
        $this->assertEquals('foo', $this->helper->getTranslatorTextDomain());
        $this->assertTrue($this->helper->hasTranslator());
        $this->assertTrue($this->helper->isTranslatorEnabled());

        $this->helper->setTranslatorEnabled(false);
        $this->assertFalse($this->helper->isTranslatorEnabled());
    }
}
