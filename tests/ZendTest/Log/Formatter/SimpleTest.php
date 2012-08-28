<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Formatter;

use DateTime;
use ZendTest\Log\TestAsset\StringObject;
use Zend\Log\Formatter\Simple;
use stdClass;
use RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class SimpleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorThrowsOnBadFormatString()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'must be a string');
        new Simple(1);
    }

    public function testDefaultFormat()
    {
        $date = new DateTime();
        $fields = array(
            'timestamp'    => $date,
            'message'      => 'foo',
            'priority'     => 42,
            'priorityName' => 'bar',
            'extra'        => array()
        );

        $f = new Simple();
        $line = $f->format($fields);

        $this->assertContains($date->format('c'), $line, 'Default date format is ISO 8601');
        $this->assertContains($fields['message'], $line);
        $this->assertContains($fields['priorityName'], $line);
        $this->assertContains((string) $fields['priority'], $line);
    }

    /**
     * @dataProvider provideMessages
     */
    public function testComplexMessages($message, $printExpected)
    {
        $fields = array(
            'timestamp'    => new DateTime(),
            'priority'     => 42,
            'priorityName' => 'bar',
            'extra'        => array()
        );

        $formatter = new Simple();

        $fields['message'] = $message;
        $line = $formatter->format($fields);
        $this->assertContains($printExpected, $line);
    }

    public function provideMessages()
    {
        return array(
            array('Foo', 'Foo'),
            array(10, '10'),
            array(10.5, '10.5'),
            array(true, '1'),
            array(fopen('php://stdout', 'w'), 'resource(stream)'),
            array(range(1, 10), '[1,2,3,4,5,6,7,8,9,10]'),
            array(new StringObject(), 'Hello World'),
            array(new stdClass(), 'object'),
        );
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testCustomDateTimeFormat($dateTimeFormat)
    {
        $date = new DateTime();
        $event = array('timestamp' => $date);
        $formatter = new Simple('%timestamp%', $dateTimeFormat);

        $this->assertEquals($date->format($dateTimeFormat), $formatter->format($event));
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testSetDateTimeFormat($dateTimeFormat)
    {
        $date = new DateTime();
        $event = array('timestamp' => $date);
        $formatter = new Simple('%timestamp%');

        $this->assertSame($formatter, $formatter->setDateTimeFormat($dateTimeFormat));
        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
        $this->assertEquals($date->format($dateTimeFormat), $formatter->format($event));
    }

    public function provideDateTimeFormats()
    {
        return array(
            array('r'),
            array('U'),
        );
    }

    /**
     * @group ZF-10427
     */
    public function testDefaultFormatShouldDisplayExtraInformations()
    {
        $message = 'custom message';
        $exception = new RuntimeException($message);
        $event = array(
            'timestamp'    => new DateTime(),
            'message'      => 'Application error',
            'priority'     => 2,
            'priorityName' => 'CRIT',
            'extra'        => array($exception),
        );

        $formatter = new Simple();
        $output = $formatter->format($event);

        $this->assertContains($message, $output);
    }

    public function testAllowsSpecifyingFormatAsConstructorArgument()
    {
        $format = '[%timestamp%] %message%';
        $formatter = new Simple($format);
        $this->assertEquals($format, $formatter->format(array()));
    }
}
