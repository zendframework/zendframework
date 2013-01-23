<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace ZendTest\Soap;

/** Include Common TestTypes */
require_once 'TestAsset/commontypes.php';

use Zend\Soap\AutoDiscover;

/** PHPUnit Test Case */

/**
 * Test cases for Zend_Soap_AutoDiscover
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @group      Zend_Soap
 */
class AutoDiscoverTest extends \PHPUnit_Framework_TestCase
{
    protected function createAutodiscoverService()
    {
        $server = new AutoDiscover();
        $server->setUri('http://localhost/my_script.php');
        $server->setServiceName('TestService');
        return $server;
    }

    protected function sanitizeWsdlXmlOutputForOsCompability($xmlstring)
    {
        $xmlstring = str_replace(array("\r", "\n"), "", $xmlstring);
        $xmlstring = preg_replace('/(>[\s]{1,}<)/', '', $xmlstring);
        return $xmlstring;
    }

    /**
     * Assertion to validate DOMDocument is a valid WSDL file.
     *
     * @param \DOMDocument $dom
     */
    protected function assertValidWSDL(\DOMDocument $dom)
    {
        // this code is necessary to support some libxml stupidities.
        $file = __DIR__.'/TestAsset/validate.wsdl';
        if (file_exists($file)) {
            unlink($file);
        }

        $dom->save($file);
        $dom = new \DOMDocument();
        $dom->load($file);

        $this->assertTrue($dom->schemaValidate(__DIR__ .'/schemas/wsdl.xsd'), "WSDL Did not validate");
        unlink($file);
    }

    public function testSetClass()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->setClass('\ZendTest\Soap\TestAsset\Test');
        $dom = $server->generate()->toDomDocument();

