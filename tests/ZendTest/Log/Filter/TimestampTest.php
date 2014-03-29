<?php
namespace ZendTest\Log\Filter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Log\Filter\Timestamp as TimestampFilter;

/**
 * @group Zend_Log
 * 
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class TimestampTest extends TestCase
{
    /**
     * @dataProvider dateTimeDataProvider
     */
    public function testComparisonWhenValueIsSuppliedAsDateTimeObject($timestamp, $dateTimeValue, $operator, $expectation)
    {
        $filter = new TimestampFilter($dateTimeValue, null, $operator);

        $result = $filter->filter(array('timestamp' => $timestamp));
        
        if ($expectation === true) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
    
    public function dateTimeDataProvider()
    {
        return array(
            array(new \DateTime('2014-03-03'), new \DateTime('2014-03-03'), '>=', true),
            array(new \DateTime('2014-10-10'), new \DateTime('2014-03-03'),'>=', true),
            array(new \DateTime('2013-03-03'), new \DateTime('2014-03-03'), '>=', false),
            array(new \DateTime('2014-03-03'), new \DateTime('2014-03-03'), '==', true),
            array(new \DateTime('2014-02-02'), new \DateTime('2014-03-03'), '<', true),
            array(new \DateTime('2014-03-03'), new \DateTime('2014-03-03'), '<', false)
        );
    }

    /**
     * @dataProvider datePartDataProvider
     */
    public function testComparisonWhenValueIsSuppliedAsDatePartValue($timestamp, $datePartVal, $datePartChar, $operator, $expectation)
    {
        $filter = new TimestampFilter($datePartVal, $datePartChar, $operator);

        $result = $filter->filter(array('timestamp' => $timestamp));
        
        if ($expectation === true) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
    
    public function datePartDataProvider()
    {
        return array(
            array(new \DateTime('2014-03-03 10:15:00'), 10, 'H', '==', true),
            array(new \DateTime('2013-03-03 22:00:00'), 10, 'H', '==', false),
            array(new \DateTime('2014-03-04 10:15:00'), 3, 'd', '>', true),
            array(new \DateTime('2014-03-04 10:15:00'), 10, 'd', '<', true),
            array(new \DateTime('2014-03-03 10:15:00'), 1, 'm', '==', false),
            array(new \DateTime('2014-03-03 10:15:00'), 2, 'm', '>=', true),
        );
    }

    /**
     * @expectedException Zend\Log\Exception\InvalidArgumentException
     */
    public function testConstructorThrowsOnInvalidValue()
    {
        new TimestampFilter('foo');
    }
    
    /**
     * @expectedException Zend\Log\Exception\InvalidArgumentException
     */
    public function testConstructorThrowsWhenDateFormatCharIsMissing()
    {
        new TimestampFilter(3);
    }
    
    public function testFilterCreatedFromArray()
    {
        $config = array(
            'value' => 10,
            'dateFormatChar' => 'm',
            'operator' => '==',
        );
        $filter = new TimestampFilter($config);
        
        $this->assertAttributeEquals($config['value'], 'value', $filter);
        $this->assertAttributeEquals($config['dateFormatChar'], 'dateFormatChar', $filter);
        $this->assertAttributeEquals($config['operator'], 'operator', $filter);
    }
}