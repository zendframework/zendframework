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
 * @package    Zend_Wildfire
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Wildfire_WildfireTest::main');
}

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/** Zend_Wildfire_Channel_HttpHeaders */
require_once 'Zend/Wildfire/Channel/HttpHeaders.php';

/** Zend_Wildfire_Plugin_FirePhp */
require_once 'Zend/Wildfire/Plugin/FirePhp.php';

/** Zend_Wildfire_Plugin_FirePhp_Message */
require_once 'Zend/Wildfire/Plugin/FirePhp/Message.php';

/** Zend_Wildfire_Plugin_FirePhp_TableMessage */
require_once 'Zend/Wildfire/Plugin/FirePhp/TableMessage.php';

/** Zend_Controller_Request_HttpTestCase */
require_once 'Zend/Controller/Request/HttpTestCase.php';

/** Zend_Controller_Response_Http */
require_once 'Zend/Controller/Response/HttpTestCase.php';

/** Zend_Controller_Front **/
require_once 'Zend/Controller/Front.php';

/** Zend_Json */
require_once 'Zend/Json.php';

/** Zend_Json_Encoder */
require_once 'Zend/Json/Encoder.php';

/**
 * @category   Zend
 * @package    Zend_Wildfire
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Wildfire
 */
class Zend_Wildfire_WildfireTest extends PHPUnit_Framework_TestCase
{

