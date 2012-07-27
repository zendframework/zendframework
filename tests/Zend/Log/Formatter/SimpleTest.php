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
        $fields = array('timestamp'    => $date,
                        'message'      => 'foo',
                        'priority'     => 42,
                        'priorityName' => 'bar');

        $f = new Simple();
        $line = $f->format($fields);

        $this->assertContains($date->format('c'), $line, 'Default date format is ISO 8601');
        $this->assertContains($fields['message'], $line);
        $this->assertContains($fields['priorityName'], $line);
        $this->assertContains((string)$fields['priority'], $line);
    }

    public function testComplexValues()
    {
        $fields = array('timestamp'    => new DateTime(),
                        'priority'     => 42,
                        'priorityName' => 'bar');

        $f = new Simple();

        $fields['message'] = 'Foo';
        $line = $f->format($fields);
        $this->assertContains($fields['message'], $line);

        $fields['message'] = 10;
        $line = $f->format($fields);
        $this->assertContains((string)$fields['message'], $line);

        $fields['message'] = 10.5;
        $line = $f->format($fields);
        $this->assertContains((string)$fields['message'], $line);

        $fields['message'] = true;
        $line = $f->format($fields);
        $this->assertContains('1', $line);

        $fields['message'] = fopen('php://stdout', 'w');
        $line = $f->format($fields);
        $this->assertContains('Resource id ', $line);
        fclose($fields['message']);

        $fields['message'] = range(1,10);
        $line = $f->format($fields);
        $this->assertContains('array', $line);

        $fields['message'] = new StringObject();
        $line = $f->format($fields);
        $this->assertContains($fields['message']->__toString(), $line);

        $fields['message'] = new \stdClass();
        $line = $f->format($fields);
        $this->assertContains('object', $line);
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
        $exception = new \RuntimeException($message);
        $event = array(
            'timestamp'    => new DateTime(),
            'message'      => 'Application error',
            'priority'     => 2,
            'priorityName' => 'CRIT',
            'info'         => $exception,
        );

        $formatter = new Simple();
        $output = $formatter->format($event);

        $this->assertContains($message, $output);
    }
}
