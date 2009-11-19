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
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Soap_ClientTest::main');
}

require_once dirname(__FILE__)."/../../TestHelper.php";

/** PHPUnit Test Case */
require_once "PHPUnit/Framework/TestCase.php";

/** Zend_Soap_Server */
require_once 'Zend/Soap/Server.php';

/** Zend_Soap_Client */
require_once 'Zend/Soap/Client.php';

require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 */
class Zend_Soap_ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }
    }

    public function testSetOptions()
    {
        /*************************************************************
         * ------ Test WSDL mode options -----------------------------
         *************************************************************/
        $client = new Zend_Soap_Client();

        $this->assertTrue($client->getOptions() == array('encoding' => 'UTF-8', 'soap_version' => SOAP_1_2));

        $ctx = stream_context_create();

        $nonWsdlOptions = array('soap_version'   => SOAP_1_1,
                                'classmap'       => array('TestData1' => 'Zend_Soap_Client_TestData1',
                                                    'TestData2' => 'Zend_Soap_Client_TestData2',),
                                'encoding'       => 'ISO-8859-1',
                                'uri'            => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                                'location'       => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                                'use'            => SOAP_ENCODED,
                                'style'          => SOAP_RPC,

                                'login'          => 'http_login',
                                'password'       => 'http_password',

                                'proxy_host'     => 'proxy.somehost.com',
                                'proxy_port'     => 8080,
                                'proxy_login'    => 'proxy_login',
                                'proxy_password' => 'proxy_password',

                                'local_cert'     => dirname(__FILE__).'/_files/cert_file',
                                'passphrase'     => 'some pass phrase',

                                'stream_context' => $ctx,
                                'cache_wsdl'     => 8,
                                'features'       => 4,

                                'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5);

        $client->setOptions($nonWsdlOptions);
        $this->assertTrue($client->getOptions() == $nonWsdlOptions);


        /*************************************************************
         * ------ Test non-WSDL mode options -----------------------------
         *************************************************************/
        $client1 = new Zend_Soap_Client();

        $this->assertTrue($client1->getOptions() == array('encoding' => 'UTF-8', 'soap_version' => SOAP_1_2));

        $wsdlOptions = array('soap_version'   => SOAP_1_1,
                             'wsdl'           => dirname(__FILE__).'/_files/wsdl_example.wsdl',
                             'classmap'       => array('TestData1' => 'Zend_Soap_Client_TestData1',
                                                 'TestData2' => 'Zend_Soap_Client_TestData2',),
                             'encoding'       => 'ISO-8859-1',

                             'login'          => 'http_login',
                             'password'       => 'http_password',

                             'proxy_host'     => 'proxy.somehost.com',
                             'proxy_port'     => 8080,
                             'proxy_login'    => 'proxy_login',
                             'proxy_password' => 'proxy_password',

                             'local_cert'     => dirname(__FILE__).'/_files/cert_file',
                             'passphrase'     => 'some pass phrase',

                             'stream_context' => $ctx,

                             'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5);

        $client1->setOptions($wsdlOptions);
        $this->assertTrue($client1->getOptions() == $wsdlOptions);
    }

    public function testGetOptions()
    {
        $client = new Zend_Soap_Client();

        $this->assertTrue($client->getOptions() == array('encoding' => 'UTF-8', 'soap_version' => SOAP_1_2));

        $options = array('soap_version'   => SOAP_1_1,
                         'wsdl'           => dirname(__FILE__).'/_files/wsdl_example.wsdl',

                         'classmap'       => array('TestData1' => 'Zend_Soap_Client_TestData1',
                                             'TestData2' => 'Zend_Soap_Client_TestData2',),
                         'encoding'       => 'ISO-8859-1',
                         'uri'            => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                         'location'       => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                         'use'            => SOAP_ENCODED,
                         'style'          => SOAP_RPC,

                         'login'          => 'http_login',
                         'password'       => 'http_password',

                         'proxy_host'     => 'proxy.somehost.com',
                         'proxy_port'     => 8080,
                         'proxy_login'    => 'proxy_login',
                         'proxy_password' => 'proxy_password',

                         'local_cert'     => dirname(__FILE__).'/_files/cert_file',
                         'passphrase'     => 'some pass phrase',

                         'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5);

        $client->setOptions($options);
        $this->assertTrue($client->getOptions() == $options);
    }

    /**
     * @group ZF-8053
     */
    public function testGetAndSetUserAgentOption()
    {
        $client = new Zend_Soap_Client();
        $this->assertNull($client->getUserAgent());

        $client->setUserAgent('agent1');
        $this->assertEquals('agent1', $client->getUserAgent());

        $client->setOptions(array(
            'user_agent' => 'agent2'
        ));
        $this->assertEquals('agent2', $client->getUserAgent());

        $client->setOptions(array(
            'useragent' => 'agent3'
        ));
        $this->assertEquals('agent3', $client->getUserAgent());

        $client->setOptions(array(
            'userAgent' => 'agent4'
        ));
        $this->assertEquals('agent4', $client->getUserAgent());

        $options = $client->getOptions();
        $this->assertEquals('agent4', $options['user_agent']);
    }

    /**
     * @group ZF-6954
     */
    public function testUserAgentAllowsEmptyString()
    {
        $client = new Zend_Soap_Client();
        $this->assertNull($client->getUserAgent());
        $options = $client->getOptions();
        $this->assertArrayNotHasKey('user_agent', $options);

        $client->setUserAgent('');
        $this->assertEquals('', $client->getUserAgent());
        $options = $client->getOptions();
        $this->assertEquals('', $options['user_agent']);

        $client->setUserAgent(null);
        $this->assertNull($client->getUserAgent());
        $options = $client->getOptions();
        $this->assertArrayNotHasKey('user_agent', $options);
    }

    public function testGetFunctions()
    {
        $server = new Zend_Soap_Server(dirname(__FILE__) . '/_files/wsdl_example.wsdl');
        $server->setClass('Zend_Soap_Client_TestClass');

        $client = new Zend_Soap_Client_Local($server, dirname(__FILE__) . '/_files/wsdl_example.wsdl');

        $this->assertTrue($client->getFunctions() == array('string testFunc1()',
                                                           'string testFunc2(string $who)',
                                                           'string testFunc3(string $who, int $when)',
                                                           'string testFunc4()'));
    }

    /**
     * @todo Implement testGetTypes().
     */
    public function testGetTypes()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    public function testGetLastRequest()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testGetLastRequest() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Zend_Soap_Server(dirname(__FILE__) . '/_files/wsdl_example.wsdl');
        $server->setClass('Zend_Soap_Client_TestClass');

        $client = new Zend_Soap_Client_Local($server, dirname(__FILE__) . '/_files/wsdl_example.wsdl');

        // Perform request
        $client->testFunc2('World');

        $expectedRequest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                         . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                         .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                         .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                         .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                         .     '<env:Body>'
                         .         '<env:testFunc2 env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                         .             '<who xsi:type="xsd:string">World</who>'
                         .         '</env:testFunc2>'
                         .     '</env:Body>'
                         . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastRequest(), $expectedRequest);
    }

    public function testGetLastResponse()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testGetLastResponse() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Zend_Soap_Server(dirname(__FILE__) . '/_files/wsdl_example.wsdl');
        $server->setClass('Zend_Soap_Client_TestClass');

        $client = new Zend_Soap_Client_Local($server, dirname(__FILE__) . '/_files/wsdl_example.wsdl');

        // Perform request
        $client->testFunc2('World');

        $expectedResponse = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                          . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                          .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                          .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                          .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                          .     '<env:Body xmlns:rpc="http://www.w3.org/2003/05/soap-rpc">'
                          .         '<env:testFunc2Response env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                          .             '<rpc:result>testFunc2Return</rpc:result>'
                          .             '<testFunc2Return xsi:type="xsd:string">Hello World!</testFunc2Return>'
                          .         '</env:testFunc2Response>'
                          .     '</env:Body>'
                          . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastResponse(), $expectedResponse);
    }

    public function testCallInvoke()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testCallInvoke() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Zend_Soap_Server(dirname(__FILE__) . '/_files/wsdl_example.wsdl');
        $server->setClass('Zend_Soap_Client_TestClass');

        $client = new Zend_Soap_Client_Local($server, dirname(__FILE__) . '/_files/wsdl_example.wsdl');

        $this->assertEquals($client->testFunc2('World'), 'Hello World!');
    }

    public function testSetOptionsWithZendConfig()
    {
        $ctx = stream_context_create();

        $nonWsdlOptions = array('soap_version'   => SOAP_1_1,
                                'classmap'       => array('TestData1' => 'Zend_Soap_Client_TestData1',
                                                    'TestData2' => 'Zend_Soap_Client_TestData2',),
                                'encoding'       => 'ISO-8859-1',
                                'uri'            => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                                'location'       => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                                'use'            => SOAP_ENCODED,
                                'style'          => SOAP_RPC,

                                'login'          => 'http_login',
                                'password'       => 'http_password',

                                'proxy_host'     => 'proxy.somehost.com',
                                'proxy_port'     => 8080,
                                'proxy_login'    => 'proxy_login',
                                'proxy_password' => 'proxy_password',

                                'local_cert'     => dirname(__FILE__).'/_files/cert_file',
                                'passphrase'     => 'some pass phrase',

                                'stream_context' => $ctx,

                                'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5
        );

        $config = new Zend_Config($nonWsdlOptions);

        $client = new Zend_Soap_Client(null, $config);

        $this->assertEquals($nonWsdlOptions, $client->getOptions());
    }

    public function testSetInputHeaders()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testSetInputHeaders() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Zend_Soap_Server(dirname(__FILE__) . '/_files/wsdl_example.wsdl');
        $server->setClass('Zend_Soap_Client_TestClass');

        $client = new Zend_Soap_Client_Local($server, dirname(__FILE__) . '/_files/wsdl_example.wsdl');

        // Add request header
        $client->addSoapInputHeader(new SoapHeader('http://www.example.com/namespace', 'MyHeader1', 'SOAP header content 1'));
        // Add permanent request header
        $client->addSoapInputHeader(new SoapHeader('http://www.example.com/namespace', 'MyHeader2', 'SOAP header content 2'), true);

        // Perform request
        $client->testFunc2('World');

        $expectedRequest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                         . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                         .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                         .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                         .               'xmlns:ns1="http://www.example.com/namespace" '
                         .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                         .     '<env:Header>'
                         .         '<ns1:MyHeader2>SOAP header content 2</ns1:MyHeader2>'
                         .         '<ns1:MyHeader1>SOAP header content 1</ns1:MyHeader1>'
                         .     '</env:Header>'
                         .     '<env:Body>'
                         .         '<env:testFunc2 env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                         .             '<who xsi:type="xsd:string">World</who>'
                         .         '</env:testFunc2>'
                         .     '</env:Body>'
                         . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastRequest(), $expectedRequest);


        // Add request header
        $client->addSoapInputHeader(new SoapHeader('http://www.example.com/namespace', 'MyHeader3', 'SOAP header content 3'));

        // Perform request
        $client->testFunc2('World');

        $expectedRequest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                         . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                         .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                         .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                         .               'xmlns:ns1="http://www.example.com/namespace" '
                         .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                         .     '<env:Header>'
                         .         '<ns1:MyHeader2>SOAP header content 2</ns1:MyHeader2>'
                         .         '<ns1:MyHeader3>SOAP header content 3</ns1:MyHeader3>'
                         .     '</env:Header>'
                         .     '<env:Body>'
                         .         '<env:testFunc2 env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                         .             '<who xsi:type="xsd:string">World</who>'
                         .         '</env:testFunc2>'
                         .     '</env:Body>'
                         . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastRequest(), $expectedRequest);


        $client->resetSoapInputHeaders();

        // Add request header
        $client->addSoapInputHeader(new SoapHeader('http://www.example.com/namespace', 'MyHeader4', 'SOAP header content 4'));

        // Perform request
        $client->testFunc2('World');

        $expectedRequest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                         . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                         .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                         .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                         .               'xmlns:ns1="http://www.example.com/namespace" '
                         .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                         .     '<env:Header>'
                         .         '<ns1:MyHeader4>SOAP header content 4</ns1:MyHeader4>'
                         .     '</env:Header>'
                         .     '<env:Body>'
                         .         '<env:testFunc2 env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                         .             '<who xsi:type="xsd:string">World</who>'
                         .         '</env:testFunc2>'
                         .     '</env:Body>'
                         . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastRequest(), $expectedRequest);
    }

    /**
     * @group ZF-6955
     */
    public function testSetCookieIsDelegatedToSoapClient()
    {
        $fixtureCookieKey = "foo";
        $fixtureCookieValue = "bar";

        $clientMock = $this->getMock('SoapClient', array('__setCookie'), array(null, array('uri' => 'http://www.zend.com', 'location' => 'http://www.zend.com')));
        $clientMock->expects($this->once())
                   ->method('__setCookie')
                   ->with($fixtureCookieKey, $fixtureCookieValue);

        $soap = new Zend_Soap_Client();
        $soap->setSoapClient($clientMock);

        $soap->setCookie($fixtureCookieKey, $fixtureCookieValue);
    }

    public function testSetSoapClient()
    {
        $clientMock = $this->getMock('SoapClient', array('__setCookie'), array(null, array('uri' => 'http://www.zend.com', 'location' => 'http://www.zend.com')));

        $soap = new Zend_Soap_Client();
        $soap->setSoapClient($clientMock);

        $this->assertSame($clientMock, $soap->getSoapClient());
    }
}


