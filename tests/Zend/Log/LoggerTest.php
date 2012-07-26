<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log;

use Zend\Log\Logger;
use Zend\Log\Writer\Mock as MockWriter;
use Zend\Log\Filter\Mock as MockFilter;
use Zend\Stdlib\SplPriorityQueue;
use Zend\Validator\Digits as DigitsFilter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->logger = new Logger;
    }

    public function testUsesDateFormatIso8601ByDefault()
    {
        $this->assertEquals('c', $this->logger->getDateTimeFormat());
    }

    public function testPassingStringToSetDateTimeFormat()
    {
        $this->logger->setDateTimeFormat('U');
        $this->assertEquals('U', $this->logger->getDateTimeFormat());
    }

    public function testUsesWriterPluginManagerByDefault()
    {
        $this->assertInstanceOf('Zend\Log\WriterPluginManager', $this->logger->getWriterPluginManager());
    }

    public function testPassingValidStringClassToSetPluginManager()
    {
        $this->logger->setWriterPluginManager('Zend\Log\WriterPluginManager');
        $this->assertInstanceOf('Zend\Log\WriterPluginManager', $this->logger->getWriterPluginManager());
    }

    public static function provideInvalidClasses()
    {
        return array(
            array('stdClass'),
            array(new \stdClass()),
        );
    }

    /**
     * @dataProvider provideInvalidClasses
     */
    public function testPassingInvalidArgumentToSetPluginManagerRaisesException($plugins)
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException');
        $this->logger->setWriterPluginManager($plugins);
    }

    public function testPassingShortNameToPluginReturnsWriterByThatName()
    {
        $writer = $this->logger->writerPlugin('mock');
        $this->assertInstanceOf('Zend\Log\Writer\Mock', $writer);
    }

    public function testPassWriterAsString()
    {
        $this->logger->addWriter('mock');
        $writers = $this->logger->getWriters();
        $this->assertInstanceOf('Zend\Stdlib\SplPriorityQueue', $writers);
    }

    /**
     * @dataProvider provideInvalidClasses
     */
    public function testPassingInvalidArgumentToAddWriterRaisesException($writer)
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'must implement');
        $this->logger->addWriter($writer);
    }

    public function testEmptyWriter()
    {
        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'No log writer specified');
        $this->logger->log(Logger::INFO, 'test');
    }

    public function testSetWriters()
    {
        $writer1 = $this->logger->writerPlugin('mock');
        $writer2 = $this->logger->writerPlugin('null');
        $writers = new SplPriorityQueue();
        $writers->insert($writer1, 1);
        $writers->insert($writer2, 2);
        $this->logger->setWriters($writers);

        $writers = $this->logger->getWriters();
        $this->assertInstanceOf('Zend\Stdlib\SplPriorityQueue', $writers);
        $writer = $writers->extract();
        $this->assertTrue($writer instanceof \Zend\Log\Writer\Null);
        $writer = $writers->extract();
        $this->assertTrue($writer instanceof \Zend\Log\Writer\Mock);
    }

    public function testAddWriterWithPriority()
    {
        $writer1 = $this->logger->writerPlugin('mock');
        $this->logger->addWriter($writer1,1);
        $writer2 = $this->logger->writerPlugin('null');
        $this->logger->addWriter($writer2,2);
        $writers = $this->logger->getWriters();

        $this->assertInstanceOf('Zend\Stdlib\SplPriorityQueue', $writers);
        $writer = $writers->extract();
        $this->assertTrue($writer instanceof \Zend\Log\Writer\Null);
        $writer = $writers->extract();
        $this->assertTrue($writer instanceof \Zend\Log\Writer\Mock);

    }

    public function testAddWithSamePriority()
    {
        $writer1 = $this->logger->writerPlugin('mock');
        $this->logger->addWriter($writer1,1);
        $writer2 = $this->logger->writerPlugin('null');
        $this->logger->addWriter($writer2,1);
        $writers = $this->logger->getWriters();

        $this->assertInstanceOf('Zend\Stdlib\SplPriorityQueue', $writers);
        $writer = $writers->extract();
        $this->assertTrue($writer instanceof \Zend\Log\Writer\Mock);
        $writer = $writers->extract();
        $this->assertTrue($writer instanceof \Zend\Log\Writer\Null);
    }

    public function testLogging()
    {
        $writer = new MockWriter;
        $this->logger->addWriter($writer);
        $this->logger->log(Logger::INFO, 'tottakai');

        $this->assertEquals(count($writer->events), 1);
        $this->assertContains('tottakai', $writer->events[0]['message']);
    }

    public function testLoggingArray()
    {
        $writer = new MockWriter;
        $this->logger->addWriter($writer);
        $this->logger->log(Logger::INFO, array('test'));

        $this->assertEquals(count($writer->events), 1);
        $this->assertContains('test', $writer->events[0]['message']);
    }

    public function testAddFilter()
    {
        $writer = new MockWriter;
        $filter = new MockFilter;
        $writer->addFilter($filter);
        $this->logger->addWriter($writer);
        $this->logger->log(Logger::INFO, array('test'));

        $this->assertEquals(count($filter->events), 1);
        $this->assertContains('test', $filter->events[0]['message']);
    }

    public function testAddFilterByName()
    {
        $writer = new MockWriter;
        $writer->addFilter('mock');
        $this->logger->addWriter($writer);
        $this->logger->log(Logger::INFO, array('test'));

        $this->assertEquals(count($writer->events), 1);
        $this->assertContains('test', $writer->events[0]['message']);
    }

    /**
     * provideTestFilters
     */
    public function provideTestFilters()
    {
        return array(
            array('priority', array('priority' => Logger::INFO)),
            array('regex', array( 'regex' => '/[0-9]+/' )),
            array('validator', array('validator' => new DigitsFilter)),
        );
    }

    /**
     * @dataProvider provideTestFilters
     */
    public function testAddFilterByNameWithParams($filter, $options)
    {
        $writer = new MockWriter;
        $writer->addFilter($filter, $options);
        $this->logger->addWriter($writer);

        $this->logger->log(Logger::INFO, '123');
        $this->assertEquals(count($writer->events), 1);
        $this->assertContains('123', $writer->events[0]['message']);
    }

    public static function provideAttributes()
    {
        return array(
            array(array()),
            array(array('user' => 'foo', 'ip' => '127.0.0.1')),
            array(new \ArrayObject(array('id' => 42))),
        );
    }

    /**
     * @dataProvider provideAttributes
     */
    public function testLoggingCustomAttributesForUserContext($extra)
    {
        $writer = new MockWriter;
        $this->logger->addWriter($writer);
        $this->logger->log(Logger::ERR, 'tottakai', $extra);

        $this->assertEquals(count($writer->events), 1);
        $this->assertInternalType('array', $writer->events[0]['extra']);
        $this->assertEquals(count($writer->events[0]['extra']), count($extra));
    }

    public static function provideInvalidArguments()
    {
        return array(
            array(new \stdClass(), array('valid')),
            array('valid', null),
            array('valid', true),
            array('valid', 10),
            array('valid', 'invalid'),
            array('valid', new \stdClass()),
        );
    }

    /**
     * @dataProvider provideInvalidArguments
     */
    public function testPassingInvalidArgumentToLogRaisesException($message, $extra)
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException');
        $this->logger->log(Logger::ERR, $message, $extra);
    }

    public function testRegisterErrorHandler()
    {
        $writer = new MockWriter;
        $this->logger->addWriter($writer);

        $this->assertTrue(Logger::registerErrorHandler($this->logger));
        // check for single error handler instance
        $this->assertFalse(Logger::registerErrorHandler($this->logger));
        // generate a warning
        echo $test;
        Logger::unregisterErrorHandler();
        $this->assertEquals($writer->events[0]['message'], 'Undefined variable: test');
    }
}
