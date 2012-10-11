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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Soap;
use Zend\Soap\Wsdl,
    Zend\Soap\Wsdl\ComplexTypeStrategy;

use Zend\Uri\Uri;

/**
 * Test cases for Zend_Soap_Wsdl
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class WsdlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\Soap\Wsdl
     */
    protected $wsdl;

    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var \DOMXPath
     */
    protected $xpath;

    /**
     * @deprecated Move to native DOM
     * @param $xmlstring
     * @return mixed
     */
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


    public function setUp()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->dom = $this->wsdl->toDomDocument();
        $this->xpath = new \DOMXPath($this->dom);
        $this->xpath->registerNamespace('unittest', Wsdl::NS_WSDL);

        $this->xpath->registerNamespace('tns',      'http://localhost/MyService.php');
        $this->xpath->registerNamespace('soap',     Wsdl::NS_SOAP);
        $this->xpath->registerNamespace('xsd',      Wsdl::NS_SCHEMA);
        $this->xpath->registerNamespace('soap-enc', Wsdl::NS_S_ENC);
        $this->xpath->registerNamespace('wsdl',     Wsdl::NS_WSDL);
    }

    function testConstructor()
    {

        $uri = 'http://localhost/MyService.php';
        $name = 'MyService';

        $this->assertEquals(Wsdl::NS_WSDL,          $this->dom->lookupNamespaceUri(null));
        $this->assertEquals(Wsdl::NS_SOAP,          $this->dom->lookupNamespaceUri('soap'));
        $this->assertEquals($uri,                   $this->dom->lookupNamespaceUri('tns'));
        $this->assertEquals(Wsdl::NS_SOAP,          $this->dom->lookupNamespaceUri('soap'));
        $this->assertEquals(Wsdl::NS_SCHEMA,        $this->dom->lookupNamespaceUri('xsd'));
        $this->assertEquals(Wsdl::NS_S_ENC,         $this->dom->lookupNamespaceUri('soap-enc'));
        $this->assertEquals(Wsdl::NS_WSDL,          $this->dom->lookupNamespaceUri('wsdl'));

        $this->assertEquals(Wsdl::NS_WSDL,          $this->dom->documentElement->namespaceURI);

        $this->assertEquals($name,  $this->dom->documentElement->getAttributeNS(Wsdl::NS_WSDL, 'name'));
        $this->assertEquals($uri,   $this->dom->documentElement->getAttributeNS(Wsdl::NS_WSDL, 'targetNamespace'));

    }

    function testSetUriChangesDomDocumentWsdlStructureTnsAndTargetNamespaceAttributes()
    {
        $newUri = 'http://localhost/MyNewService.php';
        $this->wsdl->setUri($newUri);

        $this->assertEquals($newUri, $this->dom->lookupNamespaceUri('tns'));
        $this->assertEquals($newUri, $this->dom->documentElement->getAttributeNS(Wsdl::NS_WSDL, 'targetNamespace'));
    }

    function testSetUriWithZendUriChangesDomDocumentWsdlStructureTnsAndTargetNamespaceAttributes()
    {
        $newUri = 'http://localhost/MyNewService.php';
        $this->wsdl->setUri(new Uri('http://localhost/MyNewService.php'));

        $this->assertEquals($newUri, $this->dom->lookupNamespaceUri('tns'));
        $this->assertEquals($newUri, $this->dom->documentElement->getAttributeNS(Wsdl::NS_WSDL, 'targetNamespace'));
    }

    function testAddMessage()
    {
        $messageParts = array();

        $messageParts['parameter1'] = $this->wsdl->getType('int');
        $messageParts['parameter2'] = $this->wsdl->getType('string');
        $messageParts['parameter3'] = $this->wsdl->getType('mixed');

        $messageName = 'myMessage';

        $this->wsdl->addMessage($messageName, $messageParts);

        $messageNodes = $this->xpath->query('//wsdl:definitions/message');
		$this->assertNotEquals(0, $messageNodes->length, 'Missing message node in definitions node.');

        $this->assertEquals($messageName, $messageNodes->item(0)->getAttribute('name'));

        foreach ($messageParts as $parameterName => $parameterType) {
            $part = $this->xpath->query('part[@name="'.$parameterName.'"]', $messageNodes->item(0));
            $this->assertEquals($parameterType, $part->item(0)->getAttribute('type'));
        }
    }

    function testAddPortType()
    {
        $portName = 'myPortType';
        $this->wsdl->addPortType($portName);

        $portTypeNodes = $this->xpath->query('//wsdl:definitions/portType');
		$this->assertNotEquals(0, $portTypeNodes->length, 'Missing portType node in definitions node.');

        $this->assertTrue($portTypeNodes->item(0)->hasAttribute('name'));
        $this->assertEquals($portName, $portTypeNodes->item(0)->getAttribute('name'));
    }

	public function testAddPortOperationCheckName()
	{
		$portName = 'myPortType';
		$portType = $this->wsdl->addPortType($portName);

		$this->wsdl->addPortOperation($portType, 'operation');

		$portTypeNodes = $this->xpath->query('//wsdl:definitions/portType[@name="'.$portName.'"]');
		$this->assertNotEquals(0, $portTypeNodes->length, 'Missing portType node in definitions node.');
	}

	public function testAddPortOperationWithNoRequestAndResponse()
	{
		$portName = 'myPortType';
		$portType = $this->wsdl->addPortType($portName);

		$this->wsdl->addPortOperation($portType, 'operationName');

		$portTypeNodes = $this->xpath->query('//wsdl:definitions/portType[@name="'.$portName.'"]');

		$operation1Nodes = $this->xpath->query('operation[@name="operationName"]', $portTypeNodes->item(0));
		$this->assertEquals(1, $operation1Nodes->length);
		$this->assertFalse($operation1Nodes->item(0)->hasChildNodes());
	}

	public function testAddPortOperationWithRequest()
	{
		$portName = 'myPortType';
		$portType = $this->wsdl->addPortType($portName);

		$this->wsdl->addPortOperation($portType, 'operationName', 'operationRequest');

		$portTypeNodes = $this->xpath->query('//wsdl:definitions/portType[@name="'.$portName.'"]');

		$operationNodes = $this->xpath->query('operation[@name="operationName"]', $portTypeNodes->item(0));
		$this->assertEquals(1, $operationNodes->length, 'Missing operation node');

		$result = $this->xpath->query('input[@message="operationRequest"]', $operationNodes->item(0));
		$this->assertEquals(1, $result->length, 'Missing input operation');
	}


	public function testAddPortOperationWithRequestAndResponse() {
		$portName = 'myPortType';
		$portType = $this->wsdl->addPortType($portName);

		$this->wsdl->addPortOperation($portType, 'operationName', 'operationRequest', 'operationResponse');

		$portTypeNodes = $this->xpath->query('//wsdl:definitions/portType[@name="'.$portName.'"]');

		$operationNodes = $this->xpath->query('operation[@name="operationName"]', $portTypeNodes->item(0));
		$this->assertEquals(1, $operationNodes->length, 'Missing operation node');

		$result = $this->xpath->query('input[@message="operationRequest"]', $operationNodes->item(0));
		$this->assertEquals(1, $result->length, 'Missing input operation');

		$result = $this->xpath->query('output[@message="operationResponse"]', $operationNodes->item(0));
		$this->assertEquals(1, $result->length, 'Missing output operation');
	}


	public function testAddPortOperationWithRequestAndResponseAndOneFault() {
		$portName = 'myPortType';
		$portType = $this->wsdl->addPortType($portName);

		$this->wsdl->addPortOperation($portType, 'operationName', 'operationRequest', 'operationResponse', array('FaultName'));

		$portTypeNodes = $this->xpath->query('//wsdl:definitions/portType[@name="'.$portName.'"]');

		$operationNodes = $this->xpath->query('operation[@name="operationName"]', $portTypeNodes->item(0));
		$this->assertEquals(1, $operationNodes->length, 'Missing operation node');

		$result = $this->xpath->query('input[@message="operationRequest"]', $operationNodes->item(0));
		$this->assertEquals(1, $result->length, 'Missing input operation');

		$result = $this->xpath->query('output[@message="operationResponse"]', $operationNodes->item(0));
		$this->assertEquals(1, $result->length, 'Missing output operation');

		$result = $this->xpath->query('fault[@message="FaultName"]', $operationNodes->item(0));
		$this->assertEquals(1, $result->length, 'Missing fault: FaultName');
	}

	public function testAddPortOperationWithRequestAndResponseAndMultipleFaults(){
		$portName = 'myPortType';
		$portType = $this->wsdl->addPortType($portName);

		$faultNames = array(
			'FaultName1',
			'FaultName2',
			'FaultName3'
		);

		$this->wsdl->addPortOperation($portType, 'operationName', 'operationRequest', 'operationResponse', $faultNames);

		$portTypeNodes = $this->xpath->query('//wsdl:definitions/portType[@name="'.$portName.'"]');

		$operationNodes = $this->xpath->query('operation[@name="operationName"]', $portTypeNodes->item(0));
		$this->assertEquals(1, $operationNodes->length, 'Missing operation node');

		$result = $this->xpath->query('input[@message="operationRequest"]', $operationNodes->item(0));
		$this->assertEquals(1, $result->length, 'Missing input operation');

		$result = $this->xpath->query('output[@message="operationResponse"]', $operationNodes->item(0));
		$this->assertEquals(1, $result->length, 'Missing output operation');

		foreach($faultNames as $faultName) {
			$result = $this->xpath->query('fault[@message="'.$faultName.'"]', $operationNodes->item(0));
			$this->assertEquals(1, $result->length, 'Missing fault: '.$faultName);
		}
	}

    function testAddBinding()
    {
        $this->wsdl->addBinding('MyServiceBinding', 'myPortType');

        $bindingNodes = $this->xpath->query('//wsdl:definitions/wsdl:binding');

		$this->assertNotEquals(0, $bindingNodes->length, 'Missing binding node in definitions node');

        $this->assertEquals('MyServiceBinding',     $bindingNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'name'));
        $this->assertEquals('myPortType',           $bindingNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'type'));
    }

	public function testAddBindingOperationEmptyOperation()
	{
		$binding = $this->wsdl->addBinding('MyServiceBinding', 'myPortType');
		$this->wsdl->addBindingOperation($binding, 'operation1');

		$bindingNodes = $this->xpath->query('//wsdl:binding');
		$this->assertNotEquals(0, $bindingNodes->length, 'Missing binding node in definitions node.');

		$this->assertEquals('MyServiceBinding',     $bindingNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'name'));
		$this->assertEquals('myPortType',           $bindingNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'type'));
	}

	public function testAddBindingOperationOneWayOperation()
	{

	}





    function testAddBindingOperation()
    {


        $encodingStyle = "http://schemas.xmlsoap.org/soap/encoding/";
        $this->wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => $encodingStyle),
                                   array('use' => 'encoded', 'encodingStyle' => $encodingStyle)
        );


        $this->wsdl->addBindingOperation($binding,
                                   'operation3',
                                   array('use' => 'encoded', 'encodingStyle' => $encodingStyle),
                                   array('use' => 'encoded', 'encodingStyle' => $encodingStyle),
                                   array('use' => 'encoded', 'encodingStyle' => $encodingStyle, 'name' => array('MyFault'))
        );

        $bindingNodes = $this->xpath->query('//wsdl:binding');

        if ($bindingNodes->length === 0) {
            $this->fail('Missing binding node in definitions node.'.$bindingNodes->length);
        }

        $this->assertEquals('MyServiceBinding',     $bindingNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'name'));
        $this->assertEquals('myPortType',           $bindingNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'type'));


        $operation1Nodes = $this->xpath->query('//wsdl:operation[@name="operation1"]', $bindingNodes->item(0));
        $this->assertEquals(1, $operation1Nodes->length);
        //@todo namespace ?
        $this->assertFalse($operation1Nodes->item(0)->hasChildNodes());

        $inputBodyNodes = $this->xpath->query('//wsdl:operation[@name="operation2"]/wsdl:input/soap:body', $bindingNodes->item(0));
        if ($inputBodyNodes->length === 0){
            $this->fail('Unable to find body in binding operation node');
        }
        $this->assertEquals('encoded',      $inputBodyNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'use'));
        $this->assertEquals($encodingStyle, $inputBodyNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'encodingStyle'));

        foreach(array('input', 'output') as $direction) {
            foreach(array('operation2', 'operation3') as $operation) {
                $inputBodyNodes = $this->xpath->query('//wsdl:operation[@name="'.$operation.'"]/wsdl:'.$direction.'/soap:body', $bindingNodes->item(0));
                if ($inputBodyNodes->length === 0){
                    $this->fail('Unable to find body in binding '.$operation.' node');
                }
                $this->assertEquals('encoded',       $inputBodyNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'use'));
                $this->assertEquals($encodingStyle,  $inputBodyNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'encodingStyle'));
            }
        }

        $faultNodes = $this->xpath->query('//wsdl:operation[@name="operation3"]/wsdl:fault', $bindingNodes->item(0));
        if ($faultNodes->length === 0){
            $this->fail('Unable to find fault operation node');
        }
        $this->assertEquals('encoded',       $faultNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'use'));
        $this->assertEquals('MyFault',       $faultNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'name'));
        $this->assertEquals($encodingStyle,  $faultNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'encodingStyle'));


