<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Radio as RadioElement;

class RadioTest extends TestCase
{
    public function useHiddenAttributeDataProvider()
    {
        return array(array(true), array(false));
    }

    /**
     * @dataProvider useHiddenAttributeDataProvider
     */
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes($useHiddenElement)
    {
        $element = new RadioElement();
        $options = array(
            '1' => 'Option 1',
            '2' => 'Option 2',
            '3' => 'Option 3',
        );
        $element->setAttributes(array(
            'options' => $options,
        ));
        $element->setUseHiddenElement($useHiddenElement);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\InArray'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
        }
    }

    public function radioOptionsDataProvider()
    {
        return array(
            array(
                array('foo', 'bar'),
                array(
                    'foo' => 'My Foo Label',
                    'bar' => 'My Bar Label',
                )
            ),
            array(
                array('foo', 'bar'),
                array(
                    0 => array('label' => 'My Foo Label', 'value' => 'foo'),
                    1 => array('label' => 'My Bar Label', 'value' => 'bar'),
                )
            ),
        );
    }

    /**
     * @dataProvider radioOptionsDataProvider
     */
    public function testInArrayValidationOfOptions($valueTests, $options)
    {
        $element = new RadioElement('my-radio');
        $element->setAttributes(array(
            'options' => $options,
        ));
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Zend\Validator\InArray', $inArrayValidator);
        foreach ($valueTests as $valueToTest) {
            $this->assertTrue($inArrayValidator->isValid($valueToTest));
        }
    }

    public function testDisableInputSpecification()
    {
        $element = new RadioElement();
        $element->setValueOptions(array(
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ));
        $element->setDisableInArrayValidator(true);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayNotHasKey('validators', $inputSpec);
    }
}
