<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log;

require_once __DIR__ . '/TestAsset/NotExtendedWriterAbstract.php';
require_once __DIR__ . '/TestAsset/NotImplementsFilterInterface.php';

use ZendTest\Log\TestAsset\MockFormatter,
    \Zend\Log\Logger,
    \Zend\Log;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->log = fopen('php://memory', 'w+');
        $this->writer = new Log\Writer\Stream($this->log);
    }

    // Writers

    public function testWriterCanBeAddedWithConstructor()
    {
        $logger = new Logger($this->writer);
        $logger->log($message = 'message-to-log', Logger::INFO);

        rewind($this->log);
        $this->assertContains($message, stream_get_contents($this->log));
    }

    public function testAddWriter()
    {
        $logger = new Logger();
        $logger->addWriter($this->writer);
        $logger->log($message = 'message-to-log', Logger::INFO);

        rewind($this->log);
        $this->assertContains($message, stream_get_contents($this->log));
    }

    public function testAddWriterAddsMultipleWriters()
    {
        $logger = new Logger();

        // create writers for two separate streams of temporary memory
        $log1    = fopen('php://memory', 'w+');
        $writer1 = new Log\Writer\Stream($log1);
        $log2    = fopen('php://memory', 'w+');
        $writer2 = new Log\Writer\Stream($log2);

        // add the writers
        $logger->addWriter($writer1);
        $logger->addWriter($writer2);

        // log to both writers
        $logger->log($message = 'message-sent-to-both-logs', Logger::INFO);

        // verify both writers were called by the logger
        rewind($log1);
        $this->assertContains($message, stream_get_contents($log1));
        rewind($log2);
        $this->assertContains($message, stream_get_contents($log2));

        // prove the two memory streams are different
        // and both writers were indeed called
        fwrite($log1, 'foo');
        $this->assertNotEquals(ftell($log1), ftell($log2));
    }

    public function testLoggerThrowsWhenNoWriters()
    {
        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'No writers');
        $logger = new Logger();
        $logger->log('message', Logger::INFO);
    }

    public function testDestructorCallsShutdownOnEachWriter()
    {
        $writer1 = new Log\Writer\Mock();
        $writer2 = new Log\Writer\Mock();

        $logger = new Logger();
        $logger->addWriter($writer1);
        $logger->addWriter($writer2);

        $this->assertFalse($writer1->shutdown);
        $this->assertFalse($writer2->shutdown);

        $logger = null;

        $this->assertTrue($writer1->shutdown);
        $this->assertTrue($writer2->shutdown);
    }

    // Priorities

    public function testLogThrowsOnBadLogPriority()
    {
        $logger = new Logger($this->writer);

        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Bad log priority');
        $logger->log('foo', 42);
    }

    public function testLogThrough__callThrowsOnBadLogPriority()
    {
        $logger = new Logger($this->writer);

        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Bad log priority');
        $logger->nonexistantPriority('');
    }

    public function testAddingPriorityThrowsWhenOverridingBuiltinLogPriority()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Existing priorities');
        $logger = new Logger($this->writer);
        $logger->addPriority('BOB', 0);
    }

    public function testAddLogPriority()
    {
        $logger = new Logger($this->writer);
        $logger->addPriority('EIGHT', $priority = 8);

        $logger->eight($message = 'eight message');

        rewind($this->log);
        $logdata = stream_get_contents($this->log);
        $this->assertContains((string)$priority, $logdata);
        $this->assertContains($message, $logdata);
    }

    // Fields

    public function testLogWritesStandardFields()
    {
        $logger = new Logger($mock = new Log\Writer\Mock);
        $logger->info('foo');

        $this->assertEquals(1, count($mock->events));
        $event = array_shift($mock->events);

        $standardFields = array_flip(array('timestamp', 'priority', 'priorityName', 'message'));
        $this->assertEquals(array(), array_diff_key($event, $standardFields));
    }

    public function testLogWritesAndOverwritesExtraFields()
    {
        $logger = new Logger($mock = new Log\Writer\Mock);
        $logger->setEventItem('foo', 42);
        $logger->setEventItem($field = 'bar', $value = 43);
        $logger->info('foo');

        $this->assertEquals(1, count($mock->events));
        $event = array_shift($mock->events);

        $this->assertTrue(array_key_exists($field, $event));
        $this->assertEquals($value, $event[$field]);
    }

    /**
     * @group ZF-8491
     */
    public function testLogAcceptsExtrasParameterAsArrayAndPushesIntoEvent()
    {
        $logger = new Logger($mock = new Log\Writer\Mock);
        $logger->info('foo', array('content' => 'nonesuch'));
        $event = array_shift($mock->events);
        $this->assertContains('content', array_keys($event));
        $this->assertEquals('nonesuch', $event['content']);
    }

    /**
     * @group ZF-8491
     */
    public function testLogNumericKeysInExtrasArrayArePassedToInfoKeyOfEvent()
    {
        $logger = new Logger($mock = new Log\Writer\Mock);
        $logger->info('foo', array('content' => 'nonesuch', 'bar'));
        $event = array_shift($mock->events);
        $this->assertContains('content', array_keys($event));
        $this->assertContains('info', array_keys($event));
        $this->assertContains('bar', $event['info']);
    }

    /**
     * @group ZF-8491
     */
    public function testLogAcceptsExtrasParameterAsScalarAndAddsAsInfoKeyToEvent()
    {
        $logger = new Logger($mock = new Log\Writer\Mock);
        $logger->info('foo', 'nonesuch');
        $event = array_shift($mock->events);
        $this->assertContains('info', array_keys($event));
        $info = $event['info'];
        $this->assertContains('nonesuch', $info);
    }

    // Factory

    public function testLogConstructFromConfigStream()
    {
        $cfg = array('log' => array('memory' => array(
            'writerName'      => "Stream",
            'writerNamespace' => "Zend\Log\Writer",
            'writerParams'    => array(
                'stream'      => "php://memory"
            )
        )));

        $logger = Logger::factory($cfg['log']);
        $this->assertTrue($logger instanceof Logger);
    }

    public function testLogConstructFromConfigStreamAndFilter()
    {
        $cfg = array('log' => array('memory' => array(
            'writerName'      => "Stream",
            'writerNamespace' => "Zend\Log\Writer",
            'writerParams'    => array(
                'stream'      => "php://memory"
            ),
            'filterName'   => "Priority",
            'filterParams' => array(
                'priority' => "Zend\Log\Logger::CRIT",
                'operator' => "<="
             ),
        )));

        $logger = Logger::factory($cfg['log']);
        $this->assertTrue($logger instanceof Logger);
    }

    public function testFactoryUsesNameAndNamespaceWithoutModifications()
    {
        $cfg = array('log' => array('memory' => array(
            'writerName'      => "ZendMonitor",
            'writerNamespace' => "Zend\Log\Writer",
        )));

        $logger = Logger::factory($cfg['log']);
        $this->assertTrue($logger instanceof Logger);
    }

    /**
     * @group ZF-9192
     */
    public function testUsingWithErrorHandler()
    {
        $writer = new Log\Writer\Mock();

        $logger = new Logger();
        $logger->addWriter($writer);
        $this->errWriter = $writer;


        $oldErrorLevel = error_reporting();

        $this->expectingLogging = true;
        error_reporting(E_ALL | E_STRICT);

        $oldHandler = set_error_handler(array($this, 'verifyHandlerData'));
        $logger->registerErrorHandler();

        trigger_error("Testing notice shows up in logs", E_USER_NOTICE);
        trigger_error("Testing warning shows up in logs", E_USER_WARNING);
        trigger_error("Testing error shows up in logs", E_USER_ERROR);

        $this->expectingLogging = false;
        error_reporting(0);

        trigger_error("Testing notice misses logs", E_USER_NOTICE);
        trigger_error("Testing warning misses logs", E_USER_WARNING);
        trigger_error("Testing error misses logs", E_USER_ERROR);

        restore_error_handler(); // Pop off the Logger
        restore_error_handler(); // Pop off the verifyHandlerData
        error_reporting($oldErrorLevel); // Restore original reporting level
        unset($this->errWriter);
    }

    /**
     * @group ZF-9192
     * Used by testUsingWithErrorHandler -
     * verifies that the data written to the original logger is the same as the data written in Zend_Log
     */
    public function verifyHandlerData($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if ($this->expectingLogging) {
            $this->assertFalse(empty($this->errWriter->events));
            $event = array_shift($this->errWriter->events);
            $this->assertEquals($errstr, $event['message']);
            $this->assertEquals($errno, $event['errno']);
            $this->assertEquals($errfile, $event['file']);
            $this->assertEquals($errline, $event['line']);
        } else {
            $this->assertTrue(empty($this->errWriter->events));
        }
    }

    /**
     * @group ZF-9870
     */
    public function testSetAndGetTimestampFormat()
    {
        $logger = new Logger($this->writer);
        $this->assertEquals('c', $logger->getTimestampFormat());
        $this->assertSame($logger, $logger->setTimestampFormat('Y-m-d H:i:s'));
        $this->assertEquals('Y-m-d H:i:s', $logger->getTimestampFormat());
    }

    /**
     * @group ZF-9870
     */
    public function testLogWritesWithModifiedTimestampFormat()
    {
        $logger = new Logger($this->writer);
        $logger->setTimestampFormat('Y-m-d');
        $logger->debug('ZF-9870');
        rewind($this->log);
        $message = stream_get_contents($this->log);
        $this->assertEquals(date('Y-m-d'), substr($message, 0, 10));
    }

    /**
     * @group ZF-9955
     */
    public function testExceptionConstructWriterFromConfig()
    {
        $logger = new Logger();
        $writer = array('writerName' => 'NotExtendedWriterAbstract');

        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'The specified writer does not extend Zend\Log\Writer');
        $logger->addWriter($writer);
    }

    /**
     * @group ZF-9956
     */
    public function testExceptionConstructFilterFromConfig()
    {
        $logger = new Logger();
        $filter = array('filterName' => 'NotImplementsFilterInterface');

        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'The specified filter does not implement Zend\Log\Filter');
        $logger->addFilter($filter);
    }

    /**
     * @group ZF-8953
     */
    public function testFluentInterface()
    {
        $logger   = new Logger();
        $instance = $logger->addPriority('all', 8)
                           ->addFilter(1)
                           ->addWriter(array('writerName' => 'Null'))
                           ->setEventItem('os', PHP_OS);

        $this->assertTrue($instance instanceof Logger);
    }

    /**
     * @group ZF-10170
     */
    public function testExceptionIsThrownOnPriorityDuplicates()
    {
        $logger   = new Logger();
        $mock     = new Log\Writer\Mock();
        $logger->addWriter($mock);

        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Existing priorities cannot be overwritten');
        $logger->addPriority('emerg', 8);
    }

    /**
     * @group ZF-10170
     */
    public function testExceptionIsThrownOnInvalidLogPriority()
    {
        $logger   = new Logger();
        $mock     = new Log\Writer\Mock();
        $logger->addWriter($mock);
        $logger->log('zf10170', 0);

        $this->assertEquals(0, $mock->events[0]['priority']);
        $this->assertEquals('EMERG', $mock->events[0]['priorityName']);
        $this->assertFalse(array_key_exists(1, $mock->events));

        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Bad log priority');
        $logger->log('clone zf10170', 8);
    }

    /**
     * @group ZF-9176
     */
    public function testLogConstructFromConfigFormatter()
    {
        $config = array(
            'log' => array(
                'test' => array(
                    'writerName'    => 'Mock',
                    'formatterName' => 'Simple',
                    'formatterParams' => array(
                        'format' => '%timestamp% (%priorityName%): %message%'
                    )
                )
            )
        );

        $logger = Logger::factory($config['log']);
        $logger->log('custom message', Logger::INFO);
    }

    /**
     * @group ZF-9176
     */
    public function testLogConstructFromConfigCustomFormatter()
    {
        $config = array(
            'log' => array(
                'test' => array(
                    'writerName'    => 'Mock',
                    'formatterName' => 'MockFormatter',
                    'formatterNamespace' => 'ZendTest\Log\TestAsset'
                )
            )
        );

        $logger = Logger::factory($config['log']);
        $logger->log('custom message', Logger::INFO);
    }

    /**
     * @group ZF-10990
     */
    public function testFactoryShouldSetTimestampFormat()
    {
        $config = array(
            'timestampFormat' => 'Y-m-d',
            'mock' => array(
                'writerName' => 'Mock'
            )
        );
        $logger = Logger::factory($config);

        $this->assertEquals('Y-m-d', $logger->getTimestampFormat());
    }

    /**
     * @group ZF-10990
     */
    public function testFactoryShouldKeepDefaultTimestampFormat()
    {
        $config = array(
            'timestampFormat' => '',
            'mock' => array(
                'writerName' => 'Mock'
            )
        );
        $logger = Logger::factory($config);

        $this->assertEquals('c', $logger->getTimestampFormat());
    }
}
