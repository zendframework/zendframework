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
use Zend\Soap\Wsdl,
    Zend\Soap\Wsdl\ComplexTypeStrategy;

use Zend\Uri\Uri;

/**
 * Zend_Soap_Server
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 **/
class WsdlTest extends WsdlTestHelper
{

    function testConstructor()
    {
        $this->assertEquals(Wsdl::NS_WSDL,              $this->dom->lookupNamespaceUri(null));
        $this->assertEquals(Wsdl::NS_SOAP,              $this->dom->lookupNamespaceUri('soap'));
        $this->assertEquals($this->defaultServiceUri,   $this->dom->lookupNamespaceUri('tns'));
        $this->assertEquals(Wsdl::NS_SOAP,              $this->dom->lookupNamespaceUri('soap'));
        $this->assertEquals(Wsdl::NS_SCHEMA,            $this->dom->lookupNamespaceUri('xsd'));
        $this->assertEquals(Wsdl::NS_S_ENC,             $this->dom->lookupNamespaceUri('soap-enc'));
        $this->assertEquals(Wsdl::NS_WSDL,              $this->dom->lookupNamespaceUri('wsdl'));

        $this->assertEquals(Wsdl::NS_WSDL,              $this->dom->documentElement->namespaceURI);

        $this->assertEquals($this->defaultServiceName,  $this->dom->documentElement->getAttribute('name'));
        $this->assertEquals($this->defaultServiceUri,   $this->dom->documentElement->getAttribute('targetNamespace'));

        $this->testDocumentNodes();
    }

    /**
     * @dataProvider dataProviderForURITesting
     *
     * @param string $uri
     */
    public function testSetUriChangesDomDocumentWsdlStructureTnsAndTargetNamespaceAttributes($uri)
    {
        if ($uri instanceof Uri) {
            $uri = $uri->toString();
        }

        $this->wsdl->setUri($uri);

        $uri = htmlspecialchars($uri, ENT_QUOTES, 'UTF-8', false);
        $this->assertEquals($uri, $this->dom->lookupNamespaceUri('tns'));

        //@todo string retrieved this way contains decoded entities
//        $this->assertEquals($uri, $this->dom->documentElement->getAttribute('targetNamespace'));
        $this->assertContains($uri, $this->wsdl->toXML());

        $this->testDocumentNodes();
    }

    /**
     * @dataProvider dataProviderForURITesting
     *
     * @param string $uri
     */
    public function testSetUriWithZendUriChangesDomDocumentWsdlStructureTnsAndTargetNamespaceAttributes($uri)
    {
        $this->wsdl->setUri(new Uri($uri));

        $uri = htmlspecialchars($uri, ENT_QUOTES, 'UTF-8', false);
        $this->assertEquals($uri, $this->dom->lookupNamespaceUri('tns'));

        //@todo string retrieved this way contains decoded entities
//        $this->assertEquals($uri, $this->dom->documentElement->getAttribute('targetNamespace'));
        $this->assertContains($uri, $this->wsdl->toXML());

        $this->testDocumentNodes();
    }

    /**
     * @dataProvider dataProviderForURITesting
     *
     * @param string $uri
     */
    public function testObjectConstructionWithDifferentURI($uri)
    {
        $wsdl = new Wsdl($this->defaultServiceName, $uri);

        $dom = $this->registerNamespaces($wsdl->toDomDocument(), $uri);

        $uri = htmlspecialchars($uri, ENT_QUOTES, 'UTF-8', false);
        $this->assertEquals($uri, $dom->lookupNamespaceUri('tns'));
        $this->assertEquals($uri, $dom->documentElement->getAttribute('targetNamespace'));

        $this->testDocumentNodes();
    }

    /**
     * Data provider for uri testing
     *
     * @return array
     */
    public function dataProviderForURITesting()
    {
        return array(
            array('http://localhost/MyService.php'),
            array('http://localhost/MyNewService.php'),
            array(new Uri('http://localhost/MyService.php')),
            /**
             * @bug ZF-5736
             */
            array('http://localhost/MyService.php?a=b&amp;b=c'),

            /**
             * @bug ZF-5736
             */
            array('http://localhost/MyService.php?a=b&b=c'),
        );
    }

