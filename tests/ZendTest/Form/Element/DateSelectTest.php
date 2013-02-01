<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\Element;

use DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\DateSelect as DateSelectElement;
use Zend\Form\Factory;
use Zend\Form\Exception;

class DateSelectTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new DateSelectElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\Date'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\Date':
                    $this->assertEquals('Y-m-d', $validator->getFormat());
                    break;
                default:
                    break;
            }
        }
    }

    public function testCanSetDateFromDateTime()
    {
        $element  = new DateSelectElement();
        $element->setValue(new DateTime('2012-09-24'));

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
        $this->assertEquals('24', $element->getDayElement()->getValue());
    }

    public function testCanSetDateFromString()
    {
        $element  = new DateSelectElement();
        $element->setValue('2012-09-24');

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
        $this->assertEquals('24', $element->getDayElement()->getValue());
    }

    /**
     * @expectedException \Zend\Form\Exception\InvalidArgumentException
     */
    public function testThrowsOnInvalidValue()
    {
        $element  = new DateSelectElement();
        $element->setValue('hello world');
    }
}
