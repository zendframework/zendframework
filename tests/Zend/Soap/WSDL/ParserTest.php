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
namespace ZendTest\Soap\WSDL;
use Zend\Soap\WSDL,
    Zend\Soap\WSDLException;

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_WSDL
 * @group      disable
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    protected function getWSDLExampleDom()
    {
        $dom = new \DOMDocument();
        $dom->loadXml(file_get_contents(__DIR__."/../TestAsset/wsdl_example.wsdl"));
        return $dom;
    }

    public function testFactoryWithDomDocument()
    {
        $dom = $this->getWSDLExampleDom();
        $parser = WSDL\Parser::factory($dom);
        $this->assertTrue($parser instanceof WSDL\Parser);
    }

    public function testFactoryWithString()
    {
        $xmlString = file_get_contents(__DIR__."/../TestAsset/wsdl_example.wsdl");
        $parser = WSDL\Parser::factory($xmlString);
        $this->assertTrue($parser instanceof WSDL\Parser);
    }

    public function testFactoryWithSimpleXml()
    {
        $xmlString = file_get_contents(__DIR__."/../TestAsset/wsdl_example.wsdl");
        $simpleXml = simplexml_load_string($xmlString);
        $parser = WSDL\Parser::factory($simpleXml);
        $this->assertTrue($parser instanceof WSDL\Parser);
    }

    public function testFactoryWithZendSoapWSDL()
    {
        $wsdl = new WSDL("name", "http://example.com");
        $parser = WSDL\Parser::factory($wsdl);
        $this->assertTrue($parser instanceof WSDL\Parser);
    }

    public function testFactoryWithInvalidParser()
    {
        $wsdl = new WSDL("name", "http://example.com");
        try {
            $parser = WSDL\Parser::factory($wsdl, "stdClass");
            $this->fail();
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof WSDL\Parser\Exception);
        }
    }

    public function testFactoryWithInvalidData()
    {
        try {
            $parser = WSDL\Parser::factory(null);
            $this->fail();
        } catch(WSDLException $e) {

        }
    }

    public function testParserApiInterface()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWSDLExampleDom();
        $parser = new WSDL\Parser($dom);

        // SetWSDL is a fluent function
        $this->assertTrue( ($parser->setDomDocumentContainingWSDL($dom)) instanceof WSDL\Parser );

        // Parse returns Result
        $result = $parser->parse();
        $this->assertTrue($result instanceof WSDL\Parser\Result);
    }

    public function testParserResultApiInterface()
    {
        $result = new WSDL\Parser\Result(
            "name",
            WSDL\Parser::WSDL_11,
            new WSDL\Element_Collection("Operation"),
            new WSDL\Element_Collection("Port"),
            new WSDL\Element_Collection("Binding"),
            new WSDL\Element_Collection("Service"),
            new WSDL\Element_Collection("Type"),
            array("docs")
        );

        $this->assertEquals("name",         $result->getName());
        $this->assertEquals("Zend_Soap_WSDL_Element_Operation",    $result->operations->getType());
        $this->assertEquals("Zend_Soap_WSDL_Element_Port",         $result->ports->getType());
        $this->assertEquals("Zend_Soap_WSDL_Element_Binding",      $result->bindings->getType());
        $this->assertEquals("Zend_Soap_WSDL_Element_Service",      $result->services->getType());
        $this->assertEquals("Zend_Soap_WSDL_Element_Type",         $result->types->getType());
        $this->assertEquals(array("docs"),  $result->documentation);

        try {
            $key = $result->invalidKeyThrowsException;
            $this->fail();
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof WSDL\Parser\Exception);
        }
    }

    public function testParseExampleWSDLAndCountResultElements()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWSDLExampleDom();
        $parser = new WSDL\Parser($dom);

        $result = $parser->parse();

        $this->assertEquals("Zend_Soap_Server_TestClass", $result->getName());
        $this->assertEquals(WSDL\Parser::WSDL_11, $result->getVersion());
        $this->assertEquals(4, count($result->operations), "Number of operations does not match.");
        $this->assertEquals(1, count($result->bindings), "Number of bindings does not match.");
        $this->assertEquals(1, count($result->ports), "Number of ports does not match.");
        $this->assertEquals(1, count($result->services), "Number of services does not match.");
        $this->assertEquals(0, count($result->types), "Number of types does not match.");
        $this->assertEquals(4, count($result->bindings->current()->operations), "Number of operations in the bindings collection does not match.");
        $this->assertEquals(4, count($result->ports->current()->operations), "Number of operations in the ports collection does not match.");
    }

    public function testParseExampleWSDLAndCheckMatchingNames()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWSDLExampleDom();
        $parser = new WSDL\Parser($dom);

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

    public function testParseExampleWSDLWithDocumentationBlocks()
    {
        $dom = new \DOMDocument();
        $dom->loadXml(file_get_contents(__DIR__."/../TestAsset/wsdl_documentation.wsdl"));

        $parser = new WSDL\Parser($dom);
        $result = $parser->parse();
    }
}