	/**
	 * @dataProvider dataProviderForAddMessage
	 *
	 * @param array $parameters message parameters
	 */
	function testAddMessage($parameters)
    {
		$messageParts = array();
		foreach($parameters as $i => $parameter) {
			$messageParts['parameter'.$i] = $this->wsdl->getType($parameter);
		}

        $messageName = 'myMessage';

        $this->wsdl->addMessage($messageName, $messageParts);

        $messageNodes = $this->xpath->query('//wsdl:definitions/wsdl:message');

        $this->assertGreaterThan(0, $messageNodes->length, 'Missing message node in definitions node.');

        $this->assertEquals($messageName, $messageNodes->item(0)->getAttribute('name'));

        foreach ($messageParts as $parameterName => $parameterType) {
            $part = $this->xpath->query('wsdl:part[@name="'.$parameterName.'"]', $messageNodes->item(0));
            $this->assertEquals($parameterType, $part->item(0)->getAttribute('type'));
        }

        $this->testDocumentNodes();
    }

    /**
     * @dataProvider dataProviderForAddMessage
     *
     * @param array $parameters complex message parameters
     */
    public function testAddComplexMessage($parameters)
    {
        $messageParts = array();
        foreach($parameters as $i => $parameter) {
            $messageParts['parameter'.$i] = array(
                'type'      => $this->wsdl->getType($parameter),
                'name'      => 'parameter'.$i
            );
        }

        $messageName = 'myMessage';

        $this->wsdl->addMessage($messageName, $messageParts);

        $messageNodes = $this->xpath->query('//wsdl:definitions/wsdl:message');

        $this->assertGreaterThan(0, $messageNodes->length, 'Missing message node in definitions node.');

        foreach ($messageParts as $parameterName => $parameterDefinition) {
            $part = $this->xpath->query('wsdl:part[@name="'.$parameterName.'"]', $messageNodes->item(0));
            $this->assertEquals($parameterDefinition['type'], $part->item(0)->getAttribute('type'));
            $this->assertEquals($parameterDefinition['name'], $part->item(0)->getAttribute('name'));
        }

        $this->testDocumentNodes();
    }

	/**
	 * @return array
	 */
	public function dataProviderForAddMessage()
    {
		return array(
			array(array('int', 'int', 'int')),
			array(array('string', 'string', 'string', 'string')),
			array(array('mixed')),
			array(array('int', 'int', 'string', 'string')),
			array(array('int', 'string', 'int', 'string')),
		);
	}

    function testAddPortType()
    {
        $portName = 'myPortType';
        $this->wsdl->addPortType($portName);

        $portTypeNodes = $this->xpath->query('//wsdl:definitions/wsdl:portType');

        $this->assertGreaterThan(0, $portTypeNodes->length, 'Missing portType node in definitions node.');

        $this->assertTrue($portTypeNodes->item(0)->hasAttribute('name'));
        $this->assertEquals($portName, $portTypeNodes->item(0)->getAttribute('name'));

        $this->testDocumentNodes();
    }

    /**
     * @dataProvider dataProviderForAddPortOperation
     *
     * @param string $operationName
     */
    function testAddPortOperation($operationName, $inputRequest = null, $outputResponse = null, $fail = null)
    {
        $portName = 'myPortType';
        $portType = $this->wsdl->addPortType($portName);

        $this->wsdl->addPortOperation($portType, $operationName, $inputRequest, $outputResponse, $fail);

        $portTypeNodes = $this->xpath->query('//wsdl:definitions/wsdl:portType[@name="'.$portName.'"]');
        $this->assertGreaterThan(0, $portTypeNodes->length, 'Missing portType node in definitions node.');


        $operationNodes = $this->xpath->query('wsdl:operation[@name="'.$operationName.'"]', $portTypeNodes->item(0));
        $this->assertGreaterThan(0, $operationNodes->length);

		if (empty($inputRequest) AND empty($outputResponse) AND empty($fail)) {
            $this->assertFalse($operationNodes->item(0)->hasChildNodes());
        } else {
            $this->assertTrue($operationNodes->item(0)->hasChildNodes());
		}

		if (!empty($inputRequest)) {
            $inputNodes = $operationNodes->item(0)->getElementsByTagName('input');
            $this->assertEquals($inputRequest, $inputNodes->item(0)->getAttribute('message'));
        }

		if (!empty($outputResponse)) {
			$outputNodes = $operationNodes->item(0)->getElementsByTagName('output');
    	    $this->assertEquals($outputResponse, $outputNodes->item(0)->getAttribute('message'));
		}

		if (!empty($fail)) {
        	$faultNodes = $operationNodes->item(0)->getElementsByTagName('fault');
        	$this->assertEquals($fail, $faultNodes->item(0)->getAttribute('message'));
		}

        $this->testDocumentNodes();
    }