    protected $_controller = null;
    protected $_request = null;
    protected $_response = null;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Wildfire_WildfireTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        date_default_timezone_set('America/Los_Angeles');
        Zend_Wildfire_Channel_HttpHeaders::destroyInstance();
        Zend_Wildfire_Plugin_FirePhp::destroyInstance();
    }

    public function tearDown()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        Zend_Wildfire_Channel_HttpHeaders::destroyInstance();
        Zend_Wildfire_Plugin_FirePhp::destroyInstance();
    }

    protected function _setupWithFrontController()
    {
        $this->_request    = new Zend_Wildfire_WildfireTest_Request();
        $this->_response   = new Zend_Wildfire_WildfireTest_Response();
        $this->_controller = Zend_Controller_Front::getInstance();
        $this->_controller->resetInstance();
        $this->_controller->setControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files')
                          ->setRequest($this->_request)
                          ->setResponse($this->_response)
                          ->setParam('noErrorHandler', true)
                          ->setParam('noViewRenderer', true)
                          ->throwExceptions(false);

        Zend_Wildfire_Plugin_FirePhp::getInstance()->setOption('includeLineNumbers', false);

        $this->_request->setUserAgentExtensionEnabled(true);
    }

    protected function _setupWithoutFrontController($ModifyOptions=true)
    {
        $this->_request = new Zend_Wildfire_WildfireTest_Request();
        $this->_response = new Zend_Wildfire_WildfireTest_Response();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $channel->setRequest($this->_request);
        $channel->setResponse($this->_response);

        if ($ModifyOptions) {
          Zend_Wildfire_Plugin_FirePhp::getInstance()->setOption('includeLineNumbers', false);
        }

        $this->_request->setUserAgentExtensionEnabled(true);
    }

    public function testNoResponseObject()
    {
        Zend_Wildfire_Plugin_FirePhp::getInstance()->setEnabled(false);
        Zend_Wildfire_Plugin_FirePhp::send('Hello World');

        Zend_Wildfire_Plugin_FirePhp::getInstance()->setEnabled(true);
        try {
            Zend_Wildfire_Plugin_FirePhp::send('Hello World');
            $this->fail('Should throw a response object not initialized error');
        } catch (Exception $e) {
            // success
        }
    }

    public function testIsReady1()
    {
        $this->_setupWithFrontController();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();

        $this->assertTrue($channel->isReady(true));

        $this->_request->setUserAgentExtensionEnabled(false);

        $this->assertFalse($channel->isReady(true));
    }

    public function testIsReady2()
    {
        $this->_setupWithoutFrontController();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();

        $this->assertTrue($channel->isReady());

        $this->_request->setUserAgentExtensionEnabled(false);

        $this->assertFalse($channel->isReady());
    }

    public function testFirePhpPluginInstanciation()
    {
        $this->_setupWithoutFrontController();
        try {
            Zend_Wildfire_Plugin_FirePhp::getInstance();
            Zend_Wildfire_Plugin_FirePhp::init(null);
            $this->fail('Should not be able to re-initialize');
        } catch (Exception $e) {
            // success
        }
    }

    public function testFirePhpPluginEnablement()
    {
        $this->_setupWithoutFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $this->assertFalse($protocol->getMessages());

        $this->assertTrue($firephp->getEnabled());

        $this->assertTrue($firephp->send('Hello World'));

        $messages = array(Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE=>
                          array(Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI=>
                                array('[{"Type":"LOG"},"Hello World"]')));

        $this->assertEquals(serialize($protocol->getMessages()),
                            serialize($messages));

        $this->assertTrue($firephp->setEnabled(false));

        $this->assertFalse($firephp->send('Hello World'));

        $this->assertFalse($protocol->getMessages());
    }

    public function testSetControllerPluginStackIndex1()
    {
        $this->_setupWithoutFrontController();
        $controller = Zend_Controller_Front::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $firephp->send('Hello World');

        $plugins = $controller->getPlugins();

        $this->assertEquals($plugins[999],$channel);
    }

    public function testSetControllerPluginStackIndex2()
    {
        $this->_setupWithoutFrontController(false);

        $controller = Zend_Controller_Front::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();

        Zend_Wildfire_Channel_HttpHeaders::setControllerPluginStackIndex(99);

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $firephp->send('Hello World');

        $plugins = $controller->getPlugins();

        $this->assertEquals($plugins[99],$channel);
    }

    public function testBasicLogging1()
    {
        $this->_setupWithoutFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();

        $message = 'This is a log message!';

        $firephp->send($message);

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $headers = array();
        $headers['X-Wf-Protocol-1'] = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';
        $headers['X-Wf-1-Structure-1'] = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        $headers['X-Wf-1-Plugin-1'] = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6.2';
        $headers['X-Wf-1-1-1-1'] = '41|[{"Type":"LOG"},"This is a log message!"]|';

        $this->assertTrue($this->_response->verifyHeaders($headers));
    }

    public function testBasicLogging2()
    {
        $this->_setupWithFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();

        $message = 'This is a log message!';

        $firephp->send($message);

        $this->_controller->dispatch();

        $headers = array();
        $headers['X-Wf-Protocol-1'] = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';
        $headers['X-Wf-1-Structure-1'] = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        $headers['X-Wf-1-Plugin-1'] = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6.2';
        $headers['X-Wf-1-1-1-1'] = '41|[{"Type":"LOG"},"This is a log message!"]|';

        $this->assertTrue($this->_response->verifyHeaders($headers));
    }

    /**
     * At this point UTF-8 characters are not supported as Zend_Json_Decoder
     * does not support them.
     * Once ZF-4054 is resolved this test must be updated.
     */
    public function testUTF8Logging()
    {
        $this->_setupWithFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();

        $encodedMessage = $message = 'Отладочный';

        if (function_exists('json_encode') && Zend_Json::$useBuiltinEncoderDecoder !== true) {
          $encodedMessage = '\u041e\u0442\u043b\u0430\u0434\u043e\u0447\u043d\u044b\u0439';
        }

        $firephp->send($message);

        $this->_controller->dispatch();

        $headers = array();
        $headers['X-Wf-Protocol-1'] = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';
        $headers['X-Wf-1-Structure-1'] = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        $headers['X-Wf-1-Plugin-1'] = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6.2';
        $headers['X-Wf-1-1-1-1'] = (strlen($encodedMessage)+19).'|[{"Type":"LOG"},"'.$encodedMessage.'"]|';

        $this->assertTrue($this->_response->verifyHeaders($headers));
    }

    public function testCyclicalObjectLogging()
    {
        $this->_setupWithFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();

        $obj1 = new Zend_Wildfire_WildfireTest_JsonEncodingTestClass();
        $obj2 = new Zend_Wildfire_WildfireTest_JsonEncodingTestClass();

        $obj1->child = $obj2;
        $obj2->child = $obj1;

        $firephp->send($obj1);

        $this->_controller->dispatch();

        $headers = array();
        $headers['X-Wf-Protocol-1'] = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';
        $headers['X-Wf-1-Structure-1'] = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        $headers['X-Wf-1-Plugin-1'] = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6.2';
        $headers['X-Wf-1-1-1-1'] = '257|[{"Type":"LOG"},{"__className":"Zend_Wildfire_WildfireTest_JsonEncodingTestClass","undeclared:child":{"__className":"Zend_Wildfire_WildfireTest_JsonEncodingTestClass","undeclared:child":"** Recursion (Zend_Wildfire_WildfireTest_JsonEncodingTestClass) **"}}]|';

        $this->assertTrue($this->_response->verifyHeaders($headers));
    }

    /**
     * @group ZF-4927
     */
    public function testAdvancedLogging()
    {
        Zend_Wildfire_Plugin_FirePhp::getInstance()->setOption('maxTraceDepth',0);

        $this->_setupWithoutFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();

        $message = 'This is a log message!';
        $label = 'Test Label';
        $table = array('Summary line for the table',
                       array(
                           array('Column 1', 'Column 2'),
                           array('Row 1 c 1',' Row 1 c 2'),
                           array('Row 2 c 1',' Row 2 c 2')
                       )
                      );

        $firephp->send($message, null, 'TRACE');
        $firephp->send($table, null, 'TABLE');

        Zend_Wildfire_Plugin_FirePhp::send($message, $label);
        Zend_Wildfire_Plugin_FirePhp::send($message, $label, Zend_Wildfire_Plugin_FirePhp::DUMP);

        try {
          throw new Exception('Test Exception');
        } catch (Exception $e) {
          Zend_Wildfire_Plugin_FirePhp::send($e);
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
                                array(1=>'[{"Type":"TABLE"},["Summary line for the table",[["Column 1","Column 2"],["Row 1 c 1"," Row 1 c 2"],["Row 2 c 1"," Row 2 c 2"]]]]',
                                      2=>'[{"Type":"LOG","Label":"Test Label"},"This is a log message!"]')),
                          Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_DUMP=>
                          array(Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI=>
                                array('{"Test Label":"This is a log message!"}')));

        $qued_messages = $protocol->getMessages();
        unset($qued_messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0]);
        unset($qued_messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE][Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][3]);

        $this->assertEquals(serialize($qued_messages),
                            serialize($messages));
    }


    public function testMessage()
    {
        $this->_setupWithoutFrontController();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $message = new Zend_Wildfire_Plugin_FirePhp_Message(Zend_Wildfire_Plugin_FirePhp::LOG, 'Message 1');


        $this->assertEquals($message->getStyle(), Zend_Wildfire_Plugin_FirePhp::LOG);

        $message->setStyle(Zend_Wildfire_Plugin_FirePhp::INFO);

        $this->assertEquals($message->getStyle(), Zend_Wildfire_Plugin_FirePhp::INFO);


        $this->assertNull($message->getLabel());

        $message->setLabel('Label 1');

        $this->assertEquals($message->getLabel(), 'Label 1');


        $this->assertFalse($message->getDestroy());

        $message->setDestroy(true);

        $this->assertTrue($message->getDestroy());


        $this->assertEquals($message->getMessage(), 'Message 1');

        $message->setMessage('Message 2');

        $this->assertEquals($message->getMessage(), 'Message 2');



        $message->setDestroy(true);
        $this->assertfalse(Zend_Wildfire_Plugin_FirePhp::send($message));

        $message->setDestroy(false);
        $this->assertTrue(Zend_Wildfire_Plugin_FirePhp::send($message));

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertEquals($messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0],
                            '[{"Type":"INFO","Label":"Label 1"},"Message 2"]');
    }


    /**
     * @group ZF-5742
     */
    public function testTableMessage()
    {
        $table = new Zend_Wildfire_Plugin_FirePhp_TableMessage('TestMessage');

        $this->assertEquals($table->getLabel(), 'TestMessage');

        try {
            $table->getLastRow();
            $this->fail('Should throw exception when no rows exist');
        } catch (Exception $e) {
            // success
        }

        $row = array('col1','col2');

        $this->assertEquals($table->getRowCount(), 0);

        try {
            $table->getRowAt(1);
            $this->fail('Should throw exception as no rows present');
        } catch (Exception $e) {
            // success
        }

        try {
            $table->setRowAt(1,array());
            $this->fail('Should throw exception as no rows present');
        } catch (Exception $e) {
            // success
        }

        $table->addRow($row);

        $this->assertEquals($table->getMessage(), array($row));
        $this->assertEquals($table->getLastRow(), $row);

        $this->assertEquals($table->getRowCount(), 1);

        $table->addRow($row);

        $this->assertEquals($table->getMessage(), array($row,
                                                        $row));

        $this->assertEquals($table->getRowCount(), 2);
        $this->assertEquals($table->getLastRow(), $row);

        try {
            $table->getRowAt(2);
            $this->fail('Should throw exception as index is out of bounds');
        } catch (Exception $e) {
            // success
        }

        try {
            $table->setRowAt(2,array());
            $this->fail('Should throw exception as index is out of bounds');
        } catch (Exception $e) {
            // success
        }

        $rowOld = $table->getRowAt(1);
        $this->assertEquals($rowOld, $row);

        $rowOld[1] = 'Column2';
        $table->setRowAt(1, $rowOld);

        $this->assertEquals($table->getRowAt(1), $rowOld);
        $this->assertEquals($table->getLastRow(), $rowOld);

        $this->assertEquals($table->getMessage(), array($row,
                                                        $rowOld));

        $header = array('hc1', 'hc2');
        $table->setHeader($header);

        $this->assertEquals($table->getMessage(), array($header,
                                                        $row,
                                                        $rowOld));
    }

    /**
     * @group ZF-6396
     */
    public function testTableMessage2()
    {
        $this->_setupWithoutFrontController();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $table = new Zend_Wildfire_Plugin_FirePhp_TableMessage('TestMessage');
        $table->setHeader(array('col1','col2'));
        $table->setBuffered(true);

        Zend_Wildfire_Plugin_FirePhp::send($table);

        $cell = new ArrayObject();
        $cell->append("item1");
        $cell->append("item2");

        $table->addRow(array("row1", $cell));

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertEquals($messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0],
                            '[{"Type":"TABLE","Label":"TestMessage"},[["col1","col2"],["row1",{"__className":"ArrayObject","undeclared:0":"item1","undeclared:1":"item2"}]]]');
    }

    public function testMessageGroups()
    {
        $this->_setupWithFrontController();

        Zend_Wildfire_Plugin_FirePhp::group('Test Group');
        Zend_Wildfire_Plugin_FirePhp::send('Test Message');
        Zend_Wildfire_Plugin_FirePhp::groupEnd();

        $this->_controller->dispatch();

        $headers = array();
        $headers['X-Wf-Protocol-1'] = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';
        $headers['X-Wf-1-Structure-1'] = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        $headers['X-Wf-1-Plugin-1'] = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6.2';
        $headers['X-Wf-1-1-1-1'] = '50|[{"Type":"GROUP_START","Label":"Test Group"},null]|';
        $headers['X-Wf-1-1-1-2'] = '31|[{"Type":"LOG"},"Test Message"]|';
        $headers['X-Wf-1-1-1-3'] = '27|[{"Type":"GROUP_END"},null]|';

        $this->assertTrue($this->_response->verifyHeaders($headers));
    }

    public function testMessageComparison()
    {
        $label = 'Message';

        $message1 = new Zend_Wildfire_Plugin_FirePhp_Message(Zend_Wildfire_Plugin_FirePhp::LOG, $label);

        $message2 = new Zend_Wildfire_Plugin_FirePhp_Message(Zend_Wildfire_Plugin_FirePhp::LOG, $label);

        $this->assertNotEquals($message1,$message2);
    }

    public function testBufferedMessage1()
    {
        $this->_setupWithoutFrontController();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $message = new Zend_Wildfire_Plugin_FirePhp_Message(Zend_Wildfire_Plugin_FirePhp::LOG, 'Message 1');
        $this->assertFalse($message->setBuffered(true));

        Zend_Wildfire_Plugin_FirePhp::send($message);

        $this->assertFalse($protocol->getMessages());

        $message->setMessage('Message 2');

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertEquals($messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0],
                            '[{"Type":"LOG"},"Message 2"]');
    }

    public function testBufferedMessage2()
    {
        $this->_setupWithFrontController();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $message = new Zend_Wildfire_Plugin_FirePhp_Message(Zend_Wildfire_Plugin_FirePhp::LOG, 'Message 1');
        $this->assertFalse($message->setBuffered(true));

        Zend_Wildfire_Plugin_FirePhp::send($message);

        $this->assertFalse($protocol->getMessages());

        $message->setMessage('Message 2');

        $this->_controller->dispatch();

        $messages = $protocol->getMessages();

        $this->assertEquals($messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0],
                            '[{"Type":"LOG"},"Message 2"]');
    }

    public function testDestroyedBufferedMessage()
    {
        $this->_setupWithoutFrontController();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $message = new Zend_Wildfire_Plugin_FirePhp_Message(Zend_Wildfire_Plugin_FirePhp::LOG, 'Message 1');
        $message->setBuffered(true);

        Zend_Wildfire_Plugin_FirePhp::send($message);

        $this->assertEquals($message->getStyle(), Zend_Wildfire_Plugin_FirePhp::LOG);

        $message->setStyle(Zend_Wildfire_Plugin_FirePhp::INFO);

        $this->assertEquals($message->getStyle(), Zend_Wildfire_Plugin_FirePhp::INFO);

        $message->setDestroy(true);

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $this->assertFalse($protocol->getMessages());
    }

    public function testChannelInstanciation()
    {
        $this->_setupWithoutFrontController();
        try {
            Zend_Wildfire_Channel_HttpHeaders::getInstance();
            Zend_Wildfire_Channel_HttpHeaders::init(null);
            $this->fail('Should not be able to re-initialize');
        } catch (Exception $e) {
            // success
        }
    }

    public function testChannelFlush()
    {
        $this->_setupWithoutFrontController(false);

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();

        $this->assertFalse($channel->flush(), 'Nothing to flush - No messages');

        Zend_Wildfire_Plugin_FirePhp::send('Hello World');

        $this->assertTrue($channel->flush(), 'One message to flush');

        $this->_request->setUserAgentExtensionEnabled(false);

        $this->assertFalse($channel->flush(), 'Nothing to flush - Extension not in UserAgent');
    }

    public function testFirePhpPluginSubclass()
    {

        $firephp = Zend_Wildfire_Plugin_FirePhp::init('Zend_Wildfire_WildfireTest_FirePhpPlugin');

        $this->assertEquals(get_class($firephp),
                            'Zend_Wildfire_WildfireTest_FirePhpPlugin');

        Zend_Wildfire_Plugin_FirePhp::destroyInstance();

        try {
            Zend_Wildfire_Plugin_FirePhp::init('Zend_Wildfire_WildfireTest_Request');
            $this->fail('Should not be able to initialize');
        } catch (Exception $e) {
            // success
        }

        $this->assertNull(Zend_Wildfire_Plugin_FirePhp::getInstance(true));

        try {
            Zend_Wildfire_Plugin_FirePhp::init(array());
            $this->fail('Should not be able to initialize');
        } catch (Exception $e) {
            // success
        }

        $this->assertNull(Zend_Wildfire_Plugin_FirePhp::getInstance(true));
    }

    public function testHttpHeadersChannelSubclass()
    {

        $firephp = Zend_Wildfire_Channel_HttpHeaders::init('Zend_Wildfire_WildfireTest_HttpHeadersChannel');

        $this->assertEquals(get_class($firephp),
                            'Zend_Wildfire_WildfireTest_HttpHeadersChannel');

        Zend_Wildfire_Channel_HttpHeaders::destroyInstance();

        try {
            Zend_Wildfire_Channel_HttpHeaders::init('Zend_Wildfire_WildfireTest_Request');
            $this->fail('Should not be able to initialize');
        } catch (Exception $e) {
            // success
        }

        $this->assertNull(Zend_Wildfire_Channel_HttpHeaders::getInstance(true));

        try {
            Zend_Wildfire_Channel_HttpHeaders::init(array());
            $this->fail('Should not be able to initialize');
        } catch (Exception $e) {
            // success
        }

        $this->assertNull(Zend_Wildfire_Channel_HttpHeaders::getInstance(true));
    }

    /**
     * @group ZF-4843
     */
    public function testResourceLogging()
    {
        $this->_setupWithoutFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $firephp->send(array('file'=>tmpfile()));

        $messages = $protocol->getMessages();

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [0];

        $this->assertEquals(substr($message,0,41)
                            , '[{"Type":"LOG"},{"file":"** Resource id #');
    }

    /**
     * @group ZF-4863
     */
    public function testLargeMessages()
    {
        $this->_setupWithoutFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $data = array();
        for ($i=0 ; $i < 400 ; $i++) {
          $data[] = 'Test Data '.$i;
        }
        $firephp->send($data);

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $messages = $this->_response->getHeadersForTesting();

        $this->assertTrue(substr($messages[3]['value'],0,10)=='6308|[{"Ty'
                          && substr($messages[3]['value'],-8,8)==',"Test|\\'
                          && substr($messages[4]['value'],0,10)=='| Data 318'
                          && substr($messages[4]['value'],-7,7)=='399"]]|');
    }

    /**
     * @group ZF-5540
     */
    public function testOptions()
    {
        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();

        $_options = array(
            'traceOffset' => 1, /* The offset in the trace which identifies the source of the message */
            'maxTraceDepth' => 99, /* Maximum depth for stack traces */
            'maxObjectDepth' => 10, /* The maximum depth to traverse objects when encoding */
            'maxArrayDepth' => 20, /* The maximum depth to traverse nested arrays when encoding */
            'includeLineNumbers' => true /* Whether to include line and file info for each message */
        );

        $this->assertEquals($firephp->getOptions(), $_options, 'Ensure defaults stay the same');

        $this->assertEquals($firephp->setOption('includeLineNumbers',false),
                            $_options['includeLineNumbers'],
                            'Should return old value');

        $this->assertEquals($firephp->getOption('includeLineNumbers'),
                            false,
                            'Should contain new value');
    }

    /**
     * @group ZF-5540
     */
    public function testObjectFilter()
    {
        $this->_setupWithoutFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $obj = new Zend_Wildfire_WildfireTest_TestObject1();

        $firephp->send($obj);

        $firephp->setObjectFilter('Zend_Wildfire_WildfireTest_TestObject1',array('value', 'protectedStatic'));

        $firephp->send($obj);

        $messages = $protocol->getMessages();

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [0];

        if (version_compare(phpversion(), '5.3' , '<')) {

          $this->assertEquals($message,
                              '[{"Type":"LOG"},{"__className":"Zend_Wildfire_WildfireTest_TestObject1","public:name":"Name","public:value":"Value","protected:static:protectedStatic":"** Need PHP 5.3 to get value **"}]');
        } else
        if (version_compare(phpversion(), '5.3' , '>=')) {

          $this->assertEquals($message,
                              '[{"Type":"LOG"},{"__className":"Zend_Wildfire_WildfireTest_TestObject1","public:name":"Name","public:value":"Value","protected:static:protectedStatic":"ProtectedStatic"}]');
        }

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [1];

        $this->assertEquals($message,
                            '[{"Type":"LOG"},{"__className":"Zend_Wildfire_WildfireTest_TestObject1","public:name":"Name","public:value":"** Excluded by Filter **","protected:static:protectedStatic":"** Excluded by Filter **"}]');
    }

    public function testObjectMembers()
    {
        $this->_setupWithoutFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $obj = new Zend_Wildfire_WildfireTest_TestObject2();

        $firephp->send($obj);

        $messages = $protocol->getMessages();

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [0];

        if (version_compare(phpversion(), '5.3' , '<')) {

          $this->assertEquals($message,
                              '[{"Type":"LOG"},{"__className":"Zend_Wildfire_WildfireTest_TestObject2","public:public":"Public","private:private":"Private","protected:protected":"Protected","public:static:static":"Static","private:static:staticPrivate":"** Need PHP 5.3 to get value **","protected:static:staticProtected":"** Need PHP 5.3 to get value **"}]');

        } else
        if (version_compare(phpversion(), '5.3' , '>=')) {

          $this->assertEquals($message,
                              '[{"Type":"LOG"},{"__className":"Zend_Wildfire_WildfireTest_TestObject2","public:public":"Public","private:private":"Private","protected:protected":"Protected","public:static:static":"Static","private:static:staticPrivate":"StaticPrivate","protected:static:staticProtected":"StaticProtected"}]');
        }
    }

    /**
     * @group ZF-5540
     */
    public function testMaxObjectArrayDepth()
    {
        $this->_setupWithoutFrontController();

        $firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $firephp->setOption('maxObjectDepth',2);
        $firephp->setOption('maxArrayDepth',1);

        $obj = new Zend_Wildfire_WildfireTest_TestObject3();
        $obj->testArray = array('val1',array('val2',array('Hello World')));
        $obj->child = clone $obj;
        $obj->child->child = clone $obj;

        $firephp->send($obj);


        $table = array();
        $table[] = array('Col1', 'Col2');
        $table[] = array($obj, $obj);

        $firephp->send($table, 'Label', Zend_Wildfire_Plugin_FirePhp::TABLE);


        $messages = $protocol->getMessages();

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [0];

        $this->assertEquals($message,
                            '[{"Type":"LOG"},{"__className":"Zend_Wildfire_WildfireTest_TestObject3","public:name":"Name","public:value":"Value","undeclared:testArray":["val1","** Max Array Depth (1) **"],"undeclared:child":{"__className":"Zend_Wildfire_WildfireTest_TestObject3","public:name":"Name","public:value":"Value","undeclared:testArray":["val1","** Max Array Depth (1) **"],"undeclared:child":"** Max Object Depth (2) **"}}]');

        $message = $messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI]
                            [1];

        $this->assertEquals($message,
                            '[{"Type":"TABLE","Label":"Label"},[["Col1","Col2"],[{"__className":"Zend_Wildfire_WildfireTest_TestObject3","public:name":"Name","public:value":"Value","undeclared:testArray":["val1","** Max Array Depth (1) **"],"undeclared:child":{"__className":"Zend_Wildfire_WildfireTest_TestObject3","public:name":"Name","public:value":"Value","undeclared:testArray":["val1","** Max Array Depth (1) **"],"undeclared:child":"** Max Object Depth (2) **"}},{"__className":"Zend_Wildfire_WildfireTest_TestObject3","public:name":"Name","public:value":"Value","undeclared:testArray":["val1","** Max Array Depth (1) **"],"undeclared:child":{"__className":"Zend_Wildfire_WildfireTest_TestObject3","public:name":"Name","public:value":"Value","undeclared:testArray":["val1","** Max Array Depth (1) **"],"undeclared:child":"** Max Object Depth (2) **"}}]]]');
    }

}

