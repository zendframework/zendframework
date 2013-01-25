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
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Select as SelectElement;

class SelectTest extends TestCase
{
    public function testProvidesInputSpecificationForSingleSelect()
    {
        $element = new SelectElement();
        $element->setValueOptions(array(
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ));

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

    public function testValidateWorksForNestedSelectElementWithSimpleNaming()
    {
        $element = new SelectElement();
        $element->setValueOptions(array(
          array('label' => 'group 1', 'options' => array(
            'Option 1' => 'Label 1',
            'Option 2' => 'Label 2',
            'Option 3' => 'Label 2',
          ))));

        $inputSpec = $element->getInputSpecification();
        $inArrayValidator = $inputSpec['validators'][0];

        $this->assertTrue($inArrayValidator->isValid('Option 1'));
        $this->assertFalse($inArrayValidator->isValid('Option 5'));
    }

    public function testValidateWorksForNestedSelectElementWithExplicitNaming()
    {
        $element = new SelectElement();
        $element->setValueOptions(array(
          array('label' => 'group 1', 'options' => array(
            array('value' => 'Option 1', 'label'=> 'Label 1'),
            array('value' => 'Option 2', 'label'=> 'Label 2'),
            array('value' => 'Option 3', 'label'=> 'Label 3'),
          ))));

        $inputSpec = $element->getInputSpecification();
        $inArrayValidator = $inputSpec['validators'][0];

        $this->assertTrue($inArrayValidator->isValid('Option 1'));
        $this->assertTrue($inArrayValidator->isValid('Option 2'));
        $this->assertTrue($inArrayValidator->isValid('Option 3'));
        $this->assertFalse($inArrayValidator->isValid('Option 5'));
    }
    public function testProvidesInputSpecificationForMultipleSelect()
    {
        $element = new SelectElement();
        $element->setAttributes(array(
            'multiple' => true,
        ));
        $element->setValueOptions(array(
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\Explode'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\Explode':
                    $this->assertInstanceOf('Zend\Validator\InArray', $validator->getValidator());
                    break;
                default:
                    break;
            }
        }
    }

    public function selectOptionsDataProvider()
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
     * @dataProvider selectOptionsDataProvider
     */
    public function testInArrayValidationOfOptions($valueTests, $options)
    {
        $element = new SelectElement('my-select');
        $element->setValueOptions($options);
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Zend\Validator\InArray', $inArrayValidator);
        foreach ($valueTests as $valueToTest) {
            $this->assertTrue($inArrayValidator->isValid($valueToTest));
        }
    }

    /**
     * Testing that InArray Validator Haystack is Updated if the Options
     * are added after the validator is attached
     *
     * @dataProvider selectOptionsDataProvider
     */
    public function testInArrayValidatorHaystakIsUpdated($valueTests, $options)
    {
        $element = new SelectElement('my-select');
        $inputSpec = $element->getInputSpecification();

        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Zend\Validator\InArray', $inArrayValidator);

        $element->setValueOptions($options);
        $haystack=$inArrayValidator->getHaystack();
        $this->assertCount(count($options), $haystack);
    }


    public function testOptionsHasArrayOnConstruct()
    {
        $element = new SelectElement();
        $this->assertTrue(is_array($element->getValueOptions()));
    }

    public function testDeprecateOptionsInAttributes()
    {
        $element = new SelectElement();
        $valueOptions = array(
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        );
        $element->setAttributes(array(
            'multiple' => true,
            'options'  => $valueOptions,
        ));
        $this->assertEquals($valueOptions, $element->getValueOptions());
    }

    public function testSetOptionsOptions()
    {
        $element = new SelectElement();
        $element->setOptions(array(
                                  'value_options' => array('bar' => 'baz'),
                                  'options' => array('foo' => 'bar'),
                                  'empty_option' => array('baz' => 'foo'),
                             ));
        $this->assertEquals(array('bar' => 'baz'), $element->getOption('value_options'));
        $this->assertEquals(array('foo' => 'bar'), $element->getOption('options'));
        $this->assertEquals(array('baz' => 'foo'), $element->getOption('empty_option'));
    }


}
