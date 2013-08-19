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
use Zend\Form\Element\DateTimeSelect as DateTimeSelectElement;
use Zend\Form\Factory;
use Zend\Form\Exception;
use Zend\InputFilter\Factory as InputFilterFactory;

class DateTimeSelectTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new DateTimeSelectElement();

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
                    $this->assertEquals('Y-m-d H:i:s', $validator->getFormat());
                    break;
                default:
                    break;
            }
        }
    }

    public function testInputSpecificationFilterIfSecondNotProvided()
    {
        $element = new DateTimeSelectElement('test');
        $factory = new InputFilterFactory();
        $inputFilter = $factory->createInputFilter(array(
            'test' => $element->getInputSpecification(),
        ));
        $inputFilter->setData(array(
            'test' => array(
                'year' => '2013',
                'month' => '02',
                'day' => '07',
                'hour' => '03',
                'minute' => '14'
            ),
        ));
        $this->assertTrue($inputFilter->isValid());
    }

    public function testCanSetDateFromDateTime()
    {
        $element  = new DateTimeSelectElement();
        $element->setValue(new DateTime('2012-09-24 03:04:05'));

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
        $this->assertEquals('24', $element->getDayElement()->getValue());
        $this->assertEquals('03', $element->getHourElement()->getValue());
        $this->assertEquals('04', $element->getMinuteElement()->getValue());
        $this->assertEquals('05', $element->getSecondElement()->getValue());
    }

    public function testCanSetDateFromString()
    {
        $element  = new DateTimeSelectElement();
        $element->setValue('2012-09-24 03:04:05');

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
        $this->assertEquals('24', $element->getDayElement()->getValue());
        $this->assertEquals('03', $element->getHourElement()->getValue());
        $this->assertEquals('04', $element->getMinuteElement()->getValue());
        $this->assertEquals('05', $element->getSecondElement()->getValue());
    }

    /**
     * @expectedException \Zend\Form\Exception\InvalidArgumentException
     */
    public function testThrowsOnInvalidValue()
    {
        $element  = new DateTimeSelectElement();
        $element->setValue('hello world');
    }

    public function testUseDefaultValueForSecondsIfNotProvided()
    {
        $element  = new DateTimeSelectElement();
        $element->setValue(array(
            'year' => '2012',
            'month' => '09',
            'day' => '24',
            'hour' => '03',
            'minute' => '04'
        ));

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
        $this->assertEquals('24', $element->getDayElement()->getValue());
        $this->assertEquals('03', $element->getHourElement()->getValue());
        $this->assertEquals('04', $element->getMinuteElement()->getValue());
        $this->assertEquals('00', $element->getSecondElement()->getValue());
    }
}
