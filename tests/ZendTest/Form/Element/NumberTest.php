<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Number as NumberElement;

class NumberTest extends TestCase
{
    public function testProvidesInputSpecificationWithDefaultAttributes()
    {
        $element = new NumberElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\Regex',
            'Zend\Validator\Step',
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\Step':
                    $this->assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new NumberElement();
        $element->setAttributes(array(
            'inclusive' => true,
            'min'       => 5,
            'max'       => 10,
            'step'      => 1,
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\GreaterThan',
            'Zend\Validator\LessThan',
            'Zend\Validator\Regex',
            'Zend\Validator\Step',
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(5, $validator->getMin());
                    break;
                case 'Zend\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(10, $validator->getMax());
                    break;
                case 'Zend\Validator\Step':
                    $this->assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testFalseInclusiveValidatorBasedOnAttributes()
    {
        $element = new NumberElement();
        $element->setAttributes(array(
            'inclusive' => false,
            'min'       => 5,
        ));

        $inputSpec = $element->getInputSpecification();
        foreach($inputSpec['validators'] as $validator) {
            if (get_class($validator) == 'Zend\Validator\GreaterThan') {
                $this->assertFalse($validator->getInclusive());
                break;
            }
        }
    }

    public function testDefaultInclusiveTrueatValidatorWhenInclusiveIsNotSetOnAttributes()
    {
        $element = new NumberElement();
        $element->setAttributes(array(
            'min'       => 5,
        ));

        $inputSpec = $element->getInputSpecification();
        foreach($inputSpec['validators'] as $validator) {
            if (get_class($validator) == 'Zend\Validator\GreaterThan') {
                $this->assertTrue($validator->getInclusive());
                break;
            }
        }
    }

    public function testOnlyCastableDecimalsAreAccepted()
    {
        $element = new NumberElement();

        $inputSpec = $element->getInputSpecification();
        foreach($inputSpec['validators'] as $validator) {
            if (get_class($validator) == 'Zend\Validator\Regex') {
                $this->assertFalse($validator->isValid('1,000.01'));
                $this->assertFalse($validator->isValid('-1,000.01'));
                $this->assertTrue($validator->isValid('1000.01'));
                $this->assertTrue($validator->isValid('-1000.01'));
                break;
            }
        }
    }
}