class Zend_Wildfire_WildfireTest_TestObject1
{
  var $name = 'Name';
  var $value = 'Value';
  protected static $protectedStatic = 'ProtectedStatic';
}

class Zend_Wildfire_WildfireTest_TestObject2
{
  var $public = 'Public';
  private $private = 'Private';
  protected $protected = 'Protected';

  static $static = 'Static';
  static private $staticPrivate = 'StaticPrivate';
  static protected $staticProtected = 'StaticProtected';
}

class Zend_Wildfire_WildfireTest_TestObject3
{
  var $name = 'Name';
  var $value = 'Value';
}

class Zend_Wildfire_WildfireTest_JsonEncodingTestClass
{
}


class Zend_Wildfire_WildfireTest_FirePhpPlugin extends Zend_Wildfire_Plugin_FirePhp
{
}

class Zend_Wildfire_WildfireTest_HttpHeadersChannel extends Zend_Wildfire_Channel_HttpHeaders
{
}

class Zend_Wildfire_WildfireTest_Request extends Zend_Controller_Request_HttpTestCase
{

    protected $_enabled = false;

    public function setUserAgentExtensionEnabled($enabled) {
        $this->_enabled = $enabled;
    }

    public function getHeader($header, $default = null)
    {
        if ($header == 'User-Agent') {
            if ($this->_enabled) {
                return 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14 FirePHP/0.1.0';
            } else {
                return 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14';
            }
        }
    }
}


class Zend_Wildfire_WildfireTest_Response extends Zend_Controller_Response_HttpTestCase
{

    public function getHeadersForTesting()
    {
        return $this->getHeaders();
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

if (PHPUnit_MAIN_METHOD == 'Zend_Wildfire_WildfireTest::main') {
    Zend_Wildfire_WildfireTest::main();
}
