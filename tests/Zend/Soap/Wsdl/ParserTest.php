<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";
require_once "Zend/Soap/Wsdl.php";
require_once "Zend/Soap/Wsdl/Parser.php";
require_once "Zend/Soap/Wsdl/Parser/Result.php";

class Zend_Soap_Wsdl_ParserTest extends PHPUnit_Framework_TestCase
{
    protected function getWsdlExampleDom()
    {
        $dom = new DOMDocument();
        $dom->loadXml(file_get_contents(dirname(__FILE__)."/../_files/wsdl_example.wsdl"));
        return $dom;
    }

    public function testFactoryWithDomDocument()
    {
        $dom = $this->getWsdlExampleDom();
        $parser = Zend_Soap_Wsdl_Parser::factory($dom);
        $this->assertTrue($parser instanceof Zend_Soap_Wsdl_Parser);
    }

    public function testFactoryWithString()
    {
        $xmlString = file_get_contents(dirname(__FILE__)."/../_files/wsdl_example.wsdl");
        $parser = Zend_Soap_Wsdl_Parser::factory($xmlString);
        $this->assertTrue($parser instanceof Zend_Soap_Wsdl_Parser);
    }

    public function testFactoryWithSimpleXml()
    {
        $xmlString = file_get_contents(dirname(__FILE__)."/../_files/wsdl_example.wsdl");
        $simpleXml = simplexml_load_string($xmlString);
        $parser = Zend_Soap_Wsdl_Parser::factory($simpleXml);
        $this->assertTrue($parser instanceof Zend_Soap_Wsdl_Parser);
    }

    public function testFactoryWithZendSoapWsdl()
    {
        $wsdl = new Zend_Soap_Wsdl("name", "http://example.com");
        $parser = Zend_Soap_Wsdl_Parser::factory($wsdl);
        $this->assertTrue($parser instanceof Zend_Soap_Wsdl_Parser);
    }

    public function testFactoryWithInvalidParser()
    {
        $wsdl = new Zend_Soap_Wsdl("name", "http://example.com");
        try {
            $parser = Zend_Soap_Wsdl_Parser::factory($wsdl, "stdClass");
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_Wsdl_Parser_Exception);
        }
    }

    public function testFactoryWithInvalidData()
    {
        try {
            $parser = Zend_Soap_Wsdl_Parser::factory(null);
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }

    public function testParserApiInterface()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWsdlExampleDom();
        $parser = new Zend_Soap_Wsdl_Parser($dom);

        // SetWsdl is a fluent function
        $this->assertTrue( ($parser->setDomDocumentContainingWsdl($dom)) instanceof Zend_Soap_Wsdl_Parser );

        // Parse returns Result
        $result = $parser->parse();
        $this->assertTrue($result instanceof Zend_Soap_Wsdl_Parser_Result);
    }

    public function testParserResultApiInterface()
    {
        $result = new Zend_Soap_Wsdl_Parser_Result(
            "name",
            Zend_Soap_Wsdl_Parser::WSDL_11,
            new Zend_Soap_Wsdl_Element_Collection("Operation"),
            new Zend_Soap_Wsdl_Element_Collection("Port"),
            new Zend_Soap_Wsdl_Element_Collection("Binding"),
            new Zend_Soap_Wsdl_Element_Collection("Service"),
            new Zend_Soap_Wsdl_Element_Collection("Type"),
            array("docs")
        );

        $this->assertEquals("name",         $result->getName());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Operation",    $result->operations->getType());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Port",         $result->ports->getType());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Binding",      $result->bindings->getType());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Service",      $result->services->getType());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Type",         $result->types->getType());
        $this->assertEquals(array("docs"),  $result->documentation);

        try {
            $key = $result->invalidKeyThrowsException;
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_Wsdl_Parser_Exception);
        }
    }

    public function testParseExampleWsdlAndCountResultElements()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWsdlExampleDom();
        $parser = new Zend_Soap_Wsdl_Parser($dom);

        $result = $parser->parse();

        $this->assertEquals("Zend_Soap_Server_TestClass", $result->getName());
        $this->assertEquals(Zend_Soap_Wsdl_Parser::WSDL_11, $result->getVersion());
        $this->assertEquals(4, count($result->operations), "Number of operations does not match.");
        $this->assertEquals(1, count($result->bindings), "Number of bindings does not match.");
        $this->assertEquals(1, count($result->ports), "Number of ports does not match.");
        $this->assertEquals(1, count($result->services), "Number of services does not match.");
        $this->assertEquals(0, count($result->types), "Number of types does not match.");
        $this->assertEquals(4, count($result->bindings->current()->operations), "Number of operations in the bindings collection does not match.");
        $this->assertEquals(4, count($result->ports->current()->operations), "Number of operations in the ports collection does not match.");
    }

    public function testParseExampleWsdlAndCheckMatchingNames()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWsdlExampleDom();
        $parser = new Zend_Soap_Wsdl_Parser($dom);

        $result = $parser->parse();

        foreach($result->operations AS $operation) {
            $this->assertContains($operation->getName(), array("testFunc1", "testFunc2", "testFunc3", "testFunc4"));
        }
        foreach($result->bindings AS $binding) {
            $this->assertEquals("Zend_Soap_Server_TestClassBinding", $binding->getName());
        }
        foreach($result->ports AS $port) {
            $this->assertEquals("Zend_Soap_Server_TestClassPort", $port->getName());
        }
        foreach($result->services AS $service) {
            $this->assertEquals("Zend_Soap_Server_TestClassService", $service->getName());
        }
    }

    public function testParseExampleWsdlWithDocumentationBlocks()
    {
        $dom = new DOMDocument();
        $dom->loadXml(file_get_contents(dirname(__FILE__)."/../_files/wsdl_documentation.wsdl"));

        $parser = new Zend_Soap_Wsdl_Parser($dom);
        $result = $parser->parse();
    }
}