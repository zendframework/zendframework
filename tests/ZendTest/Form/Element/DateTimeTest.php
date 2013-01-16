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
use Zend\Form\Element\DateTime as DateTimeElement;

class DateTimeTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new DateTimeElement('foo');
        $element->setAttributes(array(
            'inclusive' => true,
            'min'       => '2000-01-01T00:00Z',
            'max'       => '2001-01-01T00:00Z',
            'step'      => '1',
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\Date',
            'Zend\Validator\GreaterThan',
            'Zend\Validator\LessThan',
            'Zend\Validator\DateStep',
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2000-01-01T00:00Z', $validator->getMin());
                    break;
                case 'Zend\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2001-01-01T00:00Z', $validator->getMax());
                    break;
                case 'Zend\Validator\DateStep':
                    $dateInterval = new \DateInterval('PT1M');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testUsesBrowserFormatByDefault()
    {
        $element = new DateTimeElement('foo');
        $this->assertEquals(DateTimeElement::DATETIME_FORMAT, $element->getFormat());
    }

    public function testSpecifyingADateTimeValueWillReturnBrowserFormattedStringByDefault()
    {
        $date = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setValue($date);
        $this->assertEquals($date->format(DateTimeElement::DATETIME_FORMAT), $element->getValue());
    }

    public function testValueIsFormattedAccordingToFormatInElement()
    {
        $date = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setFormat($date::RFC2822);
        $element->setValue($date);
        $this->assertEquals($date->format($date::RFC2822), $element->getValue());
    }

    public function testCanRetrieveDateTimeObjectByPassingBooleanFalseToGetValue()
    {
        $date = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setValue($date);
        $this->assertSame($date, $element->getValue(false));
    }

    public function testSetFormatWithOptions()
    {

        $format = 'Y-m-d';
        $element = new DateTimeElement('foo');
        $element->setOptions(array(
            'format' => $format,
        ));

        $this->assertSame($format, $element->getFormat());
    }
}
