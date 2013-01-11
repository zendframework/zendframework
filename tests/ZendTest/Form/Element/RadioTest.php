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
}
