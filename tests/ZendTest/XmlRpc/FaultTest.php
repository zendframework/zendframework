<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace ZendTest\XmlRpc;

use Zend\XmlRpc\AbstractValue;
use Zend\XmlRpc;

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @group      Zend_XmlRpc
 */
class FaultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XmlRpc\Fault
     */
    protected $_fault;

    /**
     * Setup environment
     */
    public function setUp()
    {
        AbstractValue::setGenerator(null);
        $this->_fault = new XmlRpc\Fault();
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
    public function testConstructor()
    {
        $this->assertTrue($this->_fault instanceof XmlRpc\Fault);
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
        $dom = new \DOMDocument('1.0', 'UTF-8');
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

        return $dom->saveXml();
    }

    protected function _createNonStandardXml()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
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

        return $dom->saveXml();
    }

    /**
     * loadXml() test
     */
    public function testLoadXml()
    {
        $xml = $this->_createXml();

        $parsed = $this->_fault->loadXml($xml);
        $this->assertTrue($parsed, $xml);

        $this->assertEquals(1000, $this->_fault->getCode());
        $this->assertEquals('Error string', $this->_fault->getMessage());

        $this->assertFalse($this->_fault->loadXml('<wellformedButInvalid/>'));

        $this->_fault->loadXml('<methodResponse><fault><value><struct>'
                . '<member><name>faultString</name><value><string>str</string></value></member>'
                . '</struct></value></fault></methodResponse>');
        $this->assertSame(404, $this->_fault->getCode(), 'If no fault code is given, use 404 as a default');

        $this->_fault->loadXml('<methodResponse><fault><value><struct>'
                . '<member><name>faultCode</name><value><int>610</int></value></member>'
                . '</struct></value></fault></methodResponse>');
        $this->assertSame(
            'Invalid method class', $this->_fault->getMessage(), 'If empty fault string is given, resolve the code');

        $this->_fault->loadXml('<methodResponse><fault><value><struct>'
                . '<member><name>faultCode</name><value><int>1234</int></value></member>'
                . '</struct></value></fault></methodResponse>');
        $this->assertSame(
            'Unknown Error',
            $this->_fault->getMessage(),
            'If code resolval failed, use "Unknown Error"'
        );
    }

    public function testLoadXmlThrowsExceptionOnInvalidInput()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\InvalidArgumentException', 'Failed to parse XML fault: String could not be parsed as XML');
        $parsed = $this->_fault->loadXml('foo');
    }

    public function testLoadXmlThrowsExceptionOnInvalidInput2()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\InvalidArgumentException', 'Invalid fault structure');
        $this->assertFalse($this->_fault->loadXml('<methodResponse><fault/></methodResponse>'));
    }

    public function testLoadXmlThrowsExceptionOnInvalidInput3()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\InvalidArgumentException', 'Invalid fault structure');
        $this->_fault->loadXml('<methodResponse><fault/></methodResponse>');
    }

    public function testLoadXmlThrowsExceptionOnInvalidInput4()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\InvalidArgumentException', 'Fault code and string required');
        $this->_fault->loadXml('<methodResponse><fault><value><struct/></value></fault></methodResponse>');
    }

    /**
     * Zend_XmlRpc_Fault::isFault() test
     */
    public function testIsFault()
    {
        $xml = $this->_createXml();

        $this->assertTrue(XmlRpc\Fault::isFault($xml), $xml);
        $this->assertFalse(XmlRpc\Fault::isFault('foo'));
        $this->assertFalse(XmlRpc\Fault::isFault(array('foo')));
    }

    /**
     * helper for saveXml() and __toString() tests
     *
     * @param string $xml
     * @return void
     */
    protected function _testXmlFault($xml)
    {
        $sx = new \SimpleXMLElement($xml);

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
     * saveXml() test
     */
    public function testSaveXML()
    {
        $this->_fault->setCode(1000);
        $this->_fault->setMessage('Fault message');
        $xml = $this->_fault->saveXml();
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
        $this->assertEquals('UTF-8', AbstractValue::getGenerator()->getEncoding());
        $this->_fault->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->_fault->getEncoding());
        $this->assertEquals('ISO-8859-1', AbstractValue::getGenerator()->getEncoding());
    }

    public function testUnknownErrorIsUsedIfUnknownErrorCodeEndEmptyMessageIsPassed()
    {
        $fault = new XmlRpc\Fault(1234);
        $this->assertSame(1234, $fault->getCode());
        $this->assertSame('Unknown error', $fault->getMessage());
    }

    public function testFaultStringWithoutStringTypeDeclaration()
    {
        $xml = $this->_createNonStandardXml();

        $parsed = $this->_fault->loadXml($xml);
        $this->assertTrue($parsed, $xml);
        $this->assertEquals('Error string', $this->_fault->getMessage());
    }
}
