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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Log_Writer_Firebug */
require_once 'Zend/Log/Writer/Firebug.php';

/** Zend_Log_Formatter_Firebug */
require_once 'Zend/Log/Formatter/Firebug.php';

/** Zend_Wildfire_Channel_HttpHeaders */
require_once 'Zend/Wildfire/Channel/HttpHeaders.php';

/** Zend_Wildfire_Plugin_FirePhp */
require_once 'Zend/Wildfire/Plugin/FirePhp.php';

/** Zend_Controller_Request_Http */
require_once 'Zend/Controller/Request/Http.php';

/** Zend_Controller_Response_Http */
require_once 'Zend/Controller/Response/Http.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class Zend_Log_Writer_FirebugTest extends PHPUnit_Framework_TestCase
{


    protected $_controller = null;
    protected $_request = null;
    protected $_response = null;
    protected $_writer = null;
    protected $_logger = null;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Log_Writer_FirebugTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        date_default_timezone_set('America/Los_Angeles');

        // Reset front controller to reset registered plugins and
        // registered request/response objects
        Zend_Controller_Front::getInstance()->resetInstance();

        $this->_request = new Zend_Log_Writer_FirebugTest_Request();
        $this->_response = new Zend_Log_Writer_FirebugTest_Response();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $channel->setRequest($this->_request);
        $channel->setResponse($this->_response);

        $this->_writer = new Zend_Log_Writer_Firebug();

        // Explicitly enable writer as it is disabled by default
        // when running from the command line.
        $this->_writer->setEnabled(true);

        $this->_logger = new Zend_Log($this->_writer);

        Zend_Wildfire_Plugin_FirePhp::getInstance()->setOption('includeLineNumbers', false);
    }

    public function tearDown()
    {
        Zend_Wildfire_Channel_HttpHeaders::destroyInstance();
        Zend_Wildfire_Plugin_FirePhp::destroyInstance();
    }


    /**
     * Test for ZF-3960
     *
     * Zend_Log_Writer_Firebug should be automatically disabled when
     * run from the command line
     */
    public function testZf3960()
    {
        Zend_Wildfire_Channel_HttpHeaders::destroyInstance();
        Zend_Wildfire_Plugin_FirePhp::destroyInstance();

        $log = new Zend_Log();
        $writerFirebug = new Zend_Log_Writer_Firebug();
        $log->addWriter($writerFirebug);
        $log->log('hi', 2);
    }

    /**
     * @group ZF-4952
     */
    public function testSetFormatter()
    {
        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $this->_logger->log('Test Message 1', Zend_Log::INFO);

        $formatter = new Zend_Log_Writer_FirebugTest_Formatter();
        $this->_writer->setFormatter($formatter);

        $this->_logger->setEventItem('testLabel','Test Label');

        $this->_logger->log('Test Message 2', Zend_Log::INFO);

        $messages = $protocol->getMessages();

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [0];

        $this->assertEquals($message,
                            '[{"Type":"INFO"},"Test Message 1"]');

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [1];

        $this->assertEquals($message,
                            '[{"Type":"INFO"},"Test Label : Test Message 2"]');
    }

    /**
     * @group ZF-4952
     */
    public function testEventItemLabel()
    {
        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);


        $this->_logger->log('Test Message 1', Zend_Log::INFO);

        $this->_logger->setEventItem('firebugLabel','Test Label');

        $this->_logger->log('Test Message 2', Zend_Log::INFO);

        $messages = $protocol->getMessages();

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [0];

        $this->assertEquals($message,
                            '[{"Type":"INFO"},"Test Message 1"]');

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [1];

        $this->assertEquals($message,
                            '[{"Type":"INFO","Label":"Test Label"},"Test Message 2"]');
    }

    public function testLogStyling()
    {
        $this->assertEquals($this->_writer->getDefaultPriorityStyle(),
                            Zend_Wildfire_Plugin_FirePhp::LOG);
        $this->assertEquals($this->_writer->setDefaultPriorityStyle(Zend_Wildfire_Plugin_FirePhp::WARN),
                            Zend_Wildfire_Plugin_FirePhp::LOG);
        $this->assertEquals($this->_writer->getDefaultPriorityStyle(),
                            Zend_Wildfire_Plugin_FirePhp::WARN);

        $this->assertEquals($this->_writer->getPriorityStyle(9),
                            false);
        $this->assertEquals($this->_writer->setPriorityStyle(9,Zend_Wildfire_Plugin_FirePhp::WARN),
                            true);
        $this->assertEquals($this->_writer->getPriorityStyle(9),
                            Zend_Wildfire_Plugin_FirePhp::WARN);
        $this->assertEquals($this->_writer->setPriorityStyle(9,Zend_Wildfire_Plugin_FirePhp::LOG),
                            Zend_Wildfire_Plugin_FirePhp::WARN);
    }

    public function testBasicLogging()
    {
        $message = 'This is a log message!';

        $this->_logger->log($message, Zend_Log::INFO);

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

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
        Zend_Wildfire_Plugin_FirePhp::getInstance()->setOption('maxTraceDepth',0);

        $message = 'This is a log message!';
        $label = 'Test Label';
        $table = array('Summary line for the table',
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
          throw new Exception('Test Exception');
        } catch (Exception $e) {
          $this->_logger->err($e);
        }

        try {
            Zend_Wildfire_Plugin_FirePhp::send($message, $label, 'UNKNOWN');
            $this->fail('Should not be able to log with undefined log style');
        } catch (Exception $e) {
            // success
        }

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $messages = array(Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE=>
                          array(Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI=>
                                array(1=>'[{"Type":"TABLE"},["Summary line for the table",[["Column 1","Column 2"],["Row 1 c 1"," Row 1 c 2"],["Row 2 c 1"," Row 2 c 2"]]]]')));

        $qued_messages = $protocol->getMessages();

        unset($qued_messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0]);
        unset($qued_messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][2]);

        $this->assertEquals(serialize($qued_messages),
                            serialize($messages));
    }
}

class Zend_Log_Writer_FirebugTest_Formatter extends Zend_Log_Formatter_Firebug
{
    public function format($event)
    {
        return $event['testLabel'].' : '.$event['message'];
    }
}


class Zend_Log_Writer_FirebugTest_Request extends Zend_Controller_Request_Http
{
    public function getHeader($header)
    {
        if ($header == 'User-Agent') {
            return 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14 FirePHP/0.1.0';
        }
    }
}


class Zend_Log_Writer_FirebugTest_Response extends Zend_Controller_Response_Http
{

    public function canSendHeaders($throw = false)
    {
        return true;
    }

    public function verifyHeaders($headers)
    {

        $response_headers = $this->getHeaders();
        if (!$response_headers) {
            return false;
        }

        $keys1 = array_keys($headers);
        sort($keys1);
        $keys1 = serialize($keys1);

        $keys2 = array();
        foreach ($response_headers as $header ) {
            $keys2[] = $header['name'];
        }
        sort($keys2);
        $keys2 = serialize($keys2);

        if ($keys1 != $keys2) {
            return false;
        }

        $values1 = array_values($headers);
        sort($values1);
        $values1 = serialize($values1);

        $values2 = array();
        foreach ($response_headers as $header ) {
            $values2[] = $header['value'];
        }
        sort($values2);
        $values2 = serialize($values2);

        if ($values1 != $values2) {
            return false;
        }

        return true;
    }

}
