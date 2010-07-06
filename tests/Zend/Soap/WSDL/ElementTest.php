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
use Zend\Soap\WSDL;

/**
 * @category   Zend;
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_WSDL
 * @group      disable
 */
class ElementTest extends \PHPUnit_Framework_TestCase
{
    public function testBindingElementApi()
    {
        $operations = new WSDL\Element\Collection("test");
        $binding = new WSDL\Element\Binding("name1", "port1", $operations, "test");

        $this->assertEquals("name1", $binding->getName());
        $this->assertEquals("port1", $binding->portName);
        $this->assertEquals($operations, $binding->operations);
        $this->assertEquals("test", $binding->getDocumentation());

        try {
            $binding = new WSDL\Element\Binding(array(), "portName", $operations, "test");
            $this->fail();
        } catch(WSDL\Exception $e) {

        }
    }

    public function testTypeElementApi()
    {
        $types = new WSDL\Element\Collection("test");
        $type = new WSDL\Element\Type("name1", $types, "test");

        $this->assertEquals("name1", $type->getName());
        $this->assertEquals($types, $type->types);
        $this->assertEquals("test", $type->getDocumentation());

        try {
            $type = new WSDL\Element\Type(array(), $types, "test");
            $this->fail();
        } catch(WSDL\Exception $e) {

        }
    }

    public function testMessageElementApi()
    {
        $parts = new WSDL\Element\Collection("test");
        $message = new WSDL\Element\Message("name1", $parts, "test");

        $this->assertEquals("name1", $message->getName());
        $this->assertEquals($parts, $message->parts);
        $this->assertEquals("test", $message->getDocumentation());

        try {
            $message = new WSDL\Element\Message(array(), $parts, "test");
            $this->fail();
        } catch(WSDL\Exception $e) {

        }
    }

    public function testPortElementApi()
    {
        $operations = new WSDL\Element\Collection("test");
        $port = new WSDL\Element\Port("name1", $operations, "test");

        $this->assertEquals("name1", $port->getName());
        $this->assertEquals($operations, $port->operations);
        $this->assertEquals("test", $port->getDocumentation());

        try {
            $port = new WSDL\Element\Port(array(), $operations, "test");
            $this->fail();
        } catch(WSDL\Exception $e) {

        }
    }

    public function testOperationElementApi()
    {
        $collection = new WSDL\Element\Collection("test");
        $input = new WSDL\Element\Message("name", $collection, "test");
        $output = new WSDL\Element\Message("name", $collection, "test");

        $operation = new WSDL\Element\Operation("name1", $input, $output, "test");

        $this->assertEquals("name1",    $operation->getName());
        $this->assertEquals($input,     $operation->inputMessage);
        $this->assertEquals($output,    $operation->outputMessage);
        $this->assertEquals("test", $operation->getDocumentation());

        try {
            $operation = new WSDL\Element\Operation(array(), $input, $output, "test");
            $this->fail();
        } catch(WSDL\Exception $e) {

        }
    }

    public function testServiceElementApi()
    {
        $collection = new WSDL\Element\Collection("test");
        $port = new WSDL\Element\Port("name", $collection, "test");
        $binding = new WSDL\Element\Binding("name", "port", $collection, "test");

        $service = new WSDL\Element\Service("service", "address", $port, $binding, "test");

        $this->assertEquals("service", $service->getName());
        $this->assertEquals("address", $service->soapAddress);
        $this->assertEquals($port,     $service->port);
        $this->assertEquals($binding,  $service->binding);
        $this->assertEquals("test", $service->getDocumentation());

        try {
            $service = new WSDL\Element\Service(array(), "address", $port, $binding, "test");
            $this->fail();
        } catch(WSDL\Exception $e) {

        }

        try {
            $service = new WSDL\Element\Service("name", array(), $port, $binding, "test");
            $this->fail();
        } catch(WSDL\Exception $e) {

        }
    }

    public function testCollectionElementApiConstruct()
    {
        $collection = new WSDL\Element\Collection("Operation");

        $this->assertTrue($collection instanceof \Countable);
        $this->assertTrue($collection instanceof \Iterator);

        try {
            $type = new WSDL\Element\Type("type", new WSDL\Element\Collection("Type"), "test");
            $collection->addElement($type);
            $this->fail();
        } catch(WSDL\Exception $e) {

        }

        try {
            $collection = new WSDL\Element\Collection(false);
            $this->fail();
        } catch(WSDL\Exception $e) {

        }
    }

    public function testCollectionElementApiType()
    {
        $collection = new WSDL\Element\Collection("Operation");
        $this->assertEquals("\Zend\Soap\WSDL\Element\Operation", $collection->getType());

        $collection = new WSDL\Element\Collection("Type");
        $this->assertEquals("\Zend\Soap\WSDL\Element\Type", $collection->getType());

        $collection = new WSDL\Element\Collection("Binding");
        $this->assertEquals("\Zend\Soap\WSDL\Element\Binding", $collection->getType());

        $collection = new WSDL\Element\Collection("Service");
        $this->assertEquals("\Zend\Soap\WSDL\Element\Service", $collection->getType());

        $collection = new WSDL\Element\Collection("Port");
        $this->assertEquals("\Zend\Soap\WSDL\Element\Port", $collection->getType());

        $collection = new WSDL\Element\Collection("Message");
        $this->assertEquals("\Zend\Soap\WSDL\Element\Message", $collection->getType());
    }

    public function testCollectionElementApiElementAccess()
    {
        $collection = new WSDL\Element\Collection("Message");
        $message1 = new WSDL\Element\Message("message1", new WSDL\Element\Collection("Type"), "test");
        $message2 = new WSDL\Element\Message("message2", new WSDL\Element\Collection("Type"), "test");
        $messageDuplicate = new WSDL\Element\Message("message2", new WSDL\Element\Collection("Type"), "test");

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
        } catch(WSDL\Exception $e) {
            $this->assertEquals(array("message1", "message2"), $collection->getElementNames());
            $this->assertEquals($message2, $collection->getElement("message2"));
            $this->assertEquals(2, count($collection));
        }

        try {
            // Accessing unkown message leads to exception
            $collection->getElement("messageUnknown");
            $this->fail("Accessing unknown element should throw an exception.");
        }  catch(WSDL\Exception $e) {
            $this->assertEquals(2, count($collection));
        }

        foreach($collection AS $name => $message) {
            $this->assertTrue($message instanceof WSDL\Element\Message);
            $this->assertTrue( in_array($name, $collection->getElementNames()) );
        }
    }
}
