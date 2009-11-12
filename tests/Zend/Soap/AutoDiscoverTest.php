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

require_once dirname(__FILE__)."/../../TestHelper.php";

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Soap_AutoDiscover */
require_once 'Zend/Soap/AutoDiscover.php';

/** Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex */
require_once "Zend/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php";

/** Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence */
require_once "Zend/Soap/Wsdl/Strategy/ArrayOfTypeSequence.php";

/** Include Common TestTypes */
require_once "_files/commontypes.php";

/**
 * Test cases for Zend_Soap_AutoDiscover
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 */
class Zend_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // This has to be done because some CLI setups don't have $_SERVER variables
        // to simuulate that we have an actual webserver.
        if(!isset($_SERVER) || !is_array($_SERVER)) {
            $_SERVER = array();
        }
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/my_script.php?wsdl';
        $_SERVER['SCRIPT_NAME'] = '/my_script.php';
        $_SERVER['HTTPS'] = "off";
    }

    protected function sanitizeWsdlXmlOutputForOsCompability($xmlstring)
    {
        $xmlstring = str_replace(array("\r", "\n"), "", $xmlstring);
        $xmlstring = preg_replace('/(>[\s]{1,}<)/', '', $xmlstring);
        return $xmlstring;
    }

    function testSetClass()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->setClass('Zend_Soap_AutoDiscover_Test');
        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_clean());

        $wsdl = '<?xml version="1.0"?>'
              . '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
              .              'xmlns:tns="' . $scriptUri . '" '
              .              'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
              .              'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
              .              'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
              .              'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
              .              'name="Zend_Soap_AutoDiscover_Test" '
              .              'targetNamespace="' . $scriptUri . '">'
              .     '<types>'
              .         '<xsd:schema targetNamespace="' . $scriptUri . '"/>'
              .     '</types>'
              .     '<portType name="Zend_Soap_AutoDiscover_TestPort">'
              .         '<operation name="testFunc1">'
              .             '<documentation>Test Function 1</documentation>'
              .             '<input message="tns:testFunc1In"/>'
              .             '<output message="tns:testFunc1Out"/>'
              .         '</operation>'
              .         '<operation name="testFunc2">'
              .             '<documentation>Test Function 2</documentation>'
              .             '<input message="tns:testFunc2In"/>'
              .             '<output message="tns:testFunc2Out"/>'
              .         '</operation>'
              .         '<operation name="testFunc3">'
              .             '<documentation>Test Function 3</documentation>'
              .             '<input message="tns:testFunc3In"/>'
              .             '<output message="tns:testFunc3Out"/>'
              .         '</operation><operation name="testFunc4">'
              .             '<documentation>Test Function 4</documentation>'
              .             '<input message="tns:testFunc4In"/>'
              .             '<output message="tns:testFunc4Out"/>'
              .         '</operation>'
              .     '</portType>'
              .     '<binding name="Zend_Soap_AutoDiscover_TestBinding" type="tns:Zend_Soap_AutoDiscover_TestPort">'
              .         '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'
              .         '<operation name="testFunc1">'
              .             '<soap:operation soapAction="' . $scriptUri . '#testFunc1"/>'
              .             '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'
              .             '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc2">'
              .             '<soap:operation soapAction="' . $scriptUri . '#testFunc2"/>'
              .             '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'
              .             '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc3">'
              .             '<soap:operation soapAction="' . $scriptUri . '#testFunc3"/>'
              .             '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'
              .             '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc4">'
              .             '<soap:operation soapAction="' . $scriptUri . '#testFunc4"/>'
              .             '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'
              .             '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'
              .         '</operation>'
              .     '</binding>'
              .     '<service name="Zend_Soap_AutoDiscover_TestService">'
              .         '<port name="Zend_Soap_AutoDiscover_TestPort" binding="tns:Zend_Soap_AutoDiscover_TestBinding">'
              .             '<soap:address location="' . $scriptUri . '"/>'
              .         '</port>'
              .     '</service>'
              .     '<message name="testFunc1In"/>'
              .     '<message name="testFunc1Out"><part name="return" type="xsd:string"/></message>'
              .     '<message name="testFunc2In"><part name="who" type="xsd:string"/></message>'
              .     '<message name="testFunc2Out"><part name="return" type="xsd:string"/></message>'
              .     '<message name="testFunc3In"><part name="who" type="xsd:string"/><part name="when" type="xsd:int"/></message>'
              .     '<message name="testFunc3Out"><part name="return" type="xsd:string"/></message>'
              .     '<message name="testFunc4In"/>'
              .     '<message name="testFunc4Out"><part name="return" type="xsd:string"/></message>'
              . '</definitions>';

        $dom->save(dirname(__FILE__).'/_files/setclass.wsdl');
        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");

        unlink(dirname(__FILE__).'/_files/setclass.wsdl');
    }

    function testSetClassWithDifferentStyles()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->setBindingStyle(array('style' => 'document', 'transport' => 'http://framework.zend.com'));
        $server->setOperationBodyStyle(array('use' => 'literal', 'namespace' => 'http://framework.zend.com'));
        $server->setClass('Zend_Soap_AutoDiscover_Test');
        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_clean());

        $wsdl = '<?xml version="1.0"?>'
              . '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
              .              'xmlns:tns="' . $scriptUri . '" '
              .              'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
              .              'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
              .              'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
              .              'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
              .              'name="Zend_Soap_AutoDiscover_Test" '
              .              'targetNamespace="' . $scriptUri . '">'
              .     '<types>'
              .         '<xsd:schema targetNamespace="' . $scriptUri . '">'
              .           '<xsd:element name="testFunc1">'
              .             '<xsd:complexType/>'
              .           '</xsd:element>'
              .           '<xsd:element name="testFunc1Response">'
              .             '<xsd:complexType>'
              .               '<xsd:sequence>'
              .                 '<xsd:element name="testFunc1Result" type="xsd:string"/>'
              .               '</xsd:sequence>'
              .             '</xsd:complexType>'
              .           '</xsd:element>'
              .           '<xsd:element name="testFunc2">'
              .             '<xsd:complexType>'
              .               '<xsd:sequence>'
              .                 '<xsd:element name="who" type="xsd:string"/>'
              .               '</xsd:sequence>'
              .             '</xsd:complexType>'
              .           '</xsd:element>'
              .           '<xsd:element name="testFunc2Response">'
              .             '<xsd:complexType>'
              .               '<xsd:sequence>'
              .                 '<xsd:element name="testFunc2Result" type="xsd:string"/>'
              .               '</xsd:sequence>'
              .             '</xsd:complexType>'
              .           '</xsd:element>'
              .           '<xsd:element name="testFunc3">'
              .             '<xsd:complexType>'
              .               '<xsd:sequence>'
              .                 '<xsd:element name="who" type="xsd:string"/>'
              .                 '<xsd:element name="when" type="xsd:int"/>'
              .               '</xsd:sequence>'
              .             '</xsd:complexType>'
              .           '</xsd:element>'
              .           '<xsd:element name="testFunc3Response">'
              .             '<xsd:complexType>'
              .               '<xsd:sequence>'
              .                 '<xsd:element name="testFunc3Result" type="xsd:string"/>'
              .               '</xsd:sequence>'
              .             '</xsd:complexType>'
              .           '</xsd:element>'
              .           '<xsd:element name="testFunc4">'
              .             '<xsd:complexType/>'
              .           '</xsd:element>'
              .           '<xsd:element name="testFunc4Response">'
              .             '<xsd:complexType>'
              .               '<xsd:sequence>'
              .                 '<xsd:element name="testFunc4Result" type="xsd:string"/>'
              .               '</xsd:sequence>'
              .             '</xsd:complexType>'
              .           '</xsd:element>'
              .         '</xsd:schema>'
              .     '</types>'
              .     '<portType name="Zend_Soap_AutoDiscover_TestPort">'
              .         '<operation name="testFunc1">'
              .             '<documentation>Test Function 1</documentation>'
              .             '<input message="tns:testFunc1In"/>'
              .             '<output message="tns:testFunc1Out"/>'
              .         '</operation>'
              .         '<operation name="testFunc2">'
              .             '<documentation>Test Function 2</documentation>'
              .             '<input message="tns:testFunc2In"/>'
              .             '<output message="tns:testFunc2Out"/>'
              .         '</operation>'
              .         '<operation name="testFunc3">'
              .             '<documentation>Test Function 3</documentation>'
              .             '<input message="tns:testFunc3In"/>'
              .             '<output message="tns:testFunc3Out"/>'
              .         '</operation><operation name="testFunc4">'
              .             '<documentation>Test Function 4</documentation>'
              .             '<input message="tns:testFunc4In"/>'
              .             '<output message="tns:testFunc4Out"/>'
              .         '</operation>'
              .     '</portType>'
              .     '<binding name="Zend_Soap_AutoDiscover_TestBinding" type="tns:Zend_Soap_AutoDiscover_TestPort">'
              .         '<soap:binding style="document" transport="http://framework.zend.com"/>'
              .         '<operation name="testFunc1">'
              .             '<soap:operation soapAction="' . $scriptUri . '#testFunc1"/>'
              .             '<input><soap:body use="literal" namespace="http://framework.zend.com"/></input>'
              .             '<output><soap:body use="literal" namespace="http://framework.zend.com"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc2">'
              .             '<soap:operation soapAction="' . $scriptUri . '#testFunc2"/>'
              .             '<input><soap:body use="literal" namespace="http://framework.zend.com"/></input>'
              .             '<output><soap:body use="literal" namespace="http://framework.zend.com"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc3">'
              .             '<soap:operation soapAction="' . $scriptUri . '#testFunc3"/>'
              .             '<input><soap:body use="literal" namespace="http://framework.zend.com"/></input>'
              .             '<output><soap:body use="literal" namespace="http://framework.zend.com"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc4">'
              .             '<soap:operation soapAction="' . $scriptUri . '#testFunc4"/>'
              .             '<input><soap:body use="literal" namespace="http://framework.zend.com"/></input>'
              .             '<output><soap:body use="literal" namespace="http://framework.zend.com"/></output>'
              .         '</operation>'
              .     '</binding>'
              .     '<service name="Zend_Soap_AutoDiscover_TestService">'
              .         '<port name="Zend_Soap_AutoDiscover_TestPort" binding="tns:Zend_Soap_AutoDiscover_TestBinding">'
              .             '<soap:address location="' . $scriptUri . '"/>'
              .         '</port>'
              .     '</service>'
              .     '<message name="testFunc1In">'
              .       '<part name="parameters" element="tns:testFunc1"/>'
              .     '</message>'
              .     '<message name="testFunc1Out">'
              .       '<part name="parameters" element="tns:testFunc1Response"/>'
              .     '</message>'
              .     '<message name="testFunc2In">'
              .       '<part name="parameters" element="tns:testFunc2"/>'
              .     '</message>'
              .     '<message name="testFunc2Out">'
              .       '<part name="parameters" element="tns:testFunc2Response"/>'
              .     '</message>'
              .     '<message name="testFunc3In">'
              .       '<part name="parameters" element="tns:testFunc3"/>'
              .     '</message>'
              .     '<message name="testFunc3Out">'
              .       '<part name="parameters" element="tns:testFunc3Response"/>'
              .     '</message>'
              .     '<message name="testFunc4In">'
              .       '<part name="parameters" element="tns:testFunc4"/>'
              .     '</message>'
              .     '<message name="testFunc4Out">'
              .       '<part name="parameters" element="tns:testFunc4Response"/>'
              .     '</message>'
              . '</definitions>';

        $dom->save(dirname(__FILE__).'/_files/setclass.wsdl');
        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");

        unlink(dirname(__FILE__).'/_files/setclass.wsdl');
    }

    /**
     * @group ZF-5072
     */
    function testSetClassWithResponseReturnPartCompabilityMode()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->setClass('Zend_Soap_AutoDiscover_Test');
        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_clean());

        $dom->save(dirname(__FILE__).'/_files/setclass.wsdl');
        $this->assertContains('<message name="testFunc1Out"><part name="return"', $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertContains('<message name="testFunc2Out"><part name="return"', $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertContains('<message name="testFunc3Out"><part name="return"', $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertContains('<message name="testFunc4Out"><part name="return"', $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));

        unlink(dirname(__FILE__).'/_files/setclass.wsdl');
    }


    function testAddFunctionSimple()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_clean());
        $dom->save(dirname(__FILE__).'/_files/addfunction.wsdl');

        $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
        $name = $parts[0];

        $wsdl = '<?xml version="1.0"?>'.
                '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="' . $scriptUri . '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" name="' .$name. '" targetNamespace="' . $scriptUri . '">'.
                '<types><xsd:schema targetNamespace="' . $scriptUri . '"/></types>'.
                '<portType name="' .$name. 'Port">'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc"><documentation>Test Function</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFuncIn"/><output message="tns:Zend_Soap_AutoDiscover_TestFuncOut"/></operation>'.
                '</portType>'.
                '<binding name="' .$name. 'Binding" type="tns:' .$name. 'Port">'.
                '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="http://localhost/my_script.php"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="http://localhost/my_script.php"/></output>'.
                '</operation>'.
                '</binding>'.
                '<service name="' .$name. 'Service">'.
                '<port name="' .$name. 'Port" binding="tns:' .$name. 'Binding">'.
                '<soap:address location="' . $scriptUri . '"/>'.
                '</port>'.
                '</service>'.
                '<message name="Zend_Soap_AutoDiscover_TestFuncIn"><part name="who" type="xsd:string"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFuncOut"><part name="return" type="xsd:string"/></message>'.
                '</definitions>';
        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()), "Bad WSDL generated");
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");

        unlink(dirname(__FILE__).'/_files/addfunction.wsdl');
    }

    function testAddFunctionSimpleWithDifferentStyle()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->setBindingStyle(array('style' => 'document', 'transport' => 'http://framework.zend.com'));
        $server->setOperationBodyStyle(array('use' => 'literal', 'namespace' => 'http://framework.zend.com'));
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_clean());
        $dom->save(dirname(__FILE__).'/_files/addfunction.wsdl');

        $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
        $name = $parts[0];

        $wsdl = '<?xml version="1.0"?>'.
                '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="' . $scriptUri . '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" name="' .$name. '" targetNamespace="' . $scriptUri . '">'.
                '<types>'.
                '<xsd:schema targetNamespace="' . $scriptUri . '">'.
                '<xsd:element name="Zend_Soap_AutoDiscover_TestFunc"><xsd:complexType><xsd:sequence><xsd:element name="who" type="xsd:string"/></xsd:sequence></xsd:complexType></xsd:element>'.
                '<xsd:element name="Zend_Soap_AutoDiscover_TestFuncResponse"><xsd:complexType><xsd:sequence><xsd:element name="Zend_Soap_AutoDiscover_TestFuncResult" type="xsd:string"/></xsd:sequence></xsd:complexType></xsd:element>'.
                '</xsd:schema>'.
                '</types>'.
                '<portType name="' .$name. 'Port">'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc"><documentation>Test Function</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFuncIn"/><output message="tns:Zend_Soap_AutoDiscover_TestFuncOut"/></operation>'.
                '</portType>'.
                '<binding name="' .$name. 'Binding" type="tns:' .$name. 'Port">'.
                '<soap:binding style="document" transport="http://framework.zend.com"/>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc"/>'.
                '<input><soap:body use="literal" namespace="http://framework.zend.com"/></input>'.
                '<output><soap:body use="literal" namespace="http://framework.zend.com"/></output>'.
                '</operation>'.
                '</binding>'.
                '<service name="' .$name. 'Service">'.
                '<port name="' .$name. 'Port" binding="tns:' .$name. 'Binding">'.
                '<soap:address location="' . $scriptUri . '"/>'.
                '</port>'.
                '</service>'.
                '<message name="Zend_Soap_AutoDiscover_TestFuncIn"><part name="parameters" element="tns:Zend_Soap_AutoDiscover_TestFunc"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFuncOut"><part name="parameters" element="tns:Zend_Soap_AutoDiscover_TestFuncResponse"/></message>'.
                '</definitions>';
        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()), "Bad WSDL generated");
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");

        unlink(dirname(__FILE__).'/_files/addfunction.wsdl');
    }

    /**
     * @group ZF-5072
     */
    function testAddFunctionSimpleInReturnNameCompabilityMode()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_clean());
        $dom->save(dirname(__FILE__).'/_files/addfunction.wsdl');

        $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
        $name = $parts[0];

        $wsdl = $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML());
        $this->assertContains('<message name="Zend_Soap_AutoDiscover_TestFuncOut"><part name="return" type="xsd:string"/>', $wsdl);
        $this->assertNotContains('<message name="Zend_Soap_AutoDiscover_TestFuncOut"><part name="Zend_Soap_AutoDiscover_TestFuncReturn"', $wsdl);
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");

        unlink(dirname(__FILE__).'/_files/addfunction.wsdl');
    }

    function testAddFunctionMultiple()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc2');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc3');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc4');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc5');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc6');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc7');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc9');

        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_clean());
        $dom->save(dirname(__FILE__).'/_files/addfunction2.wsdl');

        $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
        $name = $parts[0];

        $wsdl = '<?xml version="1.0"?>'.
                '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="' . $scriptUri . '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" name="' .$name. '" targetNamespace="' . $scriptUri . '">'.
                '<types><xsd:schema targetNamespace="' . $scriptUri . '"/></types>'.
                '<portType name="' .$name. 'Port">'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc"><documentation>Test Function</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFuncIn"/><output message="tns:Zend_Soap_AutoDiscover_TestFuncOut"/></operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc2"><documentation>Test Function 2</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFunc2In"/></operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc3"><documentation>Return false</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFunc3In"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc3Out"/></operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc4"><documentation>Return true</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFunc4In"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc4Out"/></operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc5"><documentation>Return integer</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFunc5In"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc5Out"/></operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc6"><documentation>Return string</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFunc6In"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc6Out"/></operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc7"><documentation>Return array</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFunc7In"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc7Out"/></operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc9"><documentation>Multiple Args</documentation><input message="tns:Zend_Soap_AutoDiscover_TestFunc9In"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc9Out"/></operation>'.
                '</portType>'.
                '<binding name="' .$name. 'Binding" type="tns:' .$name. 'Port">'.
                '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc2">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc2"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc3">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc3"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc4">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc4"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc5">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc5"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc6">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc6"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc7">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc7"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="Zend_Soap_AutoDiscover_TestFunc9">'.
                '<soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc9"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '</binding>'.
                '<service name="' .$name. 'Service">'.
                '<port name="' .$name. 'Port" binding="tns:' .$name. 'Binding">'.
                '<soap:address location="' . $scriptUri . '"/>'.
                '</port>'.
                '</service>'.
                '<message name="Zend_Soap_AutoDiscover_TestFuncIn"><part name="who" type="xsd:string"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFuncOut"><part name="return" type="xsd:string"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc2In"/>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc3In"/>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc3Out"><part name="return" type="xsd:boolean"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc4In"/>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc4Out"><part name="return" type="xsd:boolean"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc5In"/>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc5Out"><part name="return" type="xsd:int"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc6In"/>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc6Out"><part name="return" type="xsd:string"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc7In"/>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc7Out"><part name="return" type="soap-enc:Array"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc9In"><part name="foo" type="xsd:string"/><part name="bar" type="xsd:string"/></message>'.
                '<message name="Zend_Soap_AutoDiscover_TestFunc9Out"><part name="return" type="xsd:string"/></message>'.
                '</definitions>';
        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()), "Generated WSDL did not match expected XML");
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");



        unlink(dirname(__FILE__).'/_files/addfunction2.wsdl');
    }

    /**
     * @group ZF-4117
     */
    public function testUseHttpsSchemaIfAccessedThroughHttps()
    {
        $_SERVER['HTTPS'] = "on";
        $httpsScriptUri = 'https://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');

        ob_start();
        $server->handle();
        $wsdlOutput = ob_get_clean();

        $this->assertContains($httpsScriptUri, $wsdlOutput);
    }

    /**
     * @group ZF-4117
     */
    public function testChangeWsdlUriInConstructor()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover(true, "http://example.com/service.php");
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');

        ob_start();
        $server->handle();
        $wsdlOutput = ob_get_clean();

        $this->assertNotContains($scriptUri, $wsdlOutput);
        $this->assertContains("http://example.com/service.php", $wsdlOutput);
    }

    /**
     * @group ZF-4117
     */
    public function testChangeWsdlUriViaSetUri()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover(true);
        $server->setUri("http://example.com/service.php");
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');

        ob_start();
        $server->handle();
        $wsdlOutput = ob_get_clean();

        $this->assertNotContains($scriptUri, $wsdlOutput);
        $this->assertContains("http://example.com/service.php", $wsdlOutput);
    }

    public function testSetNonStringNonZendUriUriThrowsException()
    {
        $server = new Zend_Soap_AutoDiscover();
        try {
            $server->setUri(array("bogus"));
            $this->fail();
        } catch(Zend_Soap_AutoDiscover_Exception $e) {

        }
    }

    /**
     * @group ZF-4117
     */
    public function testChangingWsdlUriAfterGenerationIsPossible()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover(true);
        $server->setUri("http://example.com/service.php");
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');

        ob_start();
        $server->handle();
        $wsdlOutput = ob_get_clean();

        $this->assertNotContains($scriptUri, $wsdlOutput);
        $this->assertContains("http://example.com/service.php", $wsdlOutput);

        $server->setUri("http://example2.com/service2.php");

        ob_start();
        $server->handle();
        $wsdlOutput = ob_get_clean();

        $this->assertNotContains($scriptUri, $wsdlOutput);
        $this->assertNotContains("http://example.com/service.php", $wsdlOutput);
        $this->assertContains("http://example2.com/service2.php", $wsdlOutput);
    }

    /**
     * @group ZF-4688
     * @group ZF-4125
     *
     */
    public function testUsingClassWithMultipleMethodPrototypesProducesValidWsdl()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new Zend_Soap_AutoDiscover();
        $server->setClass('Zend_Soap_AutoDiscover_TestFixingMultiplePrototypes');

        ob_start();
        $server->handle();
        $wsdlOutput = ob_get_clean();

        $this->assertEquals(1, substr_count($wsdlOutput, '<message name="testFuncIn">'));
        $this->assertEquals(1, substr_count($wsdlOutput, '<message name="testFuncOut">'));
    }

    public function testUnusedFunctionsOfAutoDiscoverThrowException()
    {
        $server = new Zend_Soap_AutoDiscover();
        try {
            $server->setPersistence("bogus");
            $this->fail();
        } catch(Zend_Soap_AutoDiscover_Exception $e) {

        }

        try {
            $server->fault();
            $this->fail();
        } catch(Zend_Soap_AutoDiscover_Exception $e) {

        }

        try {
            $server->loadFunctions("bogus");
            $this->fail();
        } catch(Zend_Soap_AutoDiscover_Exception $e) {

        }
    }

    public function testGetFunctions()
    {
        $server = new Zend_Soap_AutoDiscover();
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
        $server->setClass('Zend_Soap_AutoDiscover_Test');

        $functions = $server->getFunctions();
        $this->assertEquals(
            array('Zend_Soap_AutoDiscover_TestFunc', 'testFunc1', 'testFunc2', 'testFunc3', 'testFunc4'),
            $functions
        );
    }

    /**
     * @group ZF-4835
     */
    public function testUsingRequestUriWithoutParametersAsDefault()
    {
        // Apache
        $_SERVER = array('REQUEST_URI' => '/my_script.php?wsdl', 'HTTP_HOST' => 'localhost');
        $server = new Zend_Soap_AutoDiscover();
        $uri = $server->getUri()->getUri();
        $this->assertNotContains("?wsdl", $uri);
        $this->assertEquals("http://localhost/my_script.php", $uri);

        // Apache plus SSL
        $_SERVER = array('REQUEST_URI' => '/my_script.php?wsdl', 'HTTP_HOST' => 'localhost', 'HTTPS' => 'on');
        $server = new Zend_Soap_AutoDiscover();
        $uri = $server->getUri()->getUri();
        $this->assertNotContains("?wsdl", $uri);
        $this->assertEquals("https://localhost/my_script.php", $uri);

        // IIS 5 + PHP as FastCGI
        $_SERVER = array('ORIG_PATH_INFO' => '/my_script.php?wsdl', 'SERVER_NAME' => 'localhost');
        $server = new Zend_Soap_AutoDiscover();
        $uri = $server->getUri()->getUri();
        $this->assertNotContains("?wsdl", $uri);
        $this->assertEquals("http://localhost/my_script.php", $uri);

        // IIS
        $_SERVER = array('HTTP_X_REWRITE_URL' => '/my_script.php?wsdl', 'SERVER_NAME' => 'localhost');
        $server = new Zend_Soap_AutoDiscover();
        $uri = $server->getUri()->getUri();
        $this->assertNotContains("?wsdl", $uri);
        $this->assertEquals("http://localhost/my_script.php", $uri);
    }

    /**
     * @group ZF-4937
     */
    public function testComplexTypesThatAreUsedMultipleTimesAreRecoginzedOnce()
    {
        $server = new Zend_Soap_AutoDiscover('Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex');
        $server->setClass('Zend_Soap_AutoDiscoverTestClass2');

        ob_start();
        $server->handle();
        $wsdlOutput = ob_get_clean();

        $this->assertEquals(1,
            substr_count($wsdlOutput, 'wsdl:arrayType="tns:Zend_Soap_AutoDiscoverTestClass1[]"'),
            'wsdl:arrayType definition of TestClass1 has to occour once.'
        );
        $this->assertEquals(1,
            substr_count($wsdlOutput, '<xsd:complexType name="Zend_Soap_AutoDiscoverTestClass1">'),
            'Zend_Soap_AutoDiscoverTestClass1 has to be defined once.'
        );
        $this->assertEquals(1,
            substr_count($wsdlOutput, '<xsd:complexType name="ArrayOfZend_Soap_AutoDiscoverTestClass1">'),
            'ArrayOfZend_Soap_AutoDiscoverTestClass1 should be defined once.'
        );
        $this->assertTrue(
            substr_count($wsdlOutput, '<part name="test" type="tns:Zend_Soap_AutoDiscoverTestClass1"/>') >= 1,
            'Zend_Soap_AutoDiscoverTestClass1 appears once or more than once in the message parts section.'
        );
    }

    /**
     * @group ZF-5330
     */
    public function testDumpOrXmlOfAutoDiscover()
    {
        $server = new Zend_Soap_AutoDiscover();
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');

        ob_start();
        $server->handle();
        $wsdlOutput = ob_get_clean();

        $this->assertEquals(
            $this->sanitizeWsdlXmlOutputForOsCompability($wsdlOutput),
            $this->sanitizeWsdlXmlOutputForOsCompability($server->toXml())
        );

        ob_start();
        $server->dump(false);
        $wsdlOutput = ob_get_clean();

        $this->assertEquals(
            $this->sanitizeWsdlXmlOutputForOsCompability($wsdlOutput),
            $this->sanitizeWsdlXmlOutputForOsCompability($server->toXml())
        );
    }

    /**
     * @group ZF-5330
     */
    public function testDumpOrXmlOnlyAfterGeneratedAutoDiscoverWsdl()
    {
        $server = new Zend_Soap_AutoDiscover();
        try {
            $server->dump(false);
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_AutoDiscover_Exception);
        }

        try {
            $server->toXml();
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_AutoDiscover_Exception);
        }
    }

    /**
     * @group ZF-5604
     */
    public function testReturnSameArrayOfObjectsResponseOnDifferentMethodsWhenArrayComplex()
    {
        $autodiscover = new Zend_Soap_AutoDiscover('Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex');
        $autodiscover->setClass('Zend_Soap_AutoDiscover_MyService');
        $wsdl = $autodiscover->toXml();

        $this->assertEquals(1, substr_count($wsdl, '<xsd:complexType name="ArrayOfZend_Soap_AutoDiscover_MyResponse">'));

        $this->assertEquals(0, substr_count($wsdl, 'tns:My_Response[]'));
    }

    /**
     * @group ZF-5430
     */
    public function testReturnSameArrayOfObjectsResponseOnDifferentMethodsWhenArraySequence()
    {
        $autodiscover = new Zend_Soap_AutoDiscover('Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence');
        $autodiscover->setClass('Zend_Soap_AutoDiscover_MyServiceSequence');
        $wsdl = $autodiscover->toXml();

        $this->assertEquals(1, substr_count($wsdl, '<xsd:complexType name="ArrayOfString">'));
        $this->assertEquals(1, substr_count($wsdl, '<xsd:complexType name="ArrayOfArrayOfString">'));
        $this->assertEquals(1, substr_count($wsdl, '<xsd:complexType name="ArrayOfArrayOfArrayOfString">'));

        $this->assertEquals(0, substr_count($wsdl, 'tns:string[]'));
    }

    /**
     * @group ZF-5736
     */
    public function testAmpersandInUrlIsCorrectlyEncoded()
    {
        $autodiscover = new Zend_Soap_AutoDiscover();
        $autodiscover->setUri("http://example.com/?a=b&amp;b=c");

        $autodiscover->setClass("Zend_Soap_AutoDiscover_Test");
        $wsdl = $autodiscover->toXml();

        $this->assertContains("http://example.com/?a=b&amp;b=c", $wsdl);
    }

    /**
     * @group ZF-6689
     */
    public function testNoReturnIsOneWayCallInSetClass()
    {
        $autodiscover = new Zend_Soap_AutoDiscover();
        $autodiscover->setClass('Zend_Soap_AutoDiscover_NoReturnType');
        $wsdl = $autodiscover->toXml();

        $this->assertContains(
            '<operation name="pushOneWay"><documentation>@param string $message</documentation><input message="tns:pushOneWayIn"/></operation>',
            $wsdl
        );
    }

    /**
     * @group ZF-6689
     */
    public function testNoReturnIsOneWayCallInAddFunction()
    {
        $autodiscover = new Zend_Soap_AutoDiscover();
        $autodiscover->addFunction('Zend_Soap_AutoDiscover_OneWay');
        $wsdl = $autodiscover->toXml();

        $this->assertContains(
            '<operation name="Zend_Soap_AutoDiscover_OneWay"><documentation>@param string $message</documentation><input message="tns:Zend_Soap_AutoDiscover_OneWayIn"/></operation>',
            $wsdl
        );
    }
}
