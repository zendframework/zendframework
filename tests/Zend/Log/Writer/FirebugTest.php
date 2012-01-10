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

namespace ZendTest\Log\Writer;

use Zend\Log\Writer\Firebug as FirebugWriter,
    Zend\Log\Logger,
    Zend\Wildfire\Channel,
    Zend\Wildfire\Plugin\FirePhp,
    ZendTest\Log\TestAsset\FirePhpHeader,
    ZendTest\Log\TestAsset\MockHttpResponse,
    ZendTest\Log\TestAsset\CustomFirebugFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class FirebugTest extends \PHPUnit_Framework_TestCase
{
    protected $_request = null;
    protected $_response = null;
    protected $_writer = null;
    protected $_logger = null;

    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    public function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        date_default_timezone_set('America/Los_Angeles');

        // Reset front controller to reset registered plugins and
        // registered request/response objects
        \Zend\Controller\Front::getInstance()->resetInstance();

        $this->_request = new FirePhpHeader();
        $this->_response = new MockHttpResponse();

        $channel = Channel\HttpHeaders::getInstance();
        $channel->setRequest($this->_request);
        $channel->setResponse($this->_response);

        $this->_writer = new FirebugWriter();

        // Explicitly enable writer as it is disabled by default
        // when running from the command line.
        $this->_writer->setEnabled(true);

        $this->_logger = new Logger($this->_writer);

        FirePhp::getInstance()->setOption('includeLineNumbers', false);
    }

    public function tearDown()
    {
        Channel\HttpHeaders::destroyInstance();
        FirePhp::destroyInstance();
        date_default_timezone_set($this->_originaltimezone);
    }

    /**
     * Test for ZF-3960
     *
     * Zend_Log_Writer_Firebug should be automatically disabled when
     * run from the command line
     */
    public function testZf3960()
    {
        Channel\HttpHeaders::destroyInstance();
        FirePhp::destroyInstance();

        $log = new Logger();
        $writerFirebug = new FirebugWriter();
        $log->addWriter($writerFirebug);
        $log->log('hi', 2);
    }

    /**
     * @group ZF-4952
     */
    public function testSetFormatter()
    {
        $firephp = FirePhp::getInstance();
        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp::PROTOCOL_URI);

        $this->_logger->log('Test Message 1', Logger::INFO);

        $formatter = new CustomFirebugFormatter();
        $this->_writer->setFormatter($formatter);

        $this->_logger->setEventItem('testLabel', 'Test Label');
        $this->_logger->log('Test Message 2', Logger::INFO);

        $messages = $protocol->getMessages();

        $message = $messages[FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [FirePhp::PLUGIN_URI]
                            [0];

        $this->assertEquals($message,
                            '[{"Type":"INFO"},"Test Message 1"]');

        $message = $messages[FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [FirePhp::PLUGIN_URI]
                            [1];

        $this->assertEquals($message,
                            '[{"Type":"INFO"},"Test Label : Test Message 2"]');
    }

    /**
     * @group ZF-4952
     */
    public function testEventItemLabel()
    {
        $firephp = FirePhp::getInstance();
        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp::PROTOCOL_URI);

        $this->_logger->log('Test Message 1', Logger::INFO);

        $this->_logger->setEventItem('firebugLabel','Test Label');
        $this->_logger->log('Test Message 2', Logger::INFO);

        $messages = $protocol->getMessages();

        $message = $messages[FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [FirePhp::PLUGIN_URI]
                            [0];

        $this->assertEquals($message,
                            '[{"Type":"INFO"},"Test Message 1"]');

        $message = $messages[FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [FirePhp::PLUGIN_URI]
                            [1];

        $this->assertEquals($message,
                            '[{"Type":"INFO","Label":"Test Label"},"Test Message 2"]');
    }

    public function testLogStyling()
    {
        $this->assertEquals($this->_writer->getDefaultPriorityStyle(),
                            FirePhp::LOG);
        $this->assertEquals($this->_writer->setDefaultPriorityStyle(FirePhp::WARN),
                            FirePhp::LOG);
        $this->assertEquals($this->_writer->getDefaultPriorityStyle(),
                            FirePhp::WARN);

        $this->assertEquals($this->_writer->getPriorityStyle(9),
                            false);
        $this->assertEquals($this->_writer->setPriorityStyle(9,FirePhp::WARN),
                            true);
        $this->assertEquals($this->_writer->getPriorityStyle(9),
                            FirePhp::WARN);
        $this->assertEquals($this->_writer->setPriorityStyle(9,FirePhp::LOG),
                            FirePhp::WARN);
    }

    public function testBasicLogging()
    {
        $message = 'This is a log message!';

        $this->_logger->log($message, Logger::INFO);

        Channel\HttpHeaders::getInstance()->flush();

        $headers = array();
        $headers['X-Wf-Protocol-1'] = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';
        $headers['X-Wf-1-Structure-1'] = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        $headers['X-Wf-1-Plugin-1'] = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6.2';
        $headers['X-Wf-1-1-1-1'] = '42|[{"Type":"INFO"},"This is a log message!"]|';

        $this->assertTrue($this->_response->verifyHeaders($headers));
    }

    /**
     * @group ZF-4934
     */
    public function testAdvancedLogging()
    {
        FirePhp::getInstance()->setOption('maxTraceDepth',0);

        $message = 'This is a log message!';
        $label = 'Test Label';
        $table = array(
            'Summary line for the table',
            array(
                array('Column 1', 'Column 2'),
                array('Row 1 c 1',' Row 1 c 2'),
                array('Row 2 c 1',' Row 2 c 2')
            )
        );

        $this->_logger->addPriority('TRACE', 8);
        $this->_logger->addPriority('TABLE', 9);
        $this->_writer->setPriorityStyle(8, 'TRACE');
        $this->_writer->setPriorityStyle(9, 'TABLE');

        $this->_logger->trace($message);
        $this->_logger->table($table);

        try {
            throw new \Exception('Test Exception');
        } catch (\Exception $e) {
            $this->_logger->err($e);
        }

        try {
            FirePhp::send($message, $label, 'UNKNOWN');
            $this->fail('Should not be able to log with undefined log style');
        } catch (\Zend\Wildfire\Plugin\Exception\UnexpectedValueException $e) {
            // success
        }

        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp::PROTOCOL_URI);

        $messages = array(
            FirePhp::STRUCTURE_URI_FIREBUGCONSOLE => array(
                FirePhp::PLUGIN_URI => array(
                    1 => '[{"Type":"TABLE"},["Summary line for the table",[["Column 1","Column 2"],["Row 1 c 1"," Row 1 c 2"],["Row 2 c 1"," Row 2 c 2"]]]]'
        )));

        $qued_messages = $protocol->getMessages();

        unset($qued_messages[FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][FirePhp::PLUGIN_URI][0]);
        unset($qued_messages[FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][FirePhp::PLUGIN_URI][2]);

        $this->assertEquals(serialize($qued_messages),
                            serialize($messages));
    }

    public function testFactory()
    {
        $cfg = array('log' => array('memory' => array(
            'writerName' => "Firebug"
        )));

        $logger = Logger::factory($cfg['log']);
        $this->assertTrue($logger instanceof Logger);
    }

    /**
     * @group ZF-10537
     */
    public function testFileLineOffsets()
    {
        $firephp = FirePhp::getInstance();
        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp::PROTOCOL_URI);
        $firephp->setOption('includeLineNumbers', true);
        $firephp->setOption('maxTraceDepth', 0);

        $lines = array();
        // NOTE: Do NOT separate the following pairs otherwise the line numbers will not match for the test

        // Message number: 1
        $lines[] = __LINE__ + 1;
        $this->_logger->log('Hello World', Logger::INFO);

        // Message number: 2
        $this->_logger->addPriority('TRACE', 8);
        $this->_writer->setPriorityStyle(8, 'TRACE');
        $lines[] = __LINE__ + 1;
        $this->_logger->trace('Trace to here');

        // Message number: 3
        $this->_logger->addPriority('TABLE', 9);
        $this->_writer->setPriorityStyle(9, 'TABLE');
        $table = array('Summary line for the table',
                       array(
                           array('Column 1', 'Column 2'),
                           array('Row 1 c 1',' Row 1 c 2'),
                           array('Row 2 c 1',' Row 2 c 2')
                       )
                      );
        $lines[] = __LINE__ + 1;
        $this->_logger->table($table);

        // Message number: 4
        $lines[] = __LINE__ + 1;
        $this->_logger->info('Hello World');

        $messages = $protocol->getMessages();
        $messages = $messages[FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][FirePhp::PLUGIN_URI];

        for ($i = 0; $i < count($messages); $i++) {
            if (!preg_match_all('/FirebugTest\.php","Line":' . $lines[$i] . '/', $messages[$i], $m)) {
                $this->fail("File and line does not match for message number: " . ($i + 1));
            }
        }
    }
}
