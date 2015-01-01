<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Filter;

use ArrayObject;
use DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Log\Filter\Timestamp as TimestampFilter;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 *
 * @covers \Zend\Log\Filter\Timestamp
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

    /**
     * @expectedException \Zend\Log\Exception\InvalidArgumentException
     */
    public function testConstructorThrowsOnInvalidValue()
    {
        new TimestampFilter('foo');
    }

    /**
     * @expectedException \Zend\Log\Exception\InvalidArgumentException
     */
    public function testConstructorThrowsWhenDateFormatCharIsMissing()
    {
        new TimestampFilter(3);
    }

    /**
     * @expectedException \Zend\Log\Exception\InvalidArgumentException
     */
    public function testConstructorThrowsOnUnsupportedComparisonOperator()
    {
        new TimestampFilter(10, 'H', 'foobar');
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

    public function testFilterCreatedFromTraversable()
    {
        $config = new ArrayObject(array(
            'value' => 10,
            'dateFormatChar' => 'm',
            'operator' => '==',
        ));
        $filter = new TimestampFilter($config);

        $this->assertAttributeEquals($config['value'], 'value', $filter);
        $this->assertAttributeEquals($config['dateFormatChar'], 'dateFormatChar', $filter);
        $this->assertAttributeEquals($config['operator'], 'operator', $filter);
    }

    /**
     * @param array $message
     *
     * @dataProvider ignoredMessages
     */
    public function testIgnoresMessagesWithoutTimestamp(array $message)
    {
        $filter = new TimestampFilter(new DateTime('-10 years'));

        $this->assertFalse($filter->filter($message));
    }

    public function dateTimeDataProvider()
    {
        $march2 = new DateTime('2014-03-02');
        $march3 = new DateTime('2014-03-03');

        return array(
            array(new DateTime('2014-03-03'), new DateTime('2014-03-03'), '>=', true),
            array(new DateTime('2014-10-10'), new DateTime('2014-03-03'),'>=', true),
            array(new DateTime('2014-03-03'), new DateTime('2014-10-10'), 'gt', false),
            array(new DateTime('2013-03-03'), new DateTime('2014-03-03'), 'ge', false),
            array(new DateTime('2014-03-03'), new DateTime('2014-03-03'), '==', true),
            array(new DateTime('2014-02-02'), new DateTime('2014-03-03'), '<', true),
            array(new DateTime('2014-03-03'), new DateTime('2014-03-03'), 'lt', false),
            array($march3->getTimestamp(), new DateTime('2014-03-03'), 'lt', false),
            array($march2->getTimestamp(), new DateTime('2014-03-03'), 'lt', true),
            array((string) $march3->getTimestamp(), new DateTime('2014-03-03'), 'lt', false),
            array((string) $march2->getTimestamp(), new DateTime('2014-03-03'), 'lt', true),
        );
    }

    public function datePartDataProvider()
    {
        return array(
            array(new DateTime('2014-03-03 10:15:00'), 10, 'H', '==', true),
            array(new DateTime('2013-03-03 22:00:00'), 10, 'H', '=', false),
            array(new DateTime('2014-03-04 10:15:00'), 3, 'd', 'gt', true),
            array(new DateTime('2014-03-04 10:15:00'), 10, 'd', '<', true),
            array(new DateTime('2014-03-03 10:15:00'), 1, 'm', 'eq', false),
            array(new DateTime('2014-03-03 10:15:00'), 2, 'm', 'ge', true),
            array(new DateTime('2014-03-03 10:15:00'), 20, 'H', '!=', true),
        );
    }

    public function ignoredMessages()
    {
        return array(
            array(array()),
            array(array('hello world')),
            array(array('timestamp' => null)),
        );
    }
}
