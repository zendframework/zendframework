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

use Zend\Form\Element;
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Zend\Form\View\Helper\FormMultiCheckbox as FormMultiCheckboxHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
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
            'value1' => 'This is the first label',
            'value2' => 'This is the second label',
            'value3' => 'This is the third label',
        );
        $element->setValueOptions($options);
        return $element;
    }

    public function getElementWithOptionSpec()
    {
        $element = new MultiCheckboxElement('foo');
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

    public function testUsesOptionsAttributeToGenerateCheckBoxes()
    {
        $element = $this->getElement();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo'));
        $this->assertEquals(3, substr_count($markup, 'type="checkbox"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            $this->assertContains(sprintf('>%s</label>', $label), $markup);
            $this->assertContains(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesOptionsAttributeWithOptionSpecToGenerateCheckBoxes()
    {
        $element = $this->getElementWithOptionSpec();
        $options = $element->getValueOptions();
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
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertEquals(4, substr_count($markup, 'name="foo'));
        $this->assertEquals(1, substr_count($markup, 'type="hidden"'));
        $this->assertEquals(1, substr_count($markup, 'value="none"'));
        $this->assertEquals(3, substr_count($markup, 'type="checkbox"'));
        $this->assertEquals(4, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
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
        $options = $element->getValueOptions();
        $this->helper->setLabelPosition(FormMultiCheckboxHelper::LABEL_PREPEND);
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo'));
        $this->assertEquals(3, substr_count($markup, 'type="checkbox"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
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
        $element = new MultiCheckboxElement();
        $element->setName('codeType');
        $element->setOptions(array('label' => 'Code Type'));
        $element->setAttributes(array(
            'type' => 'radio',
            'value' => array('markdown'),
        ));
        $element->setValueOptions(array(
            'Markdown' => 'markdown',
            'HTML'     => 'html',
            'Wiki'     => 'wiki',
        ));

        $markup = $this->helper->render($element);
        $this->assertNotContains('type="hidden"', $markup);
        // Lack of error also indicates this test passes
    }

    public function testCanTranslateContent()
    {
        $element = new MultiCheckboxElement('foo');
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

    public function testRenderInputNotSelectElementRaisesException()
    {
        $element = new Element\Text('foo');
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->helper->render($element);
    }

    public function testRenderElementWithNoNameRaisesException()
    {
        $element = new MultiCheckboxElement(null);

        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->helper->render($element);
    }

    public function testRenderElementWithNoValueOptionsRaisesException()
    {
        $element = new MultiCheckboxElement('foo');

        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->helper->render($element);
    }

    public function testCanMarkSingleOptionAsSelected()
    {
        $element = new MultiCheckboxElement('foo');
        $options = array(
            'value1' => 'This is the first label',
            1 => array(
                'value'           => 'value2',
                'label'           => 'This is the second label (overridden)',
                'disabled'        => false,
                'selected'         => true,
                'label_attributes' => array('class' => 'label-class'),
                'attributes'      => array('class' => 'input-class'),
            ),
            'value3' => 'This is the third label',
        );
        $element->setValueOptions($options);

        $markup  = $this->helper->render($element);
        $this->assertRegexp('#class="input-class" value="value2" checked="checked"#', $markup);
        $this->assertNotRegexp('#class="input-class" value="value1" checked="checked"#', $markup);
        $this->assertNotRegexp('#class="input-class" value="value3" checked="checked"#', $markup);
    }

    public function testInvokeSetLabelPositionToAppend()
    {
        $element = new MultiCheckboxElement('foo');
        $element->setValueOptions(array(
                                       array(
                                           'label' => 'label1',
                                           'value' => 'value1',
                                       ),
                                  ));
        $this->helper->__invoke($element, 'append');

        $this->assertSame('append', $this->helper->getLabelPosition());
    }

    public function testSetLabelPositionInputNullRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->helper->setLabelPosition(null);
    }

    public function testSetLabelAttributes()
    {
        $this->helper->setLabelAttributes(array('foo', 'bar'));
        $this->assertEquals(array(0 => 'foo', 1 => 'bar'), $this->helper->getLabelAttributes());
    }

    public function testGetUseHiddenElementReturnsDefaultFalse()
    {
        $hiddenElement = $this->helper->getUseHiddenElement();
        $this->assertFalse($hiddenElement);
    }

    public function testGetUseHiddenElementSetToTrue()
    {
        $this->helper->setUseHiddenElement(true);
        $hiddenElement = $this->helper->getUseHiddenElement();
        $this->assertTrue($hiddenElement);
    }

    public function testGetUncheckedValueReturnsDefaultEmptyString()
    {
        $uncheckedValue = $this->helper->getUncheckedValue();
        $this->assertSame('', $uncheckedValue);
    }

    public function testGetUncheckedValueSetToFoo()
    {
        $this->helper->setUncheckedValue('foo');
        $uncheckedValue = $this->helper->getUncheckedValue();
        $this->assertSame('foo', $uncheckedValue);
    }
}
