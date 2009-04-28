<?php
require_once 'Zend/XmlRpc/Request.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

/**
 * Test case for Zend_XmlRpc_Request
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_XmlRpc_RequestTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Zend_XmlRpc_Request object
     * @var Zend_XmlRpc_Request
     */
    protected $_request;

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->_request = new Zend_XmlRpc_Request();
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        unset($this->_request);
    }

    /**
     * get/setMethod() test
     */
    public function testMethod()
    {
        $this->assertTrue($this->_request->setMethod('testMethod'));
        $this->assertTrue($this->_request->setMethod('testMethod9'));
        $this->assertTrue($this->_request->setMethod('test.Method'));
        $this->assertTrue($this->_request->setMethod('test_method'));
        $this->assertTrue($this->_request->setMethod('test:method'));
        $this->assertTrue($this->_request->setMethod('test/method'));
        $this->assertFalse($this->_request->setMethod('testMethod-bogus'));

        $this->assertEquals('test/method', $this->_request->getMethod());
    }


    /**
     * __construct() test
     */
    public function testConstructorOptionallySetsMethodAndParams()
    {
        $r = new Zend_XmlRpc_Request();
        $this->assertEquals('', $r->getMethod());
        $this->assertEquals(array(), $r->getParams());
        
        $method = 'foo.bar';
        $params = array('baz', 1, array('foo' => 'bar'));
        $r = new Zend_XmlRpc_Request($method, $params);
        $this->assertEquals($method, $r->getMethod());
        $this->assertEquals($params, $r->getParams());
    }
    

    /**
     * addParam()/getParams() test
     */
    public function testAddParam()
    {
        $this->_request->addParam('string1');
        $params = $this->_request->getParams();
        $this->assertEquals(1, count($params));
        $this->assertEquals('string1', $params[0]);

        $this->_request->addParam('string2');
        $params = $this->_request->getParams();
        $this->assertEquals(2, count($params));
        $this->assertEquals('string1', $params[0]);
        $this->assertEquals('string2', $params[1]);
    }

    public function testAddDateParamGeneratesCorrectXml()
    {
        $time = time();
        $this->_request->addParam($time, Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);
        $this->_request->setMethod('foo.bar');
        $xml = $this->_request->saveXML();
        $sxl = new SimpleXMLElement($xml);
        $param = $sxl->params->param->value;
        $type  = 'dateTime.iso8601';
        $this->assertTrue(isset($param->{$type}), var_export($param, 1));
        $this->assertEquals($time, strtotime((string) $param->{$type}));
    }

    /**
     * setParams()/getParams() test
     */
    public function testSetParams()
    {
        $params = array(
            'string1',
            true,
            array('one', 'two')
        );
        $this->_request->setParams($params);
        $returned = $this->_request->getParams();
        $this->assertSame($params, $returned);

        $params = array(
            'string2',
            array('two', 'one')
        );
        $this->_request->setParams($params);
        $returned = $this->_request->getParams();
        $this->assertSame($params, $returned);
    }

    /**
     * loadXml() test
     */
    public function testLoadXml()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $mCall = $dom->appendChild($dom->createElement('methodCall'));
        $mName = $mCall->appendChild($dom->createElement('methodName', 'do.Something'));
        $params = $mCall->appendChild($dom->createElement('params'));
        $param1 = $params->appendChild($dom->createElement('param'));
            $value1 = $param1->appendChild($dom->createElement('value'));
            $value1->appendChild($dom->createElement('string', 'string1'));

        $param2 = $params->appendChild($dom->createElement('param'));
            $value2 = $param2->appendChild($dom->createElement('value'));
            $value2->appendChild($dom->createElement('boolean', 1));


        $xml = $dom->saveXML();

        try {
            $parsed = $this->_request->loadXml($xml);
        } catch (Exception $e) {
            $this->fail('Failed to parse XML: ' . $e->getMessage());
        }
        $this->assertTrue($parsed, $xml);

        $this->assertEquals('do.Something', $this->_request->getMethod());
        $test = array('string1', true);
        $params = $this->_request->getParams();
        $this->assertSame($test, $params);

        try {
            $parsed = $this->_request->loadXml('foo');
        } catch (Exception $e) {
            $this->fail('Failed to parse XML: ' . $e->getMessage());
        }
        $this->assertFalse($parsed, 'Parsed non-XML string?');
    }

    /**
     * isFault() test
     */
    public function testIsFault()
    {
        $this->assertFalse($this->_request->isFault());
        $this->_request->loadXml('foo');
        $this->assertTrue($this->_request->isFault());
    }

    /**
     * getFault() test
     */
    public function testGetFault()
    {
        $fault = $this->_request->getFault();
        $this->assertTrue(null === $fault);
        $this->_request->loadXml('foo');
        $fault = $this->_request->getFault();
        $this->assertTrue($fault instanceof Zend_XmlRpc_Fault);
    }

    /**
     * helper for saveXML() and __toString() tests
     * 
     * @param string $xml 
     * @return void
     */
    protected function _testXmlRequest($xml, $argv)
    {
        try {
            $sx = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $this->fail('Invalid XML returned');
        }

        $result = $sx->xpath('//methodName');
        $count = 0;
        while (list( , $node) = each($result)) {
            ++$count;
        }
        $this->assertEquals(1, $count, $xml);

        $result = $sx->xpath('//params');
        $count = 0;
        while (list( , $node) = each($result)) {
            ++$count;
        }
        $this->assertEquals(1, $count, $xml);

        try {
            $methodName = (string) $sx->methodName;
            $params = array(
                (string) $sx->params->param[0]->value->string,
                (bool) $sx->params->param[1]->value->boolean
            );
        } catch (Exception $e) {
            $this->fail('One or more inconsistencies parsing generated XML: ' . $e->getMessage());
        }

        $this->assertEquals('do.Something', $methodName);
        $this->assertSame($argv, $params, $xml);
    }

    /**
     * testSaveXML() test
     */
    public function testSaveXML()
    {
        $argv = array('string', true);
        $this->_request->setMethod('do.Something');
        $this->_request->setParams($argv);
        $xml = $this->_request->saveXML();
        $this->_testXmlRequest($xml, $argv);
    }

    /**
     * __toString() test
     */
    public function test__toString()
    {
        $argv = array('string', true);
        $this->_request->setMethod('do.Something');
        $this->_request->setParams($argv);
        $xml = $this->_request->__toString();
        $this->_testXmlRequest($xml, $argv);
    }

    /**
     * Test encoding settings
     */
    public function testSetGetEncoding()
    {
        $this->assertEquals('UTF-8', $this->_request->getEncoding());
        $this->_request->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->_request->getEncoding());
    }
}
