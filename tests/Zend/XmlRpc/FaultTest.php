<?php
require_once 'Zend/XmlRpc/Fault.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

/**
 * Test case for Zend_XmlRpc_Fault
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_XmlRpc_FaultTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Zend_XmlRpc_Fault object
     * @var Zend_XmlRpc_Fault
     */
    protected $_fault;

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->_fault = new Zend_XmlRpc_Fault();
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        unset($this->_fault);
    }

    /**
     * __construct() test
     */
    public function test__construct()
    {
        $this->assertTrue($this->_fault instanceof Zend_XmlRpc_Fault);
        $this->assertEquals(404, $this->_fault->getCode());
        $this->assertEquals('Unknown Error', $this->_fault->getMessage());
    }

    /**
     * get/setCode() test
     */
    public function testCode()
    {
        $this->_fault->setCode('1000');
        $this->assertEquals(1000, $this->_fault->getCode());
    }

    /**
     * get/setMessage() test
     */
    public function testMessage()
    {
        $this->_fault->setMessage('Message');
        $this->assertEquals('Message', $this->_fault->getMessage());
    }

    protected function _createXml()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $response = $dom->appendChild($dom->createElement('methodResponse'));
        $fault  = $response->appendChild($dom->createElement('fault'));
        $value  = $fault->appendChild($dom->createElement('value'));
        $struct = $value->appendChild($dom->createElement('struct'));

        $member1 = $struct->appendChild($dom->createElement('member'));
            $member1->appendChild($dom->createElement('name', 'faultCode'));
            $value1 = $member1->appendChild($dom->createElement('value'));
            $value1->appendChild($dom->createElement('int', 1000));

        $member2 = $struct->appendChild($dom->createElement('member'));
            $member2->appendChild($dom->createElement('name', 'faultString'));
            $value2 = $member2->appendChild($dom->createElement('value'));
            $value2->appendChild($dom->createElement('string', 'Error string'));

        return $dom->saveXML();
    }

    protected function _createNonStandardXml()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $response = $dom->appendChild($dom->createElement('methodResponse'));
        $fault  = $response->appendChild($dom->createElement('fault'));
        $value  = $fault->appendChild($dom->createElement('value'));
        $struct = $value->appendChild($dom->createElement('struct'));

        $member1 = $struct->appendChild($dom->createElement('member'));
            $member1->appendChild($dom->createElement('name', 'faultCode'));
            $value1 = $member1->appendChild($dom->createElement('value'));
            $value1->appendChild($dom->createElement('int', 1000));

        $member2 = $struct->appendChild($dom->createElement('member'));
            $member2->appendChild($dom->createElement('name', 'faultString'));
            $value2 = $member2->appendChild($dom->createElement('value', 'Error string'));

        return $dom->saveXML();
    }

    /**
     * loadXml() test
     */
    public function testLoadXml()
    {
        $xml = $this->_createXml();

        try {
            $parsed = $this->_fault->loadXml($xml);
        } catch (Exception $e) {
            $this->fail('Failed to parse XML: ' . $e->getMessage());
        }
        $this->assertTrue($parsed, $xml);

        $this->assertEquals(1000, $this->_fault->getCode());
        $this->assertEquals('Error string', $this->_fault->getMessage());

        try {
            $parsed = $this->_fault->loadXml('foo');
            $this->fail('Should not parse invalid XML');
        } catch (Exception $e) {
            // do nothing
        }
    }

    /**
     * Zend_XmlRpc_Fault::isFault() test
     */
    public function testIsFault()
    {
        $xml = $this->_createXml();

        $this->assertTrue(Zend_XmlRpc_Fault::isFault($xml), $xml);
        $this->assertFalse(Zend_XmlRpc_Fault::isFault('foo'));
        $this->assertFalse(Zend_XmlRpc_Fault::isFault(array('foo')));
    }

    /**
     * helper for saveXML() and __toString() tests
     * 
     * @param string $xml 
     * @return void
     */
    protected function _testXmlFault($xml)
    {
        try {
            $sx = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $this->fail('Unable to parse generated XML');
        }

        $this->assertTrue($sx->fault ? true : false, $xml);
        $this->assertTrue($sx->fault->value ? true : false, $xml);
        $this->assertTrue($sx->fault->value->struct ? true : false, $xml);
        $count = 0;
        foreach ($sx->fault->value->struct->member as $member) {
            $count++;
            $this->assertTrue($member->name ? true : false, $xml);
            $this->assertTrue($member->value ? true : false, $xml);
            if ('faultCode' == (string) $member->name) {
                $this->assertTrue($member->value->int ? true : false, $xml);
                $this->assertEquals(1000, (int) $member->value->int, $xml);
            }
            if ('faultString' == (string) $member->name) {
                $this->assertTrue($member->value->string ? true : false, $xml);
                $this->assertEquals('Fault message', (string) $member->value->string, $xml);
            }
        }

        $this->assertEquals(2, $count, $xml);
    }

    /**
     * saveXML() test
     */
    public function testSaveXML()
    {
        $this->_fault->setCode(1000);
        $this->_fault->setMessage('Fault message');
        $xml = $this->_fault->saveXML();
        $this->_testXmlFault($xml);
    }

    /**
     * __toString() test
     */
    public function test__toString()
    {
        $this->_fault->setCode(1000);
        $this->_fault->setMessage('Fault message');
        $xml = $this->_fault->__toString();
        $this->_testXmlFault($xml);
    }

    /**
     * Test encoding settings
     */
    public function testSetGetEncoding()
    {
        $this->assertEquals('UTF-8', $this->_fault->getEncoding());
        $this->_fault->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->_fault->getEncoding());
    }

    public function testFaultStringWithoutStringTypeDeclaration()
    {
        $xml = $this->_createNonStandardXml();

        try {
            $parsed = $this->_fault->loadXml($xml);
        } catch (Exception $e) {
            $this->fail('Failed to parse XML: ' . $e->getMessage());
        }
        $this->assertTrue($parsed, $xml);

        $this->assertEquals('Error string', $this->_fault->getMessage());
    }
}
