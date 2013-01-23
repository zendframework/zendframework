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

use Zend\Soap\Wsdl;
use Zend\Soap\Wsdl\ComplexTypeStrategy;

/**
 * Test cases for Zend_Soap_Wsdl
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class WsdlTest extends \PHPUnit_Framework_TestCase
{
    protected function sanitizeWsdlXmlOutputForOsCompability($xmlstring)
    {
        $xmlstring = str_replace(array("\r", "\n"), "", $xmlstring);
        $xmlstring = preg_replace('/(>[\s]{1,}<)/', '', $xmlstring);
        return $xmlstring;
    }

    public function swallowIncludeNotices($errno, $errstr)
    {
        if ($errno != E_WARNING || !strstr($errstr, 'failed')) {
            return false;
        }
    }

    public function testConstructor()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                                 . 'xmlns:tns="http://localhost/MyService.php" '
                                 . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                                 . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                                 . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                                 . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                                 . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                                 . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );
    }

    public function testSetUriChangesDomDocumentWsdlStructureTnsAndTargetNamespaceAttributes()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $wsdl->setUri('http://localhost/MyNewService.php');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                                 . 'xmlns:tns="http://localhost/MyNewService.php" '
                                 . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                                 . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                                 . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                                 . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                                 . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                                 . 'name="MyService" targetNamespace="http://localhost/MyNewService.php"/>' );
    }

    public function testAddMessage()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $messageParts = array();
        $messageParts['parameter1'] = $wsdl->getType('int');
        $messageParts['parameter2'] = $wsdl->getType('string');
        $messageParts['parameter3'] = $wsdl->getType('mixed');

        $wsdl->addMessage('myMessage', $messageParts);

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<message name="myMessage">'
                               .   '<part name="parameter1" type="xsd:int"/>'
                               .   '<part name="parameter2" type="xsd:string"/>'
                               .   '<part name="parameter3" type="xsd:anyType"/>'
                               . '</message>'
                          . '</definitions>' );
    }

    public function testAddPortType()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                          . '</definitions>' );
    }

    public function testAddPortOperation()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $portType = $wsdl->addPortType('myPortType');

        $wsdl->addPortOperation($portType, 'operation1');
        $wsdl->addPortOperation($portType, 'operation2', 'tns:operation2Request', 'tns:operation2Response');
        $wsdl->addPortOperation($portType, 'operation3', 'tns:operation3Request', 'tns:operation3Response', 'tns:operation3Fault');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType">'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input message="tns:operation2Request"/>'
                               .     '<output message="tns:operation2Response"/>'
                               .   '</operation>'
                               .   '<operation name="operation3">'
                               .     '<input message="tns:operation3Request"/>'
                               .     '<output message="tns:operation3Response"/>'
                               .     '<fault message="tns:operation3Fault"/>'
                               .   '</operation>'
                               . '</portType>'
                          . '</definitions>' );
    }

    public function testAddBinding()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType"/>'
                          . '</definitions>' );
    }

    public function testAddBindingOperation()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );
        $wsdl->addBindingOperation($binding,
                                   'operation3',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('name' => 'MyFault','use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                   );

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               .   '<operation name="operation3">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .     '<fault name="MyFault">'
                               .       '<soap:fault name="MyFault" use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</fault>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' );
    }

    public function testAddSoapBinding()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addSoapBinding($binding);

        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' );

        $wsdl1 = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl1->addPortType('myPortType');
        $binding = $wsdl1->addBinding('MyServiceBinding', 'myPortType');

        $wsdl1->addSoapBinding($binding, 'rpc');

        $wsdl1->addBindingOperation($binding, 'operation1');
        $wsdl1->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl1->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' );
    }


    public function testAddSoapOperation()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addSoapOperation($binding, 'http://localhost/MyService.php#myOperation');

        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:operation soapAction="http://localhost/MyService.php#myOperation"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' );
    }

    public function testAddService()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addService('Service1', 'myPortType', 'MyServiceBinding', 'http://localhost/MyService.php');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType"/>'
                               . '<service name="Service1">'
                               .   '<port name="myPortType" binding="MyServiceBinding">'
                               .     '<soap:address location="http://localhost/MyService.php"/>'
                               .   '</port>'
                               . '</service>'
                          . '</definitions>' );
    }

    /**
     * @dataProvider ampersandInUrlDataProvider()
     */
    public function testAddBindingOperationWithAmpersandInUrl($actualUrl, $expectedUrl)
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addBindingOperation(
            $binding,
            'operation1',
            array('use' => 'encoded', 'encodingStyle' => $actualUrl),
            array('use' => 'encoded', 'encodingStyle' => $actualUrl),
            array('name' => 'MyFault','use' => 'encoded', 'encodingStyle' => $actualUrl)
        );

        $expectedXml = '<operation name="operation1">'
                       . '<input>'
                       .   '<soap:body use="encoded" encodingStyle="' . $expectedUrl . '"/>'
                       . '</input>'
                       . '<output>'
                       .   '<soap:body use="encoded" encodingStyle="' . $expectedUrl . '"/>'
                       . '</output>'
                       . '<fault name="MyFault">'
                       .   '<soap:fault name="MyFault" use="encoded" encodingStyle="' . $expectedUrl . '"/>'
                       . '</fault>'
                     . '</operation>';
        $this->assertContains($expectedXml, $wsdl->toXML());
    }

    /**
     * @dataProvider ampersandInUrlDataProvider()
     */
    public function testAddSoapOperationWithAmpersandInUrl($actualUrl, $expectedUrl)
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addSoapOperation($binding, $actualUrl);

        $expectedXml = '<soap:operation soapAction="' . $expectedUrl . '"/>';
        $this->assertContains($expectedXml, $wsdl->toXML());
    }

    /**
     * @dataProvider ampersandInUrlDataProvider()
     */
    public function testAddServiceWithAmpersandInUrl($actualUrl, $expectedUrl)
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addService('Service1', 'myPortType', 'MyServiceBinding', $actualUrl);

        $expectedXml = '<service name="Service1">'
                       . '<port name="myPortType" binding="MyServiceBinding">'
                       .   '<soap:address location="' . $expectedUrl . '"/>'
                       . '</port>'
                     . '</service>';
        $this->assertContains($expectedXml, $wsdl->toXML());
    }

    public function ampersandInUrlDataProvider()
    {
        return array(
            'Decoded ampersand' => array(
                'http://localhost/MyService.php?foo=bar&baz=qux',
                'http://localhost/MyService.php?foo=bar&amp;baz=qux',
            ),
            'Encoded ampersand' => array(
                'http://localhost/MyService.php?foo=bar&amp;baz=qux',
                'http://localhost/MyService.php?foo=bar&amp;baz=qux',
            ),
            'Encoded and decoded ampersand' => array(
                'http://localhost/MyService.php?foo=bar&&amp;baz=qux',
                'http://localhost/MyService.php?foo=bar&amp;&amp;baz=qux',
            ),
        );
    }

    public function testAddDocumentation()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $portType = $wsdl->addPortType('myPortType');

        $wsdl->addDocumentation($portType, 'This is a description for Port Type node.');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType">'
                               .   '<documentation>This is a description for Port Type node.</documentation>'
                               . '</portType>'
                          . '</definitions>' );
    }

    public function testAddDocumentationToSetInsertsBefore()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $messageParts = array();
        $messageParts['parameter1'] = $wsdl->getType('int');
        $messageParts['parameter2'] = $wsdl->getType('string');
        $messageParts['parameter3'] = $wsdl->getType('mixed');

        $message = $wsdl->addMessage('myMessage', $messageParts);
        $wsdl->addDocumentation($message, "foo");

        $this->assertEquals(
            '<?xml version="1.0" encoding="utf-8"?>'  .
            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
               . 'xmlns:tns="http://localhost/MyService.php" '
               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
               . '<message name="myMessage">'
               .   '<documentation>foo</documentation>'
               .   '<part name="parameter1" type="xsd:int"/>'
               .   '<part name="parameter2" type="xsd:string"/>'
               .   '<part name="parameter3" type="xsd:anyType"/>'
               . '</message>'
            . '</definitions>',
            $this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml())
        );
    }

    public function testToXml()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );
    }

    public function testToDomDocument()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $dom = $wsdl->toDomDocument();

        $this->assertTrue($dom instanceOf \DOMDocument);

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($dom->saveXML()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );
    }

    public function testDump()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        ob_start();
        $wsdl->dump();
        $wsdlDump = ob_get_clean();

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdlDump),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );

        $wsdl->dump(__DIR__ . '/TestAsset/dumped.wsdl');
        $dumpedContent = file_get_contents(__DIR__ . '/TestAsset/dumped.wsdl');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($dumpedContent),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );

        unlink(__DIR__ . '/TestAsset/dumped.wsdl');
    }

    public function testGetType()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals('xsd:string',       $wsdl->getType('string'),  'xsd:string detection failed.');
        $this->assertEquals('xsd:string',       $wsdl->getType('str'),     'xsd:string detection failed.');
        $this->assertEquals('xsd:int',          $wsdl->getType('int'),     'xsd:int detection failed.');
        $this->assertEquals('xsd:int',          $wsdl->getType('integer'), 'xsd:int detection failed.');
        $this->assertEquals('xsd:float',        $wsdl->getType('float'),   'xsd:float detection failed.');
        $this->assertEquals('xsd:double',        $wsdl->getType('double'),  'xsd:double detection failed.');
        $this->assertEquals('xsd:boolean',      $wsdl->getType('boolean'), 'xsd:boolean detection failed.');
        $this->assertEquals('xsd:boolean',      $wsdl->getType('bool'),    'xsd:boolean detection failed.');
        $this->assertEquals('soap-enc:Array',   $wsdl->getType('array'),   'soap-enc:Array detection failed.');
        $this->assertEquals('xsd:struct',       $wsdl->getType('object'),  'xsd:struct detection failed.');
        $this->assertEquals('xsd:anyType',      $wsdl->getType('mixed'),   'xsd:anyType detection failed.');
        $this->assertEquals('',                 $wsdl->getType('void'),    'void  detection failed.');
    }

    public function testGetComplexTypeBasedOnStrategiesBackwardsCompabilityBoolean()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $this->assertEquals('tns:WsdlTestClass', $wsdl->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($wsdl->getComplexTypeStrategy() instanceof ComplexTypeStrategy\DefaultComplexType);

//        $wsdl2 = new Wsdl('MyService', 'http://localhost/MyService.php', false);
//        $this->assertEquals('xsd:anyType', $wsdl2->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
//        $this->assertTrue($wsdl2->getComplexTypeStrategy() instanceof ComplexTypeStrategy\AnyType);
    }

    public function testGetComplexTypeBasedOnStrategiesStringNames()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php', new \Zend\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType);
        $this->assertEquals('tns:WsdlTestClass', $wsdl->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($wsdl->getComplexTypeStrategy() instanceof ComplexTypeStrategy\DefaultComplexType);

        $wsdl2 = new Wsdl('MyService', 'http://localhost/MyService.php', new \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType);
        $this->assertEquals('xsd:anyType', $wsdl2->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($wsdl2->getComplexTypeStrategy() instanceof ComplexTypeStrategy\AnyType);
    }

    public function testAddingSameComplexTypeMoreThanOnceIsIgnored()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $wsdl->addType('\ZendTest\Soap\TestAsset\WsdlTestClass', 'tns:SomeTypeName');
        $wsdl->addType('\ZendTest\Soap\TestAsset\WsdlTestClass', 'tns:AnotherTypeName');
        $types = $wsdl->getTypes();
        $this->assertEquals(1, count($types));
        $this->assertEquals(array('\ZendTest\Soap\TestAsset\WsdlTestClass' => 'tns:SomeTypeName'),
                            $types);
    }

    public function testUsingSameComplexTypeTwiceLeadsToReuseOfDefinition()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');
        $this->assertEquals(array('\ZendTest\Soap\TestAsset\WsdlTestClass' =>
                                     'tns:WsdlTestClass'),
                            $wsdl->getTypes());

        $wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');
        $this->assertEquals(array('\ZendTest\Soap\TestAsset\WsdlTestClass' =>
                                     'tns:WsdlTestClass'),
                            $wsdl->getTypes());
    }

    public function testAddComplexType()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdl->toXml()),
                            '<?xml version="1.0" encoding="utf-8"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<types>'
                               .   '<xsd:schema targetNamespace="http://localhost/MyService.php">'
                               .     '<xsd:complexType name="WsdlTestClass">'
                               .       '<xsd:all>'
                               .         '<xsd:element name="var1" type="xsd:int" nillable="true"/>'
                               .         '<xsd:element name="var2" type="xsd:string" nillable="true"/>'
                               .       '</xsd:all>'
                               .     '</xsd:complexType>'
                               .   '</xsd:schema>'
                               . '</types>'
                          . '</definitions>' );
    }

    /**
     * @group ZF-3910
     */
    public function testCaseOfDocBlockParamsDosNotMatterForSoapTypeDetectionZf3910()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals("xsd:string", $wsdl->getType("StrIng"));
        $this->assertEquals("xsd:string", $wsdl->getType("sTr"));
        $this->assertEquals("xsd:int", $wsdl->getType("iNt"));
        $this->assertEquals("xsd:int", $wsdl->getType("INTEGER"));
        $this->assertEquals("xsd:float", $wsdl->getType("FLOAT"));
        $this->assertEquals("xsd:double", $wsdl->getType("douBLE"));
    }

    /**
     * @group ZF-11937
     */
    public function testWsdlGetTypeWillAllowLongType()
    {
        $wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $this->assertEquals("xsd:long", $wsdl->getType("long"));
    }

    /**
     * @group ZF-5430
     */
    public function testMultipleSequenceDefinitionsOfSameTypeWillBeRecognizedOnceBySequenceStrategy()
    {
        $wsdl = new Wsdl("MyService", "http://localhost/MyService.php");
        $wsdl->setComplexTypeStrategy(new ComplexTypeStrategy\ArrayOfTypeSequence());

        $wsdl->addComplexType("string[]");
        $wsdl->addComplexType("int[]");
        $wsdl->addComplexType("string[]");

        $xml = $wsdl->toXml();
        $this->assertEquals(1, substr_count($xml, "ArrayOfString"), "ArrayOfString should appear only once.");
        $this->assertEquals(1, substr_count($xml, "ArrayOfInt"),    "ArrayOfInt should appear only once.");
    }

    const URI_WITH_EXPANDED_AMP = "http://localhost/MyService.php?a%3Db%26b%3Dc";
    const URI_WITHOUT_EXPANDED_AMP = "http://localhost/MyService.php?a=b&b=c";

    /**
     * @group ZF-5736
     */
    public function testHtmlAmpersandInUrlInConstructorIsEncodedCorrectly()
    {
        $wsdl = new Wsdl("MyService", self::URI_WITHOUT_EXPANDED_AMP);
        $this->assertContains(self::URI_WITH_EXPANDED_AMP, $wsdl->toXML());
    }

    /**
     * @group ZF-5736
     */
    public function testHtmlAmpersandInUrlInSetUriIsEncodedCorrectly()
    {
        $wsdl = new Wsdl("MyService", "http://example.com");
        $wsdl->setUri(self::URI_WITHOUT_EXPANDED_AMP);
        $this->assertContains(self::URI_WITH_EXPANDED_AMP, $wsdl->toXML());
    }
}