/** Test Class */
class Zend_Soap_Client_TestClass {
    /**
     * Test Function 1
     *
     * @return string
     */
    function testFunc1()
    {
        return "Hello World";
    }

    /**
     * Test Function 2
     *
     * @param string $who Some Arg
     * @return string
     */
    function testFunc2($who)
    {
        return "Hello $who!";
    }

    /**
     * Test Function 3
     *
     * @param string $who Some Arg
     * @param int $when Some
     * @return string
     */
    function testFunc3($who, $when)
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test Function 4
     *
     * @return string
     */
    static function testFunc4()
    {
        return "I'm Static!";
    }
}

/** Test class 2 */
class Zend_Soap_Client_TestData1 {
    /**
     * Property1
     *
     * @var string
     */
     public $property1;

    /**
     * Property2
     *
     * @var float
     */
     public $property2;
}

/** Test class 2 */
class Zend_Soap_Client_TestData2 {
    /**
     * Property1
     *
     * @var integer
     */
     public $property1;

    /**
     * Property1
     *
     * @var float
     */
     public $property2;
}


/* Test Functions */

/**
 * Test Function
 *
 * @param string $arg
 * @return string
 */
function Zend_Soap_Client_TestFunc1($who)
{
    return "Hello $who";
}

/**
 * Test Function 2
 */
function Zend_Soap_Client_TestFunc2()
{
    return "Hello World";
}

/**
 * Return false
 *
 * @return bool
 */
function Zend_Soap_Client_TestFunc3()
{
    return false;
}

/**
 * Return true
 *
 * @return bool
 */
function Zend_Soap_Client_TestFunc4()
{
    return true;
}

/**
 * Return integer
 *
 * @return int
 */
function Zend_Soap_Client_TestFunc5()
{
    return 123;
}

/**
 * Return string
 *
 * @return string
 */
function Zend_Soap_Client_TestFunc6()
{
    return "string";
}

if (PHPUnit_MAIN_METHOD == 'Zend_Soap_ClientTest::main') {
    Zend_Soap_ClientTest::main();
}