    /**
     *
     */
    function dataProviderForAddPortOperation()
    {
        return array(
            array('operation'),
            array('operation', 'tns:operationRequest', 'tns:operationResponse'),
            array('operation', 'tns:operationRequest', 'tns:operationResponse', 'tns:operationFault'),
            array('operation', 'tns:operationRequest', null, 'tns:operationFault'),
            array('operation', null, null, 'tns:operationFault'),
            array('operation', null, 'tns:operationResponse', 'tns:operationFault'),
			array('operation', null, 'tns:operationResponse'),
		);
    }

    function testAddBinding()
    {
        $this->wsdl->addBinding('MyServiceBinding', 'myPortType');
        $this->wsdl->toDomDocument()->formatOutput = true;

        $bindingNodes = $this->xpath->query('//wsdl:definitions/wsdl:binding');

        if ($bindingNodes->length === 0) {
            $this->fail('Missing binding node in definitions node.'.$bindingNodes->length);
        }

        $this->assertEquals('MyServiceBinding',     $bindingNodes->item(0)->getAttribute('name'));
        $this->assertEquals('myPortType',           $bindingNodes->item(0)->getAttribute('type'));

        $this->testDocumentNodes();
    }

	/**
	 * @dataProvider dataProviderForAddBindingOperation
	 *
	 * @param $operationName
	 * @param null $input
	 * @param null $inputEncoding
	 * @param null $output
	 * @param null $outputEncoding
	 * @param null $fault
	 * @param null $faultEncoding
	 * @param null $faultName
	 */
	function testAddBindingOperation($operationName,
		$input = null, $inputEncoding = null,
		$output = null, $outputEncoding = null,
		$fault = null, $faultEncoding = null, $faultName = null)
    {
        $binding = $this->wsdl->addBinding('MyServiceBinding', 'myPortType');

		$inputArray = array();
		if (!empty($input) AND !empty($inputEncoding)) {
			$inputArray = array('use' => $input, 	'encodingStyle' => $inputEncoding);
		}

		$outputArray = array();
		if (!empty($output) AND !empty($outputEncoding)) {
			$outputArray = array('use' => $output, 'encodingStyle' => $outputEncoding);
		}

		$faultArray = array();
		if (!empty($fault) AND !empty($faultEncoding) AND !empty($faultName)) {
			$faultArray = array('use' => $fault, 	'encodingStyle' => $faultEncoding, 	'name'=>$faultName);
		}

		$this->wsdl->addBindingOperation($binding,
			$operationName,
			$inputArray,
			$outputArray,
			$faultArray
		);

        $bindingNodes = $this->xpath->query('//wsdl:binding');

		$this->assertGreaterThan(0, $bindingNodes->length, 'Missing binding node in definition.');

        $this->assertEquals('MyServiceBinding',     $bindingNodes->item(0)->getAttribute('name'));
        $this->assertEquals('myPortType',           $bindingNodes->item(0)->getAttribute('type'));

		$operationNodes = $this->xpath->query('wsdl:operation[@name="'.$operationName.'"]', $bindingNodes->item(0));
		$this->assertEquals(1, $operationNodes->length, 'Missing operation node in definition.');

		if (empty($inputArray) AND empty($outputArray) AND empty($faultArray)) {
			$this->assertFalse($operationNodes->item(0)->hasChildNodes());
		}

        foreach (array(
            '//wsdl:input/soap:body'    => $inputArray,
            '//wsdl:output/soap:body'   => $outputArray,
            '//wsdl:fault'              => $faultArray
                 ) as $query => $ar) {

            if (!empty($ar)) {
                $nodes = $this->xpath->query($query);

                $this->assertGreaterThan(0, $nodes->length, 'Missing operation body.');

                foreach ($ar as $key => $val) {
                    $this->assertEquals($ar[$key], $nodes->item(0)->getAttribute($key),
                        'Bad attribute in operation definition: '.$key);
                }
            }
        }

        $this->testDocumentNodes();
    }

