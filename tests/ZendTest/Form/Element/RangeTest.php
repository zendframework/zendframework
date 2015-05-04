<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Range as RangeElement;

class RangeTest extends TestCase
{
    public function testProvidesInputSpecificationWithDefaultAttributes()
    {
        if (!extension_loaded('intl')) {
            // Required by \Zend\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $element = new RangeElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\I18n\Validator\IsFloat',
            'Zend\Validator\GreaterThan',
            'Zend\Validator\LessThan',
            'Zend\Validator\Step',
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Zend\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(0, $validator->getMin());
                    break;
                case 'Zend\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(100, $validator->getMax());
                    break;
                case 'Zend\Validator\Step':
                    $this->assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesValidator()
    {
        if (!extension_loaded('intl')) {
            // Required by \Zend\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $element = new RangeElement();
        $element->setAttributes(array(
            'inclusive' => true,
            'min'       => 2,
            'max'       => 102,
            'step'      => 2,
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\I18n\Validator\IsFloat',
            'Zend\Validator\GreaterThan',
            'Zend\Validator\LessThan',
            'Zend\Validator\Step',
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Zend\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(2, $validator->getMin());
                    break;
                case 'Zend\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(102, $validator->getMax());
                    break;
                case 'Zend\Validator\Step':
                    $this->assertEquals(2, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }
}