//        $this->wsdl->toDomDocument()->formatOutput = true;
//        print ($this->wsdl->toDomDocument()->saveXML());exit;


//        $inputBodyNodes = $this->xpath->query('//wsdl:operation[@name="operation3"]/wsdl:input/soap:body', $bindingNodes->item(0));
//        if ($inputBodyNodes->length === 0){
//            $this->fail('Unable to find body in binding operation node');
//        }
//        $this->assertEquals('encoded',       $inputBodyNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'use'));
//        $this->assertEquals($encodingStyle,  $inputBodyNodes->item(0)->getAttributeNS(Wsdl::NS_WSDL, 'encodingStyle'));




        ($this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml()).
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
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

    function testAddSoapBinding()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->wsdl->addPortType('myPortType');
        $binding = $this->wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->wsdl->addSoapBinding($binding);

        $this->wsdl->addBindingOperation($binding, 'operation1');
        $this->wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml()),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
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
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
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


    function testAddSoapOperation()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->wsdl->addPortType('myPortType');
        $binding = $this->wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->wsdl->addSoapOperation($binding, 'http://localhost/MyService.php#myOperation');

        $this->wsdl->addBindingOperation($binding, 'operation1');
        $this->wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml()),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
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

    function testAddService()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->wsdl->addPortType('myPortType');
        $this->wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->wsdl->addService('Service1', 'myPortType', 'MyServiceBinding', 'http://localhost/MyService.php');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml()),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
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

    function testAddDocumentation()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $portType = $this->wsdl->addPortType('myPortType');

        $this->wsdl->addDocumentation($portType, 'This is a description for Port Type node.');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml()),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
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
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $messageParts = array();
        $messageParts['parameter1'] = $this->wsdl->getType('int');
        $messageParts['parameter2'] = $this->wsdl->getType('string');
        $messageParts['parameter3'] = $this->wsdl->getType('mixed');

        $message = $this->wsdl->addMessage('myMessage', $messageParts);
        $this->wsdl->addDocumentation($message, "foo");

        $this->assertEquals(
            '<?xml version="1.0"?>'  .
            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
               . 'xmlns:tns="http://localhost/MyService.php" '
               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
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
            $this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml())
        );
    }

    function testToXml()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml()),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );
    }

    function testToDomDocument()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $this->dom = $this->wsdl->toDomDocument();

        $this->assertTrue($this->dom instanceOf \DOMDocument);

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($this->dom->saveXML()),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );
    }

    function testDump()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        ob_start();
        $this->wsdl->dump();
        $wsdlDump = ob_get_clean();

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($wsdlDump),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );

        $this->wsdl->dump(__DIR__ . '/TestAsset/dumped.wsdl');
        $dumpedContent = file_get_contents(__DIR__ . '/TestAsset/dumped.wsdl');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($dumpedContent),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' );

        unlink(__DIR__ . '/TestAsset/dumped.wsdl');
    }

    function testGetType()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals('xsd:string',       $this->wsdl->getType('string'),  'xsd:string detection failed.');
        $this->assertEquals('xsd:string',       $this->wsdl->getType('str'),     'xsd:string detection failed.');
        $this->assertEquals('xsd:int',          $this->wsdl->getType('int'),     'xsd:int detection failed.');
        $this->assertEquals('xsd:int',          $this->wsdl->getType('integer'), 'xsd:int detection failed.');
        $this->assertEquals('xsd:float',        $this->wsdl->getType('float'),   'xsd:float detection failed.');
        $this->assertEquals('xsd:double',       $this->wsdl->getType('double'),  'xsd:double detection failed.');
        $this->assertEquals('xsd:boolean',      $this->wsdl->getType('boolean'), 'xsd:boolean detection failed.');
        $this->assertEquals('xsd:boolean',      $this->wsdl->getType('bool'),    'xsd:boolean detection failed.');
        $this->assertEquals('soap-enc:Array',   $this->wsdl->getType('array'),   'soap-enc:Array detection failed.');
        $this->assertEquals('xsd:struct',       $this->wsdl->getType('object'),  'xsd:struct detection failed.');
        $this->assertEquals('xsd:anyType',      $this->wsdl->getType('mixed'),   'xsd:anyType detection failed.');
        $this->assertEquals('',                 $this->wsdl->getType('void'),    'void  detection failed.');
    }

    function testGetComplexTypeBasedOnStrategiesBackwardsCompabilityBoolean()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $this->assertEquals('tns:WsdlTestClass', $this->wsdl->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof ComplexTypeStrategy\DefaultComplexType);