        $wsdl = '<?xml version="1.0" encoding="utf-8"?>'
              . '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
              .              'xmlns:tns="' . $scriptUri . '" '
              .              'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
              .              'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
              .              'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
              .              'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
              .              'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
              .              'name="TestService" '
              .              'targetNamespace="' . $scriptUri . '">'
              .     '<types>'
              .         '<xsd:schema targetNamespace="' . $scriptUri . '"/>'
              .     '</types>'
              .     '<portType name="TestServicePort">'
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
              .     '<binding name="TestServiceBinding" type="tns:TestServicePort">'
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
              .     '<service name="TestServiceService">'
              .         '<port name="TestServicePort" binding="tns:TestServiceBinding">'
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

        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertValidWSDL($dom);
    }

    public function testSetClassWithDifferentStyles()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->setBindingStyle(array('style' => 'document', 'transport' => 'http://framework.zend.com'));
        $server->setOperationBodyStyle(array('use' => 'literal', 'namespace' => 'http://framework.zend.com'));
        $server->setClass('\ZendTest\Soap\TestAsset\Test');
        $dom = $server->generate()->toDomDocument();

        $wsdl = '<?xml version="1.0" encoding="utf-8"?>'
              . '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
              .              'xmlns:tns="' . $scriptUri . '" '
              .              'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
              .              'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
              .              'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
              .              'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
              .              'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
              .              'name="TestService" '
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
              .     '<portType name="TestServicePort">'
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
              .     '<binding name="TestServiceBinding" type="tns:TestServicePort">'
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
              .     '<service name="TestServiceService">'
              .         '<port name="TestServicePort" binding="tns:TestServiceBinding">'
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

        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertValidWSDL($dom);
    }

    /**
     * @group ZF-5072
     */
    public function testSetClassWithResponseReturnPartCompabilityMode()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->setClass('\ZendTest\Soap\TestAsset\Test');
        $dom = $server->generate()->toDomDocument();

        $dom->save(__DIR__.'/TestAsset/setclass.wsdl');
        $this->assertContains('<message name="testFunc1Out"><part name="return"', $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertContains('<message name="testFunc2Out"><part name="return"', $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertContains('<message name="testFunc3Out"><part name="return"', $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));
        $this->assertContains('<message name="testFunc4Out"><part name="return"', $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()));

        unlink(__DIR__.'/TestAsset/setclass.wsdl');
    }

    public function testAddFunctionSimple()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        $dom = $server->generate()->toDomDocument();

        $name = "TestService";

        $wsdl = '<?xml version="1.0" encoding="utf-8"?>'.
                '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="' . $scriptUri . '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" name="' .$name. '" targetNamespace="' . $scriptUri . '">'.
                '<types><xsd:schema targetNamespace="' . $scriptUri . '"/></types>'.
                '<portType name="' .$name. 'Port">'.
                '<operation name="TestFunc"><documentation>Test Function</documentation><input message="tns:TestFuncIn"/><output message="tns:TestFuncOut"/></operation>'.
                '</portType>'.
                '<binding name="' .$name. 'Binding" type="tns:' .$name. 'Port">'.
                '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'.
                '<operation name="TestFunc">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="http://localhost/my_script.php"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="http://localhost/my_script.php"/></output>'.
                '</operation>'.
                '</binding>'.
                '<service name="' .$name. 'Service">'.
                '<port name="' .$name. 'Port" binding="tns:' .$name. 'Binding">'.
                '<soap:address location="' . $scriptUri . '"/>'.
                '</port>'.
                '</service>'.
                '<message name="TestFuncIn"><part name="who" type="xsd:string"/></message>'.
                '<message name="TestFuncOut"><part name="return" type="xsd:string"/></message>'.
                '</definitions>';
        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()), "Bad WSDL generated");
        $this->assertValidWSDL($dom);
    }

    public function testAddFunctionSimpleWithDifferentStyle()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->setBindingStyle(array('style' => 'document', 'transport' => 'http://framework.zend.com'));
        $server->setOperationBodyStyle(array('use' => 'literal', 'namespace' => 'http://framework.zend.com'));
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        $dom = $server->generate()->toDomDocument();

        $name = "TestService";
        $wsdl = '<?xml version="1.0" encoding="utf-8"?>'.
                '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="' . $scriptUri . '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" name="' .$name. '" targetNamespace="' . $scriptUri . '">'.
                '<types>'.
                '<xsd:schema targetNamespace="' . $scriptUri . '">'.
                '<xsd:element name="TestFunc"><xsd:complexType><xsd:sequence><xsd:element name="who" type="xsd:string"/></xsd:sequence></xsd:complexType></xsd:element>'.
                '<xsd:element name="TestFuncResponse"><xsd:complexType><xsd:sequence><xsd:element name="TestFuncResult" type="xsd:string"/></xsd:sequence></xsd:complexType></xsd:element>'.
                '</xsd:schema>'.
                '</types>'.
                '<portType name="' .$name. 'Port">'.
                '<operation name="TestFunc"><documentation>Test Function</documentation><input message="tns:TestFuncIn"/><output message="tns:TestFuncOut"/></operation>'.
                '</portType>'.
                '<binding name="' .$name. 'Binding" type="tns:' .$name. 'Port">'.
                '<soap:binding style="document" transport="http://framework.zend.com"/>'.
                '<operation name="TestFunc">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc"/>'.
                '<input><soap:body use="literal" namespace="http://framework.zend.com"/></input>'.
                '<output><soap:body use="literal" namespace="http://framework.zend.com"/></output>'.
                '</operation>'.
                '</binding>'.
                '<service name="' .$name. 'Service">'.
                '<port name="' .$name. 'Port" binding="tns:' .$name. 'Binding">'.
                '<soap:address location="' . $scriptUri . '"/>'.
                '</port>'.
                '</service>'.
                '<message name="TestFuncIn"><part name="parameters" element="tns:TestFunc"/></message>'.
                '<message name="TestFuncOut"><part name="parameters" element="tns:TestFuncResponse"/></message>'.
                '</definitions>';
        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()), "Bad WSDL generated");
        $this->assertValidWSDL($dom);
    }

    /**
     * @group ZF-5072
     */
    public function testAddFunctionSimpleInReturnNameCompabilityMode()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        $dom = $server->generate()->toDomDocument();

        $name = "TestService";

        $wsdl = $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML());
        $this->assertContains('<message name="TestFuncOut"><part name="return" type="xsd:string"/>', $wsdl);
        $this->assertNotContains('<message name="TestFuncOut"><part name="TestFuncReturn"', $wsdl);
        $this->assertValidWSDL($dom);
    }

    public function testAddFunctionMultiple()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc2');
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc3');
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc4');
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc5');
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc6');
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc7');
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc9');

        $dom = $server->generate()->toDomDocument();

        $name = "TestService";

        $wsdl = '<?xml version="1.0" encoding="utf-8"?>'.
                '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="' . $scriptUri . '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" name="' .$name. '" targetNamespace="' . $scriptUri . '">'.
                '<types><xsd:schema targetNamespace="' . $scriptUri . '"/></types>'.
                '<portType name="' .$name. 'Port">'.
                '<operation name="TestFunc"><documentation>Test Function</documentation><input message="tns:TestFuncIn"/><output message="tns:TestFuncOut"/></operation>'.
                '<operation name="TestFunc2"><documentation>Test Function 2</documentation><input message="tns:TestFunc2In"/></operation>'.
                '<operation name="TestFunc3"><documentation>Return false</documentation><input message="tns:TestFunc3In"/><output message="tns:TestFunc3Out"/></operation>'.
                '<operation name="TestFunc4"><documentation>Return true</documentation><input message="tns:TestFunc4In"/><output message="tns:TestFunc4Out"/></operation>'.
                '<operation name="TestFunc5"><documentation>Return integer</documentation><input message="tns:TestFunc5In"/><output message="tns:TestFunc5Out"/></operation>'.
                '<operation name="TestFunc6"><documentation>Return string</documentation><input message="tns:TestFunc6In"/><output message="tns:TestFunc6Out"/></operation>'.
                '<operation name="TestFunc7"><documentation>Return array</documentation><input message="tns:TestFunc7In"/><output message="tns:TestFunc7Out"/></operation>'.
                '<operation name="TestFunc9"><documentation>Multiple Args</documentation><input message="tns:TestFunc9In"/><output message="tns:TestFunc9Out"/></operation>'.
                '</portType>'.
                '<binding name="' .$name. 'Binding" type="tns:' .$name. 'Port">'.
                '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'.
                '<operation name="TestFunc">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="TestFunc2">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc2"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '</operation>'.
                '<operation name="TestFunc3">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc3"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="TestFunc4">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc4"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="TestFunc5">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc5"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="TestFunc6">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc6"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="TestFunc7">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc7"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '<operation name="TestFunc9">'.
                '<soap:operation soapAction="' . $scriptUri . '#TestFunc9"/>'.
                '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></input>'.
                '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $scriptUri . '"/></output>'.
                '</operation>'.
                '</binding>'.
                '<service name="' .$name. 'Service">'.
                '<port name="' .$name. 'Port" binding="tns:' .$name. 'Binding">'.
                '<soap:address location="' . $scriptUri . '"/>'.
                '</port>'.
                '</service>'.
                '<message name="TestFuncIn"><part name="who" type="xsd:string"/></message>'.
                '<message name="TestFuncOut"><part name="return" type="xsd:string"/></message>'.
                '<message name="TestFunc2In"/>'.
                '<message name="TestFunc3In"/>'.
                '<message name="TestFunc3Out"><part name="return" type="xsd:boolean"/></message>'.
                '<message name="TestFunc4In"/>'.
                '<message name="TestFunc4Out"><part name="return" type="xsd:boolean"/></message>'.
                '<message name="TestFunc5In"/>'.
                '<message name="TestFunc5Out"><part name="return" type="xsd:int"/></message>'.
                '<message name="TestFunc6In"/>'.
                '<message name="TestFunc6Out"><part name="return" type="xsd:string"/></message>'.
                '<message name="TestFunc7In"/>'.
                '<message name="TestFunc7Out"><part name="return" type="soap-enc:Array"/></message>'.
                '<message name="TestFunc9In"><part name="foo" type="xsd:string"/><part name="bar" type="xsd:string"/></message>'.
                '<message name="TestFunc9Out"><part name="return" type="xsd:string"/></message>'.
                '</definitions>';
        $this->assertEquals($wsdl, $this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()), "Generated WSDL did not match expected XML");
        $this->assertValidWSDL($dom);
    }

    /**
     * @group ZF-4117
     */
    public function testChangeWsdlUriInConstructor()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = new AutoDiscover(null, "http://example.com/service.php");
        $server->setServiceName("TestService");
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        $wsdlOutput = $server->toXml();

        $this->assertNotContains($scriptUri, $wsdlOutput);
        $this->assertContains("http://example.com/service.php", $wsdlOutput);
    }

    /**
     * @group ZF-4117
     */
    public function testChangeWsdlUriViaSetUri()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->setUri("http://example.com/service.php");
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        $wsdlOutput = $server->toXml();

        $this->assertNotContains($scriptUri, $wsdlOutput);
        $this->assertContains("http://example.com/service.php", $wsdlOutput);
    }

    public function testSetNonStringNonZendUriUriThrowsException()
    {
        $server = $this->createAutodiscoverService();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'No uri given to');
        $server->setUri(array("bogus"));
    }

    /**
     * @group ZF-4117
     */
    public function testChangingWsdlUriAfterGenerationIsPossible()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->setUri("http://example.com/service.php");
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        $wsdlOutput = $server->toXml();

        $this->assertNotContains($scriptUri, $wsdlOutput);
        $this->assertContains("http://example.com/service.php", $wsdlOutput);

        $server->setUri("http://example2.com/service2.php");

        $wsdlOutput = $server->toXml();

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

        $server = $this->createAutodiscoverService();
        $server->setClass('\ZendTest\Soap\TestAsset\TestFixingMultiplePrototypes');

        $wsdlOutput = $server->toXml();

        $this->assertEquals(1, substr_count($wsdlOutput, '<message name="testFuncIn">'));
        $this->assertEquals(1, substr_count($wsdlOutput, '<message name="testFuncOut">'));
    }

    /**
     * @group ZF-4937
     */
    public function testComplexTypesThatAreUsedMultipleTimesAreRecoginzedOnce()
    {
        $server = $this->createAutodiscoverService();
        $server->setComplexTypeStrategy(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex);
        $server->setClass('\ZendTest\Soap\TestAsset\AutoDiscoverTestClass2');

        $wsdlOutput = $server->toXml();

        $this->assertEquals(1,
            substr_count($wsdlOutput, 'wsdl:arrayType="tns:AutoDiscoverTestClass1[]"'),
            'wsdl:arrayType definition of TestClass1 has to occour once.'
        );
        $this->assertEquals(1,
            substr_count($wsdlOutput, '<xsd:complexType name="AutoDiscoverTestClass1">'),
            '\ZendTest\Soap\TestAsset\AutoDiscoverTestClass1 has to be defined once.'
        );
        $this->assertEquals(1,
            substr_count($wsdlOutput, '<xsd:complexType name="ArrayOfAutoDiscoverTestClass1">'),
            '\ZendTest\Soap\TestAsset\AutoDiscoverTestClass1 should be defined once.'
        );
        $this->assertTrue(
            substr_count($wsdlOutput, '<part name="test" type="tns:AutoDiscoverTestClass1"/>') >= 1,
            '\ZendTest\Soap\TestAsset\AutoDiscoverTestClass1 appears once or more than once in the message parts section.'
        );
    }

    /**
     * @group ZF-5604
     */
    public function testReturnSameArrayOfObjectsResponseOnDifferentMethodsWhenArrayComplex()
    {
        $autodiscover = $this->createAutodiscoverService();
        $autodiscover->setComplexTypeStrategy(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex);
        $autodiscover->setClass('\ZendTest\Soap\TestAsset\MyService');
        $wsdl = $autodiscover->toXml();

        $this->assertEquals(1, substr_count($wsdl, '<xsd:complexType name="ArrayOfMyResponse">'));

        $this->assertEquals(0, substr_count($wsdl, 'tns:My_Response[]'));
    }

    /**
     * @group ZF-5430
     */
    public function testReturnSameArrayOfObjectsResponseOnDifferentMethodsWhenArraySequence()
    {
        $autodiscover = $this->createAutodiscoverService();
        $autodiscover->setComplexTypeStrategy(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence);
        $autodiscover->setClass('\ZendTest\Soap\TestAsset\MyServiceSequence');
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
        $autodiscover = new AutoDiscover();
        $autodiscover->setUri("http://example.com/?a=b&amp;b=c");

        $autodiscover->setClass('\ZendTest\Soap\TestAsset\Test');
        $wsdl = $autodiscover->toXml();

        $this->assertContains("http://example.com/?a=b&amp;b=c", $wsdl);
    }

    /**
     * @group ZF-6689
     */
    public function testNoReturnIsOneWayCallInSetClass()
    {
        $autodiscover = $this->createAutodiscoverService();
        $autodiscover->setClass('\ZendTest\Soap\TestAsset\NoReturnType');
        $wsdl = $autodiscover->toXml();

        $this->assertContains(
            '<operation name="pushOneWay"><documentation>pushOneWay</documentation><input message="tns:pushOneWayIn"/></operation>',
            $wsdl
        );
    }

    /**
     * @group ZF-6689
     */
    public function testNoReturnIsOneWayCallInAddFunction()
    {
        $autodiscover = $this->createAutodiscoverService();
        $autodiscover->setServiceName('TestService');
        $autodiscover->addFunction('\ZendTest\Soap\TestAsset\OneWay');
        $wsdl = $autodiscover->toXml();

        $this->assertContains(
            '<operation name="OneWay"><documentation>ZendTest\Soap\TestAsset\OneWay</documentation><input message="tns:OneWayIn"/></operation>',
            $wsdl
        );
    }

    /**
     * @group ZF-8948
     * @group ZF-5766
     */
    public function testRecursiveWsdlDependencies()
    {
        $autodiscover = $this->createAutodiscoverService();
        $autodiscover->setComplexTypeStrategy(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence);
        $autodiscover->setClass('\ZendTest\Soap\TestAsset\Recursion');
        $wsdl = $autodiscover->toXml();

        //  <types>
        //      <xsd:schema targetNamespace="http://localhost/my_script.php">
        //          <xsd:complexType name="Zend_Soap_AutoDiscover_Recursion">
        //              <xsd:all>
        //                  <xsd:element name="recursion" type="tns:Zend_Soap_AutoDiscover_Recursion"/>


        $path = '//wsdl:types/xsd:schema/xsd:complexType[@name="Recursion"]/xsd:all/xsd:element[@name="recursion" and @type="tns:Recursion"]';
        $this->assertWsdlPathExists($wsdl, $path);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandle()
    {
        $scriptUri = 'http://localhost/my_script.php';

        $server = $this->createAutodiscoverService();
        $server->setClass('\ZendTest\Soap\TestAsset\Test');

        ob_start();
        $server->handle();
        $actualWsdl = ob_get_clean();
        $this->assertNotEmpty($actualWsdl, "WSDL content was not outputted.");
        $this->assertContains($scriptUri, $actualWsdl, "Script URL was not found in WSDL content.");
    }

    public function assertWsdlPathExists($xml, $path)
    {
        $doc = new \DOMDocument('UTF-8');
        $doc->loadXML($xml);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('wsdl', 'http://schemas.xmlsoap.org/wsdl/');

        $nodes = $xpath->query($path);

        $this->assertTrue($nodes->length >= 1, "Could not assert that XML Document contains a node that matches the XPath Expression: " . $path);
    }
}