	/**
	 *
	 */
	public function dataProviderForAddBindingOperation()
    {

		$enc = 'http://schemas.xmlsoap.org/soap/encoding/';

		return array(
			array('operation'),
			array('operation', 'encoded', $enc, 'encoded', $enc, 'encoded', $enc, 'myFaultName'),
			array('operation', null, null, 'encoded', $enc, 'encoded', $enc, 'myFaultName'),
			array('operation', null, null, 'encoded', $enc, 'encoded'),
			array('operation', 'encoded', $enc),
			array('operation', null, null, null, null, 'encoded', $enc, 'myFaultName'),
			array('operation', 'encoded1', $enc.'1', 'encoded2', $enc.'2', 'encoded3', $enc.'3', 'myFaultName'),

		);
	}

	/**
	 * @dataProvider dataProviderForSoapBindingStyle
	 */
	function testAddSoapBinding($style)
    {
        $this->wsdl->addPortType('myPortType');
        $binding = $this->wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->wsdl->addSoapBinding($binding, $style);


		$nodes = $this->xpath->query('//soap:binding');

		$this->assertGreaterThan(0, $nodes->length);
		$this->assertEquals($style, $nodes->item(0)->getAttribute('style'));

        $this->testDocumentNodes();
    }

	public function dataProviderForSoapBindingStyle()
    {
		return array(
			array('document'),
			array('rpc'),
		);
	}

    /**
     * @dataProvider dataProviderForAddSoapOperation
     */
    function testAddSoapOperation($operationUrl)
    {
        $this->wsdl->addPortType('myPortType');
        $binding = $this->wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->wsdl->addSoapOperation($binding, $operationUrl);

        $node = $this->xpath->query('//soap:operation');
        $this->assertGreaterThan(0, $node->length);
        $this->assertEquals($operationUrl, $node->item(0)->getAttribute('soapAction'));

        $this->testDocumentNodes();
    }

    public function dataProviderForAddSoapOperation()
    {
        return array(
            array('http://localhost/MyService.php#myOperation'),
            array(new Uri('http://localhost/MyService.php#myOperation'))
        );
    }

    /**
     * @dataProvider dataProviderForAddService
     */
    function testAddService($serviceUrl)
    {
        $this->wsdl->addPortType('myPortType');
        $this->wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->wsdl->addService('Service1', 'myPortType', 'MyServiceBinding', $serviceUrl);

        $nodes = $this->xpath->query('//wsdl:service[@name="Service1"]/wsdl:port/soap:address');
        $this->assertGreaterThan(0, $nodes->length);

        $this->assertEquals($serviceUrl, $nodes->item(0)->getAttribute('location'));

        $this->testDocumentNodes();
    }

    public function dataProviderForAddService()
    {
        return array(
            array('http://localhost/MyService.php'),
            array(new Uri('http://localhost/MyService.php'))
        );
    }

    function testAddDocumentation()
    {
        $doc = 'This is a description for Port Type node.';
        $this->wsdl->addDocumentation($this->wsdl, $doc);

        $nodes = $this->wsdl->toDomDocument()->childNodes;
        $this->assertEquals(1, $nodes->length);
        $this->assertEquals($doc, $nodes->item(0)->nodeValue);

        $this->testDocumentNodes();
    }

    function testAddDocumentationToSomeElmenet()
    {
        $portType = $this->wsdl->addPortType('myPortType');

        $doc = 'This is a description for Port Type node.';
        $this->wsdl->addDocumentation($portType, $doc);

        $nodes = $this->xpath->query('//wsdl:portType[@name="myPortType"]/wsdl:documentation');
        $this->assertEquals(1, $nodes->length);
        $this->assertEquals($doc, $nodes->item(0)->nodeValue);

        $this->testDocumentNodes();
    }