//        $wsdl2 = new Wsdl('MyService', 'http://localhost/MyService.php', false);
//        $this->assertEquals('xsd:anyType', $wsdl2->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
//        $this->assertTrue($wsdl2->getComplexTypeStrategy() instanceof ComplexTypeStrategy\AnyType);
    }

    function testGetComplexTypeBasedOnStrategiesStringNames()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php', new \Zend\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType);
        $this->assertEquals('tns:WsdlTestClass', $this->wsdl->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof ComplexTypeStrategy\DefaultComplexType);

        $wsdl2 = new Wsdl('MyService', 'http://localhost/MyService.php', new \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType);
        $this->assertEquals('xsd:anyType', $wsdl2->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($wsdl2->getComplexTypeStrategy() instanceof ComplexTypeStrategy\AnyType);
    }

    function testAddingSameComplexTypeMoreThanOnceIsIgnored()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $this->wsdl->addType('\ZendTest\Soap\TestAsset\WsdlTestClass', 'tns:SomeTypeName');
        $this->wsdl->addType('\ZendTest\Soap\TestAsset\WsdlTestClass', 'tns:AnotherTypeName');
        $types = $this->wsdl->getTypes();
        $this->assertEquals(1, count($types));
        $this->assertEquals(array('\ZendTest\Soap\TestAsset\WsdlTestClass' => 'tns:SomeTypeName'),
                            $types);
    }

    function testUsingSameComplexTypeTwiceLeadsToReuseOfDefinition()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');
        $this->assertEquals(array('\ZendTest\Soap\TestAsset\WsdlTestClass' =>
                                     'tns:WsdlTestClass'),
                            $this->wsdl->getTypes());

        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');
        $this->assertEquals(array('\ZendTest\Soap\TestAsset\WsdlTestClass' =>
                                     'tns:WsdlTestClass'),
                            $this->wsdl->getTypes());
    }

    function testAddComplexType()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');

        $this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml()),
                            '<?xml version="1.0"?>'  .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
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
    function testCaseOfDocBlockParamsDosNotMatterForSoapTypeDetectionZf3910()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals("xsd:string", $this->wsdl->getType("StrIng"));
        $this->assertEquals("xsd:string", $this->wsdl->getType("sTr"));
        $this->assertEquals("xsd:int", $this->wsdl->getType("iNt"));
        $this->assertEquals("xsd:int", $this->wsdl->getType("INTEGER"));
        $this->assertEquals("xsd:float", $this->wsdl->getType("FLOAT"));
        $this->assertEquals("xsd:double", $this->wsdl->getType("douBLE"));
    }

    /**
     * @group ZF-11937
     */
    public function testWsdlGetTypeWillAllowLongType()
    {
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php');
        $this->assertEquals("xsd:long", $this->wsdl->getType("long"));
    }

    /**
     * @group ZF-5430
     */
    public function testMultipleSequenceDefinitionsOfSameTypeWillBeRecognizedOnceBySequenceStrategy()
    {
        $this->wsdl = new Wsdl("MyService", "http://localhost/MyService.php");
        $this->wsdl->setComplexTypeStrategy(new ComplexTypeStrategy\ArrayOfTypeSequence());

        $this->wsdl->addComplexType("string[]");
        $this->wsdl->addComplexType("int[]");
        $this->wsdl->addComplexType("string[]");

        $xml = $this->wsdl->toXml();
        $this->assertEquals(1, substr_count($xml, "ArrayOfString"), "ArrayOfString should appear only once.");
        $this->assertEquals(1, substr_count($xml, "ArrayOfInt"),    "ArrayOfInt should appear only once.");
    }

    const URI_WITH_EXPANDED_AMP = "http://localhost/MyService.php?a=b&amp;b=c";
    const URI_WITHOUT_EXPANDED_AMP = "http://localhost/MyService.php?a=b&b=c";

    /**
     * @group ZF-5736
     */
    public function testHtmlAmpersandInUrlInConstructorIsEncodedCorrectly()
    {
        $this->wsdl = new Wsdl("MyService", self::URI_WITH_EXPANDED_AMP);
        $this->assertContains(self::URI_WITH_EXPANDED_AMP, $this->wsdl->toXML());
    }

    /**
     * @group ZF-5736
     */
    public function testHtmlAmpersandInUrlInSetUriIsEncodedCorrectly()
    {
        $this->wsdl = new Wsdl("MyService", "http://example.com");
        $this->wsdl->setUri(self::URI_WITH_EXPANDED_AMP);
        $this->assertContains(self::URI_WITH_EXPANDED_AMP, $this->wsdl->toXML());
    }
}
