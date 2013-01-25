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
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;

class MultiCheckboxTest extends TestCase
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
        $element = new MultiCheckboxElement();
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
            'Zend\Validator\Explode'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\Explode':
                    $inArrayValidator = $validator->getValidator();
                    $this->assertInstanceOf('Zend\Validator\InArray', $inArrayValidator);
                    break;
                default:
                    break;
            }
        }
    }

    public function multiCheckboxOptionsDataProvider()
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
     * @dataProvider multiCheckboxOptionsDataProvider
     */
    public function testInArrayValidationOfOptions($valueTests, $options)
    {
        $element = new MultiCheckboxElement('my-checkbox');
        $element->setAttributes(array(
            'options' => $options,
        ));
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $explodeValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Zend\Validator\Explode', $explodeValidator);
        $this->assertTrue($explodeValidator->isValid($valueTests));
    }

    /**
     * Testing that InArray Validator Haystack is Updated if the Options
     * are added after the validator is attached
     *
     * @dataProvider multiCheckboxOptionsDataProvider
     */
    public function testInArrayValidatorHaystakIsUpdated($valueTests, $options)
    {
        $element = new MultiCheckboxElement('my-checkbox');
        $inputSpec = $element->getInputSpecification();
        $inArrayValidator=$inputSpec['validators'][0]->getValidator();

        $element->setAttributes(array(
            'options' => $options,
        ));
        $haystack=$inArrayValidator->getHaystack();
        $this->assertCount(count($options), $haystack);
    }


    public function testAttributeType()
    {
        $element = new MultiCheckboxElement();
        $attributes = $element->getAttributes();

        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('multi_checkbox', $attributes['type']);
    }

    public function testSetOptionsOptions()
    {
        $element = new MultiCheckboxElement();
        $element->setOptions(array(
                                  'value_options' => array('bar' => 'baz'),
                                  'options' => array('foo' => 'bar'),
                             ));
        $this->assertEquals(array('bar' => 'baz'), $element->getOption('value_options'));
        $this->assertEquals(array('foo' => 'bar'), $element->getOption('options'));
    }
}
