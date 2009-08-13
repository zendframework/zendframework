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

require_once dirname(__FILE__)."/../../../TestHelper.php";
require_once "Zend/Soap/Wsdl/Element/Binding.php";
require_once "Zend/Soap/Wsdl/Element/Type.php";
require_once "Zend/Soap/Wsdl/Element/Message.php";
require_once "Zend/Soap/Wsdl/Element/Operation.php";
require_once "Zend/Soap/Wsdl/Element/Port.php";
require_once "Zend/Soap/Wsdl/Element/Service.php";
require_once "Zend/Soap/Wsdl/Element/Collection.php";

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class Zend_Soap_Wsdl_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testBindingElementApi()
    {
        $operations = new Zend_Soap_Wsdl_Element_Collection("test");
        $binding = new Zend_Soap_Wsdl_Element_Binding("name1", "port1", $operations, "test");

        $this->assertEquals("name1", $binding->getName());
        $this->assertEquals("port1", $binding->portName);
        $this->assertEquals($operations, $binding->operations);
        $this->assertEquals("test", $binding->getDocumentation());

        try {
            $binding = new Zend_Soap_Wsdl_Element_Binding(array(), "portName", $operations, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }

    public function testTypeElementApi()
    {
        $types = new Zend_Soap_Wsdl_Element_Collection("test");
        $type = new Zend_Soap_Wsdl_Element_Type("name1", $types, "test");

        $this->assertEquals("name1", $type->getName());
        $this->assertEquals($types, $type->types);
        $this->assertEquals("test", $type->getDocumentation());

        try {
            $type = new Zend_Soap_Wsdl_Element_Type(array(), $types, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testMessageElementApi()
    {
        $parts = new Zend_Soap_Wsdl_Element_Collection("test");
        $message = new Zend_Soap_Wsdl_Element_Message("name1", $parts, "test");

        $this->assertEquals("name1", $message->getName());
        $this->assertEquals($parts, $message->parts);
        $this->assertEquals("test", $message->getDocumentation());

        try {
            $message = new Zend_Soap_Wsdl_Element_Message(array(), $parts, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testPortElementApi()
    {
        $operations = new Zend_Soap_Wsdl_Element_Collection("test");
        $port = new Zend_Soap_Wsdl_Element_Port("name1", $operations, "test");

        $this->assertEquals("name1", $port->getName());
        $this->assertEquals($operations, $port->operations);
        $this->assertEquals("test", $port->getDocumentation());

        try {
            $port = new Zend_Soap_Wsdl_Element_Port(array(), $operations, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testOperationElementApi()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("test");
        $input = new Zend_Soap_Wsdl_Element_Message("name", $collection, "test");
        $output = new Zend_Soap_Wsdl_Element_Message("name", $collection, "test");
        
        $operation = new Zend_Soap_Wsdl_Element_Operation("name1", $input, $output, "test");

        $this->assertEquals("name1",    $operation->getName());
        $this->assertEquals($input,     $operation->inputMessage);
        $this->assertEquals($output,    $operation->outputMessage);
        $this->assertEquals("test", $operation->getDocumentation());

        try {
            $operation = new Zend_Soap_Wsdl_Element_Operation(array(), $input, $output, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testServiceElementApi()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("test");
        $port = new Zend_Soap_Wsdl_Element_Port("name", $collection, "test");
        $binding = new Zend_Soap_Wsdl_Element_Binding("name", "port", $collection, "test");

        $service = new Zend_Soap_Wsdl_Element_Service("service", "address", $port, $binding, "test");

        $this->assertEquals("service", $service->getName());
        $this->assertEquals("address", $service->soapAddress);
        $this->assertEquals($port,     $service->port);
        $this->assertEquals($binding,  $service->binding);
        $this->assertEquals("test", $service->getDocumentation());

        try {
            $service = new Zend_Soap_Wsdl_Element_Service(array(), "address", $port, $binding, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }

        try {
            $service = new Zend_Soap_Wsdl_Element_Service("name", array(), $port, $binding, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testCollectionElementApiConstruct()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("Operation");

        $this->assertTrue($collection instanceof Countable);
        $this->assertTrue($collection instanceof Iterator);

        try {
            $type = new Zend_Soap_Wsdl_Element_Type("type", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");
            $collection->addElement($type);
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }

        try {
            $collection = new Zend_Soap_Wsdl_Element_Collection(false);
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testCollectionElementApiType()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("Operation");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Operation", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Type");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Type", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Binding");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Binding", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Service");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Service", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Port");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Port", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Message");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Message", $collection->getType());
    }

    public function testCollectionElementApiElementAccess()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("Message");
        $message1 = new Zend_Soap_Wsdl_Element_Message("message1", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");
        $message2 = new Zend_Soap_Wsdl_Element_Message("message2", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");
        $messageDuplicate = new Zend_Soap_Wsdl_Element_Message("message2", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");

        $collection->addElement($message1);
        $this->assertEquals(array("message1"), $collection->getElementNames());
        $this->assertEquals($message1, $collection->getElement("message1"));
        $this->assertEquals(1, count($collection));

        $collection->addElement($message2);
        $this->assertEquals(array("message1", "message2"), $collection->getElementNames());
        $this->assertEquals($message2, $collection->getElement("message2"));
        $this->assertEquals(2, count($collection));

        try {
            // Adding duplicate message leads to exception
            $collection->addElement($messageDuplicate);
            $this->fail("Adding a duplicate named element to a collection should throw an exception.");
        } catch(Zend_Soap_Wsdl_Exception $e) {
            $this->assertEquals(array("message1", "message2"), $collection->getElementNames());
            $this->assertEquals($message2, $collection->getElement("message2"));
            $this->assertEquals(2, count($collection));
        }

        try {
            // Accessing unkown message leads to exception
            $collection->getElement("messageUnknown");
            $this->fail("Accessing unknown element should throw an exception.");
        }  catch(Zend_Soap_Wsdl_Exception $e) {
            $this->assertEquals(2, count($collection));
        }

        foreach($collection AS $name => $message) {
            $this->assertTrue($message instanceof Zend_Soap_Wsdl_Element_Message);
            $this->assertTrue( in_array($name, $collection->getElementNames()) );
        }
    }
}
