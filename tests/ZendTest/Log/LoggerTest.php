<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log;

use Exception;
use ErrorException;
use Zend\Log\Logger;
use Zend\Log\Processor\Backtrace;
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

        $previous = Logger::registerErrorHandler($this->logger);
        $this->assertNotNull($previous);
        $this->assertTrue(false !== $previous);

        // check for single error handler instance
        $this->assertFalse(Logger::registerErrorHandler($this->logger));

        // generate a warning
        echo $test; // $test is not defined

        Logger::unregisterErrorHandler();

        $this->assertEquals($writer->events[0]['message'], 'Undefined variable: test');
    }

    public function testOptionsWithMock()
    {
        $options = array('writers' => array(
                             'first_writer' => array(
                                 'name'     => 'mock',
                             )
                        ));
        $logger = new Logger($options);

        $writers = $logger->getWriters()->toArray();
        $this->assertCount(1, $writers);
        $this->assertInstanceOf('Zend\Log\Writer\Mock', $writers[0]);
    }

    public function testOptionsWithWriterOptions()
    {
        $options = array('writers' => array(
                              array(
                                 'name'     => 'stream',
                                 'options'  => array(
                                     'stream' => 'php://output',
                                     'log_separator' => 'foo'
                                 ),
                              )
                         ));
        $logger = new Logger($options);

        $writers = $logger->getWriters()->toArray();
        $this->assertCount(1, $writers);
        $this->assertInstanceOf('Zend\Log\Writer\Stream', $writers[0]);
        $this->assertEquals('foo', $writers[0]->getLogSeparator());
    }

    public function testAddProcessor()
    {
        $processor = new Backtrace();
        $this->logger->addProcessor($processor);

        $processors = $this->logger->getProcessors()->toArray();
        $this->assertEquals($processor, $processors[0]);
    }

    public function testAddProcessorByName()
    {
        $this->logger->addProcessor('backtrace');

        $processors = $this->logger->getProcessors()->toArray();
        $this->assertInstanceOf('Zend\Log\Processor\Backtrace', $processors[0]);

        $writer = new MockWriter;
        $this->logger->addWriter($writer);
        $this->logger->log(Logger::ERR, 'foo');
    }

    public function testProcessorOutputAdded()
    {
        $processor = new Backtrace();
        $this->logger->addProcessor($processor);
        $writer = new MockWriter;
        $this->logger->addWriter($writer);

        $this->logger->log(Logger::ERR, 'foo');
        $this->assertEquals(__FILE__, $writer->events[0]['extra']['file']);
    }

    public function testExceptionHandler()
    {
        $writer = new MockWriter;
        $this->logger->addWriter($writer);

        $this->assertTrue(Logger::registerExceptionHandler($this->logger));

        // check for single error handler instance
        $this->assertFalse(Logger::registerExceptionHandler($this->logger));

        // get the internal exception handler
        $exceptionHandler = set_exception_handler(function ($e) {});
        set_exception_handler($exceptionHandler);

        // reset the exception handler
        Logger::unregisterExceptionHandler();

        // call the exception handler
        $exceptionHandler(new Exception('error', 200, new Exception('previos', 100)));
        $exceptionHandler(new ErrorException('user notice', 1000, E_USER_NOTICE, __FILE__, __LINE__));

        // check logged messages
        $expectedEvents = array(
            array('priority' => Logger::ERR,    'message' => 'previos',     'file' => __FILE__),
            array('priority' => Logger::ERR,    'message' => 'error',       'file' => __FILE__),
            array('priority' => Logger::NOTICE, 'message' => 'user notice', 'file' => __FILE__),
        );
        for ($i = 0; $i < count($expectedEvents); $i++) {
            $expectedEvent = $expectedEvents[$i];
            $event         = $writer->events[$i];

            $this->assertEquals($expectedEvent['priority'], $event['priority'], 'Unexpected priority');
            $this->assertEquals($expectedEvent['message'], $event['message'], 'Unexpected message');
            $this->assertEquals($expectedEvent['file'], $event['extra']['file'], 'Unexpected file');
        }
    }
}