    public function testAddDocumentationToSetInsertsBefore()
    {
        $messageParts = array();
        $messageParts['parameter1'] = $this->wsdl->getType('int');
        $messageParts['parameter2'] = $this->wsdl->getType('string');
        $messageParts['parameter3'] = $this->wsdl->getType('mixed');

        $message = $this->wsdl->addMessage('myMessage', $messageParts);
        $this->wsdl->addDocumentation($message, "foo");

        $nodes = $this->xpath->query('//wsdl:message[@name="myMessage"]/*[1]');
        $this->assertEquals('documentation', $nodes->item(0)->nodeName);

        $this->testDocumentNodes();
    }

    public function testDumpToFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'zfunittest');

        $dumpStatus = $this->wsdl->dump($file);

        $fileContent = file_get_contents($file);
        unlink($file);

        $this->assertTrue($dumpStatus, 'WSDL Dump fail');

        $this->checkXMLContent($fileContent);
    }

    public function testDumpToOutput()
    {

        ob_start();
        $dumpStatus = $this->wsdl->dump();
        $screenContent = ob_get_clean();

        $this->assertTrue($dumpStatus, 'Dump to output failed');

        $this->checkXMLContent($screenContent);
    }

    public function checkXMLContent($content)
    {
        libxml_use_internal_errors(true);
        libxml_disable_entity_loader(false);
        $xml = new \DOMDocument();
        $xml->loadXML($content);

        $errors = libxml_get_errors();
        $this->assertEmpty($errors, 'Libxml parsing errors: '.print_r($errors, 1));

        $this->dom = $this->registerNamespaces($xml);

        $this->testConstructor();

        $this->testDocumentNodes();
    }

    public function testGetType()
    {
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
        $this->assertEquals('tns:WsdlTestClass', $this->wsdl->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof ComplexTypeStrategy\DefaultComplexType);

        $this->testDocumentNodes();
    }

    function testGetComplexTypeBasedOnStrategiesStringNames()
    {
        $this->wsdl = new Wsdl($this->defaultServiceName, 'http://localhost/MyService.php', new \Zend\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType);
        $this->assertEquals('tns:WsdlTestClass', $this->wsdl->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof ComplexTypeStrategy\DefaultComplexType);

        $wsdl2 = new Wsdl($this->defaultServiceName, $this->defaultServiceUri, new \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType);
        $this->assertEquals('xsd:anyType', $wsdl2->getType('\ZendTest\Soap\TestAsset\WsdlTestClass'));
        $this->assertTrue($wsdl2->getComplexTypeStrategy() instanceof ComplexTypeStrategy\AnyType);

        $this->testDocumentNodes();
    }

    function testAddingSameComplexTypeMoreThanOnceIsIgnored()
    {
        $this->wsdl->addType('\ZendTest\Soap\TestAsset\WsdlTestClass', 'tns:SomeTypeName');
        $this->wsdl->addType('\ZendTest\Soap\TestAsset\WsdlTestClass', 'tns:AnotherTypeName');
        $types = $this->wsdl->getTypes();
        $this->assertEquals(1, count($types));
        $this->assertEquals(
            array(
                '\ZendTest\Soap\TestAsset\WsdlTestClass' => 'tns:SomeTypeName'
            ),
            $types
        );

        $this->testDocumentNodes();
    }

    function testUsingSameComplexTypeTwiceLeadsToReuseOfDefinition()
    {
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');
        $this->assertEquals(
            array(
                '\ZendTest\Soap\TestAsset\WsdlTestClass' => 'tns:WsdlTestClass'
            ),
            $this->wsdl->getTypes()
        );

        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');
        $this->assertEquals(
            array(
                '\ZendTest\Soap\TestAsset\WsdlTestClass' => 'tns:WsdlTestClass'
            ),
            $this->wsdl->getTypes()
        );

        $this->testDocumentNodes();
    }

    public function testGetSchema()
    {
        $schema = $this->wsdl->getSchema();

        $this->assertEquals($this->defaultServiceUri, $schema->getAttribute('targetNamespace'));
    }

    function testAddComplexType()
    {
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');

        $nodes = $this->xpath->query('//wsdl:types/xsd:schema/xsd:complexType/xsd:all/*');

        $this->assertGreaterThan(0, $nodes->length, 'Unable to find object properties in wsdl');

        $this->testDocumentNodes();
//        print $nodes->length;
//        print_r($nodes->item(0)->firstChild);
//        print_r($this->wsdl->toXML());
//
//exit;
//        $this->markTestIncomplete();

        /*$this->assertEquals($this->sanitizeWsdlXmlOutputForOsCompability($this->wsdl->toXml()),
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
                          . '</definitions>' );*/
    }

    public function testAddTypesFromDocument()
    {
        $dom = new \DOMDocument();
        $types = $dom->createElementNS(WSDL::NS_WSDL, 'types');
        $dom->appendChild($types);

        $this->wsdl->addTypes($dom);

        $nodes = $this->xpath->query('//wsdl:types');
        $this->assertGreaterThanOrEqual(1, $nodes->length);

        $this->testDocumentNodes();
    }

    public function testAddTypesFromNode()
    {
        $dom = $this->dom->createElementNS(WSDL::NS_WSDL, 'types');

        $this->wsdl->addTypes($dom);

        $nodes = $this->xpath->query('//wsdl:types');
        $this->assertGreaterThanOrEqual(1, $nodes->length);

        $this->testDocumentNodes();
    }

    public function testTranslateTypeFromClassMap()
    {
        $this->wsdl->setClassMap(array(
            'SomeType'=>'SomeOtherType'
        ));

        $this->assertEquals('SomeOtherType', $this->wsdl->translateType('SomeType'));
    }

    /**
     * @dataProvider dataProviderForTranslateType
     */
    public function testTranslateType($type, $expected) {
        $this->assertEquals($expected, $this->wsdl->translateType($type));
    }

    public function dataProviderForTranslateType() {
        return array(
            array('\\SomeType','SomeType'),
            array('SomeType\\','SomeType'),
            array('\\SomeType\\','SomeType'),
            array('\\SomeNamespace\SomeType\\','SomeType'),
        );
    }


    /**
     * @group ZF-3910
     */
    function testCaseOfDocBlockParamsDosNotMatterForSoapTypeDetectionZf3910()
    {
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
        $this->assertEquals("xsd:long", $this->wsdl->getType("long"));
    }

    /**
     * @group ZF-5430
     */
    public function testMultipleSequenceDefinitionsOfSameTypeWillBeRecognizedOnceBySequenceStrategy()
    {
        $this->wsdl->setComplexTypeStrategy(new ComplexTypeStrategy\ArrayOfTypeSequence());

        $this->wsdl->addComplexType("string[]");
        $this->wsdl->addComplexType("int[]");
        $this->wsdl->addComplexType("string[]");

        $xml = $this->wsdl->toXml();
        $this->assertEquals(1, substr_count($xml, "ArrayOfString"), "ArrayOfString should appear only once.");
        $this->assertEquals(1, substr_count($xml, "ArrayOfInt"),    "ArrayOfInt should appear only once.");

        $this->testDocumentNodes();
    }

    public function testClassMap()
    {
        $this->wsdl->setClassMap(array('foo'=>'bar'));

        $this->assertArrayHasKey('foo', $this->wsdl->getClassMap());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testAddElementException ()
    {
        $this->wsdl->addElement(1);
    }

    public function testAddElement()
    {
        $element = array(
            'name'      => 'MyElement',
            'sequence'  => array(
                array('name' => 'myString', 'type' => 'string'),
                array('name' => 'myInt',    'type' => 'int')
            )
        );

        $newElementName = $this->wsdl->addElement($element);

        $this->assertEquals('tns:'.$element['name'], $newElementName);

        $nodes = $this->xpath->query('//wsdl:types/xsd:schema/xsd:element[@name="'.$element['name'].'"]/xsd:complexType');

        $this->assertEquals(1, $nodes->length);

        $this->assertEquals('sequence', $nodes->item(0)->firstChild->localName);

        $n = 0;
        foreach($element['sequence'] as $elementDefinition) {
            $n++;
            $elementNode = $this->xpath->query('xsd:element[@name="'.$elementDefinition['name'].'"]', $nodes->item(0)->firstChild);
            $this->assertEquals($elementDefinition['type'], $elementNode->item(0)->getAttribute('type'));
        }

        $this->assertEquals(count($element['sequence']), $n);

        $this->testDocumentNodes();
    }


}
