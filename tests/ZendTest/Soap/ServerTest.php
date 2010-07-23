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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Soap;

require_once __DIR__ . '/TestAsset/commontypes.php';

use Zend\Soap\Server,
    Zend\Soap\ServerException;

/**
 * Zend_Soap_Server
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @uses       Zend_Server_Interface
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_Server
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }
    }

    public function testSetOptions()
    {
        $server = new Server();

        $this->assertTrue($server->getOptions() == array('soap_version' => SOAP_1_2));

        $options = array('soap_version' => SOAP_1_1,
                         'actor' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                         'classmap' => array('TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                                             'TestData2' => '\ZendTest\Soap\TestAsset\TestData2',),
                         'encoding' => 'ISO-8859-1',
                         'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php'
                        );
        $server->setOptions($options);

        $this->assertTrue($server->getOptions() == $options);
    }

    public function testSetOptionsViaSecondConstructorArgument()
    {
        $options = array(
            'soap_version' => SOAP_1_1,
            'actor' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
            'classmap' => array(
                'TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                'TestData2' => '\ZendTest\Soap\TestAsset\TestData2',
            ),
            'encoding' => 'ISO-8859-1',
            'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
        );
        $server = new Server(null, $options);

        $this->assertTrue($server->getOptions() == $options);
    }

    /**
     * @group ZF-9816
     */
    public function testSetOptionsWithFeaturesOption()
    {
        $server = new Server(null, array(
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS
        ));

        $this->assertEquals(
            SOAP_SINGLE_ELEMENT_ARRAYS,
            $server->getSoapFeatures()
        );
    }

    public function testSetWsdlViaOptionsArrayIsPossible()
    {
        $server = new Server();
        $server->setOptions(array('wsdl' => 'http://www.example.com/test.wsdl'));

        $this->assertEquals('http://www.example.com/test.wsdl', $server->getWSDL());
    }

    public function testGetOptions()
    {
        $server = new Server();

        $this->assertTrue($server->getOptions() == array('soap_version' => SOAP_1_2));

        $options = array('soap_version' => SOAP_1_1,
                         'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php'
                        );
        $server->setOptions($options);

        $this->assertTrue($server->getOptions() == $options);
    }

    public function testEncoding()
    {
        $server = new Server();

        $this->assertNull($server->getEncoding());
        $server->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $server->getEncoding());

        try {
            $server->setEncoding(array('UTF-8'));
            $this->fail('Non-string encoding values should fail');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testSoapVersion()
    {
        $server = new Server();

        $this->assertEquals(SOAP_1_2, $server->getSoapVersion());
        $server->setSoapVersion(SOAP_1_1);
        $this->assertEquals(SOAP_1_1, $server->getSoapVersion());
        try {
            $server->setSoapVersion('bogus');
            $this->fail('Invalid soap versions should fail');
        } catch (\Exception $e)  {
            // success
        }
    }

    public function testValidateUrn()
    {
        $server = new Server();

        try {
            $server->validateUrn('bogosity');
            $this->fail('URNs without schemes should fail');
        } catch (\Exception $e) {
            // success
        }

        $this->assertTrue($server->validateUrn('http://framework.zend.com/'));
        $this->assertTrue($server->validateUrn('urn:soapHandler/GetOpt'));
    }

    public function testSetActor()
    {
        $server = new Server();

        $this->assertNull($server->getActor());
        $server->setActor('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getActor());
        try {
            $server->setActor('bogus');
            $this->fail('Invalid actor should fail');
        } catch (\Exception $e)  {
            // success
        }
    }

    public function testGetActor()
    {
        $server = new Server();

        $this->assertNull($server->getActor());
        $server->setActor('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getActor());
    }

    public function testSetUri()
    {
        $server = new Server();

        $this->assertNull($server->getUri());
        $server->setUri('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getUri());
        try {
            $server->setUri('bogus');
            $this->fail('Invalid URI should fail');
        } catch (\Exception $e)  {
            // success
        }
    }

    public function testGetUri()
    {
        $server = new Server();

        $this->assertNull($server->getUri());
        $server->setUri('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getUri());
    }

    public function testSetClassmap()
    {
        $server = new Server();

        $classmap = array('TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                          'TestData2' => '\ZendTest\Soap\TestAsset\TestData2');

        $this->assertNull($server->getClassmap());
        $server->setClassmap($classmap);
        $this->assertTrue($classmap == $server->getClassmap());
        try {
            $server->setClassmap('bogus');
            $this->fail('Classmap which is not an array should fail');
        } catch (\Exception $e)  {
            // success
        }
        try {
            $server->setClassmap(array('soapTypeName', 'bogusClassName'));
            $this->fail('Invalid class within classmap should fail');
        } catch (\Exception $e)  {
            // success
        }
    }

    public function testGetClassmap()
    {
        $server = new Server();

        $classmap = array('TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                          'TestData2' => '\ZendTest\Soap\TestAsset\TestData2');

        $this->assertNull($server->getClassmap());
        $server->setClassmap($classmap);
        $this->assertTrue($classmap == $server->getClassmap());
    }

    public function testSetWSDL()
    {
        $server = new Server();

        $this->assertNull($server->getWSDL());
        $server->setWSDL(__DIR__.'/_files/wsdl_example.wsdl');
        $this->assertEquals(__DIR__.'/_files/wsdl_example.wsdl', $server->getWSDL());
        try {
            $server->setWSDL(__DIR__.'/_files/bogus.wsdl');
            $this->fail('Invalid WSDL URI or PATH should fail');
        } catch (\Exception $e)  {
            // success
        }
    }

    public function testGetWSDL()
    {
        $server = new Server();

        $this->assertNull($server->getWSDL());
        $server->setWSDL(__DIR__.'/_files/wsdl_example.wsdl');
        $this->assertEquals(__DIR__.'/_files/wsdl_example.wsdl', $server->getWSDL());
    }

    public function testAddFunction()
    {
        $server = new Server();

        // Correct function should pass
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        // Array of correct functions should pass
        $functions = array('\ZendTest\Soap\TestAsset\TestFunc2',
                           '\ZendTest\Soap\TestAsset\TestFunc3',
                           '\ZendTest\Soap\TestAsset\TestFunc4');
        $server->addFunction($functions);

        $this->assertEquals(
            array_merge(array('\ZendTest\Soap\TestAsset\TestFunc'), $functions),
            $server->getFunctions()
        );
    }

    public function testAddBogusFunctionAsInteger()
    {
        $server = new Server();
        try {
            $server->addFunction(126);
            $this->fail('Invalid value should fail');
        } catch (ServerException $e)  {
            // success
        }
    }

    public function testAddBogusFunctionsAsString()
    {
        $server = new Server();

        try {
            $server->addFunction('bogus_function');
            $this->fail('Invalid function should fail.');
        } catch (ServerException $e)  {
            // success
        }
    }

    public function testAddBogusFunctionsAsArray()
    {
        $server = new Server();

        try {
            $functions = array('\ZendTest\Soap\TestAsset\TestFunc5',
                                'bogus_function',
                                '\ZendTest\Soap\TestAsset\TestFunc6');
            $server->addFunction($functions);
            $this->fail('Invalid function within a set of functions should fail');
        } catch (ServerException $e)  {
            // success
        }
    }

    public function testAddAllFunctionsSoapConstant()
    {
        $server = new Server();

        // SOAP_FUNCTIONS_ALL as a value should pass
        $server->addFunction(SOAP_FUNCTIONS_ALL);
        $server->addFunction('substr');
        $this->assertEquals(array(SOAP_FUNCTIONS_ALL), $server->getFunctions());
    }

    public function testSetClass()
    {
        $server = new Server();

        // Correct class name should pass
        try {
            $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
        } catch(\Exception $e) {
            $this->fail("Setting a correct class name should not fail setClass()");
        }
    }

    public function testSetClassTwiceThrowsException()
    {
        $server = new Server();

        // Correct class name should pass
        try {
            $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
            $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
            $this->fail();
        } catch(ServerException $e) {
            $this->assertEquals('A class has already been registered with this soap server instance', $e->getMessage());
        }
    }

    public function testSetClassWithArguments()
    {
        $server = new Server();

        // Correct class name should pass
        try {
            $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass', 1, 2, 3, 4);
        } catch(\Exception $e) {
            $this->fail("Setting a correct class name should not fail setClass()");
        }
    }

    public function testSetBogusClassWithIntegerName()
    {
        $server = new Server();

        try {
            $server->setClass(465);
            $this->fail('Non-string value should fail');
        } catch (\Exception $e)  {
            // success
        }
    }

    public function testSetBogusClassWithUnknownClassName()
    {
        $server = new Server();

        try {
            $server->setClass('Zend_Soap_Server_Test_BogusClass');
            $this->fail('Invalid class should fail');
        } catch (\Exception $e)  {
            // success
        }
    }

    /**
     * @group ZF-4366
     */
    public function testSetObject()
    {
        $server = new Server();

        try {
            $server->setObject(465);
            $this->fail('Non-object value should fail');
        } catch (\Exception $e)  {
            // success
        }

        try {
            $int = 1;
            $server->setObject($int);
            $this->fail('Invalid argument should fail');
        } catch (\Exception $e)  {
            // success
        }

        // Correct class name should pass
        $server->setObject(new TestAsset\ServerTestClass());

        try {
            $server->setObject(new TestAsset\ServerTestClass());
            $this->fail('setClass() should pass only once');
        } catch (\Exception $e)  {
            // success
        }
    }

    public function testGetFunctions()
    {
        $server = new Server();

        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        $functions  =  array('\ZendTest\Soap\TestAsset\TestFunc2',
                             '\ZendTest\Soap\TestAsset\TestFunc3',
                             '\ZendTest\Soap\TestAsset\TestFunc4');
        $server->addFunction($functions);

        $functions  =  array('\ZendTest\Soap\TestAsset\TestFunc3',
                             '\ZendTest\Soap\TestAsset\TestFunc5',
                             '\ZendTest\Soap\TestAsset\TestFunc6');
        $server->addFunction($functions);

        $allAddedFunctions = array(
            '\ZendTest\Soap\TestAsset\TestFunc',
            '\ZendTest\Soap\TestAsset\TestFunc2',
            '\ZendTest\Soap\TestAsset\TestFunc3',
            '\ZendTest\Soap\TestAsset\TestFunc4',
            '\ZendTest\Soap\TestAsset\TestFunc5',
            '\ZendTest\Soap\TestAsset\TestFunc6'
        );
        $this->assertTrue($server->getFunctions() == $allAddedFunctions);
    }

    public function testGetFunctionsWithClassAttached()
    {
        $server = new Server();
        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $this->assertEquals(
            array('testFunc1', 'testFunc2', 'testFunc3', 'testFunc4', 'testFunc5'),
            $server->getFunctions()
        );
    }

    public function testGetFunctionsWithObjectAttached()
    {
        $server = new Server();
        $server->setObject(new TestAsset\ServerTestClass());

        $this->assertEquals(
            array('testFunc1', 'testFunc2', 'testFunc3', 'testFunc4', 'testFunc5'),
            $server->getFunctions()
        );
    }

    public function testSetPersistence()
    {
        $server = new Server();

        $this->assertNull($server->getPersistence());
        $server->setPersistence(SOAP_PERSISTENCE_SESSION);
        $this->assertEquals(SOAP_PERSISTENCE_SESSION, $server->getPersistence());
        try {
            $server->setSoapVersion('bogus');
            $this->fail('Invalid soap versions should fail');
        } catch (\Exception $e)  {
            // success
        }

        $server->setPersistence(SOAP_PERSISTENCE_REQUEST);
        $this->assertEquals(SOAP_PERSISTENCE_REQUEST, $server->getPersistence());
    }

    public function testSetUnknownPersistenceStateThrowsException()
    {
        $server = new Server();

        try {
            $server->setPersistence('bogus');
            $this->fail();
        } catch(ServerException $e) {

        }
    }

    public function testGetPersistence()
    {
        $server = new Server();

        $this->assertNull($server->getPersistence());
        $server->setPersistence(SOAP_PERSISTENCE_SESSION);
        $this->assertEquals(SOAP_PERSISTENCE_SESSION, $server->getPersistence());
    }

    public function testGetLastRequest()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testGetLastRequest() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $response = $server->handle($request);

        $this->assertEquals($request, $server->getLastRequest());
    }

    public function testSetReturnResponse()
    {
        $server = new Server();

        $this->assertFalse($server->getReturnResponse());

        $server->setReturnResponse(true);
        $this->assertTrue($server->getReturnResponse());

        $server->setReturnResponse(false);
        $this->assertFalse($server->getReturnResponse());
    }

    public function testGetReturnResponse()
    {
        $server = new Server();

        $this->assertFalse($server->getReturnResponse());

        $server->setReturnResponse(true);
        $this->assertTrue($server->getReturnResponse());
    }

    public function testGetLastResponse()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testGetLastResponse() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $expectedResponse =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2Response>'
          .             '<return xsi:type="xsd:string">Hello World!</return>'
          .         '</ns1:testFunc2Response>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $server->handle($request);

        $this->assertEquals($expectedResponse, $server->getLastResponse());
    }

    public function testHandle()
    {
        if (!extension_loaded('soap')) {
            $this->markTestSkipped('Soap extension not loaded');
        }

        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testHandle() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $localClient = new TestAsset\TestLocalSoapClient($server,
                                                         null,
                                                         array('location'=>'test://',
                                                               'uri'=>'http://framework.zend.com'));

        // Local SOAP client call automatically invokes handle method of the provided SOAP server
        $this->assertEquals('Hello World!', $localClient->testFunc2('World'));


        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $expectedResponse =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2Response>'
          .             '<return xsi:type="xsd:string">Hello World!</return>'
          .         '</ns1:testFunc2Response>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $server1 = new Server();
        $server1->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));

        $server1->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
        $server1->setReturnResponse(true);

        $this->assertEquals($expectedResponse, $server1->handle($request));
    }

    /**
     * @todo Implement testRegisterFaultException().
     */
    public function testRegisterFaultException()
    {
        $server = new Server();

        $server->registerFaultException("Zend_Soap_Server_Exception");
        $server->registerFaultException(array("OutOfBoundsException", "BogusException"));

        $this->assertEquals(array(
            'Zend_Soap_Server_Exception',
            'OutOfBoundsException',
            'BogusException',
        ), $server->getFaultExceptions());
    }

    /**
     * @todo Implement testDeregisterFaultException().
     */
    public function testDeregisterFaultException()
    {
        $server = new Server();

        $server->registerFaultException(array("OutOfBoundsException", "BogusException"));
        $ret = $server->deregisterFaultException("BogusException");
        $this->assertTrue($ret);

        $this->assertEquals(array(
            'OutOfBoundsException',
        ), $server->getFaultExceptions());

        $ret = $server->deregisterFaultException("NonRegisteredException");
        $this->assertFalse($ret);
    }

    /**
     * @todo Implement testGetFaultExceptions().
     */
    public function testGetFaultExceptions()
    {
        $server = new Server();

        $this->assertEquals(array(), $server->getFaultExceptions());
        $server->registerFaultException("Exception");
        $this->assertEquals(array("Exception"), $server->getFaultExceptions());
    }

    public function testFaultWithTextMessage()
    {
        $server = new Server();
        $fault = $server->fault("Faultmessage!");

        $this->assertTrue($fault instanceof \SOAPFault);
        $this->assertContains("Faultmessage!", $fault->getMessage());
    }

    public function testFaultWithUnregisteredException()
    {
        $server = new Server();
        $fault = $server->fault(new \Exception("MyException"));

        $this->assertTrue($fault instanceof \SOAPFault);
        $this->assertContains("Unknown error", $fault->getMessage());
        $this->assertNotContains("MyException", $fault->getMessage());
    }

    public function testFaultWithRegisteredException()
    {
        $server = new Server();
        $server->registerFaultException("Exception");
        $fault = $server->fault(new \Exception("MyException"));

        $this->assertTrue($fault instanceof \SOAPFault);
        $this->assertNotContains("Unknown error", $fault->getMessage());
        $this->assertContains("MyException", $fault->getMessage());
    }

    public function testFautlWithBogusInput()
    {
        $server = new Server();
        $fault = $server->fault(array("Here", "There", "Bogus"));

        $this->assertContains("Unknown error", $fault->getMessage());
    }

    /**
     * @group ZF-3958
     */
    public function testFaultWithIntegerFailureCodeDoesNotBreakClassSoapFault()
    {
        $server = new Server();
        $fault = $server->fault("Faultmessage!", 5000);

        $this->assertTrue($fault instanceof \SOAPFault);
    }

    /**
     * @todo Implement testHandlePhpErrors().
     */
    public function testHandlePhpErrors()
    {
        $server = new Server();

        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    public function testLoadFunctionsIsNotImplemented()
    {
        $server = new Server();

        try {
            $server->loadFunctions("bogus");
            $this->fail();
        } catch(ServerException $e) {

        }
    }

    public function testErrorHandlingOfSoapServerChangesToThrowingSoapFaultWhenInHandleMode()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run ' . __METHOD__ . '() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        // Requesting Method with enforced parameter without it.
        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc5 />'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
        $response = $server->handle($request);

        $this->assertContains(
            '<SOAP-ENV:Fault><faultcode>Receiver</faultcode><faultstring>Test Message</faultstring></SOAP-ENV:Fault>',
            $response
        );
    }

    /**
     * @group ZF-5597
     */
    public function testServerAcceptsZendConfigObject()
    {
        $options = array('soap_version' => SOAP_1_1,
                         'actor' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                         'classmap' => array('TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                                             'TestData2' => '\ZendTest\Soap\TestAsset\TestData2',),
                         'encoding' => 'ISO-8859-1',
                         'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php'
                        );
        $config = new \Zend\Config\Config($options);

        $server = new Server();
        $server->setOptions($config);
        $this->assertEquals($options, $server->getOptions());
    }

    /**
     * @group ZF-5300
     */
    public function testSetAndGetFeatures()
    {
        $server = new Server();
        $this->assertNull($server->getSoapFeatures());
        $server->setSoapFeatures(100);
        $this->assertEquals(100, $server->getSoapFeatures());
        $options = $server->getOptions();
        $this->assertTrue(isset($options['features']));
        $this->assertEquals(100, $options['features']);
    }

    /**
     * @group ZF-5300
     */
    public function testSetAndGetWSDLCache()
    {
        $server = new Server();
        $this->assertNull($server->getWSDLCache());
        $server->setWSDLCache(100);
        $this->assertEquals(100, $server->getWSDLCache());
        $options = $server->getOptions();
        $this->assertTrue(isset($options['cache_wsdl']));
        $this->assertEquals(100, $options['cache_wsdl']);
    }
}
