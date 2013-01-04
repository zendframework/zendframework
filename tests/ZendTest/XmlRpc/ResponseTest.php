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

use Zend\XmlRpc\Response;
use Zend\XmlRpc\AbstractValue;

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @group      Zend_XmlRpc
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_XmlRpc_Response object
     * @var Zend_XmlRpc_Response
     */
    protected $_response;

    /**
     * @var bool
     */
    protected $_errorOccured = false;

    /**
     * Setup environment
     */
    public function setUp()
    {
        $this->_response = new Response();
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        unset($this->_response);
    }

    /**
     * __construct() test
     */
    public function test__construct()
    {
        $this->assertTrue($this->_response instanceof Response);
    }

    /**
     * get/setReturnValue() test
     */
    public function testReturnValue()
    {
        $this->_response->setReturnValue('string');
        $this->assertEquals('string', $this->_response->getReturnValue());

        $this->_response->setReturnValue(array('one', 'two'));
        $this->assertSame(array('one', 'two'), $this->_response->getReturnValue());
    }

    /**
     * isFault() test
     *
     * Call as method call
     *
     * Returns: boolean
     */
    public function testIsFault()
    {
        $this->assertFalse($this->_response->isFault());
        $this->_response->loadXml('foo');
        $this->assertTrue($this->_response->isFault());
    }

    /**
     * Tests getFault() returns NULL (no fault) or the fault object
     */
    public function testGetFault()
    {
        $this->assertNull($this->_response->getFault());
        $this->_response->loadXml('foo');
        $this->assertInstanceOf('Zend\\XmlRpc\\Fault', $this->_response->getFault());
    }

    /**
     * loadXml() test
     *
     * Call as method call
     *
     * Expects:
     * - response:
     *
     * Returns: boolean
     */
    public function testLoadXml()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $response = $dom->appendChild($dom->createElement('methodResponse'));
        $params   = $response->appendChild($dom->createElement('params'));
        $param    = $params->appendChild($dom->createElement('param'));
        $value    = $param->appendChild($dom->createElement('value'));
        $value->appendChild($dom->createElement('string', 'Return value'));

        $xml = $dom->saveXml();

        $parsed = $this->_response->loadXml($xml);
        $this->assertTrue($parsed, $xml);
        $this->assertEquals('Return value', $this->_response->getReturnValue());
    }

    public function testLoadXmlWithInvalidValue()
    {
        $this->assertFalse($this->_response->loadXml(new \stdClass()));
        $this->assertTrue($this->_response->isFault());
        $this->assertSame(650, $this->_response->getFault()->getCode());
    }

    /**
     * @group ZF-9039
     */
    public function testExceptionIsThrownWhenInvalidXmlIsReturnedByServer()
    {
        set_error_handler(array($this, 'trackError'));
        $invalidResponse = 'foo';
        $response = new Response();
        $this->assertFalse($this->_errorOccured);
        $this->assertFalse($response->loadXml($invalidResponse));
        $this->assertFalse($this->_errorOccured);
    }

    /**
     * @group ZF-5404
     */
    public function testNilResponseFromXmlRpcServer()
    {
        $rawResponse = <<<EOD
<methodResponse><params><param><value><array><data><value><struct><member><name>id</name><value><string>1</string></value></member><member><name>name</name><value><string>birdy num num!</string></value></member><member><name>description</name><value><nil/></value></member></struct></value></data></array></value></param></params></methodResponse>
EOD;

        $response = new Response();
        $ret      = $response->loadXml($rawResponse);

        $this->assertTrue($ret);
        $this->assertEquals(array(
            0 => array(
                'id'            => 1,
                'name'          => 'birdy num num!',
                'description'   => null,
            )
        ), $response->getReturnValue());
    }

    /**
     * helper for saveXml() and __toString() tests
     *
     * @param string $xml
     * @return void
     */
    protected function _testXmlResponse($xml)
    {
        $sx = new \SimpleXMLElement($xml);

        $this->assertTrue($sx->params ? true : false);
        $this->assertTrue($sx->params->param ? true : false);
        $this->assertTrue($sx->params->param->value ? true : false);
        $this->assertTrue($sx->params->param->value->string ? true : false);
        $this->assertEquals('return value', (string) $sx->params->param->value->string);
    }

    /**
     * saveXml() test
     */
    public function testSaveXML()
    {
        $this->_response->setReturnValue('return value');
        $xml = $this->_response->saveXml();
        $this->_testXmlResponse($xml);
    }

    /**
     * __toString() test
     */
    public function test__toString()
    {
        $this->_response->setReturnValue('return value');
        $xml = $this->_response->__toString();
        $this->_testXmlResponse($xml);
    }

    /**
     * Test encoding settings
     */
    public function testSetGetEncoding()
    {
        $this->assertEquals('UTF-8', $this->_response->getEncoding());
        $this->assertEquals('UTF-8', AbstractValue::getGenerator()->getEncoding());
        $this->assertSame($this->_response, $this->_response->setEncoding('ISO-8859-1'));
        $this->assertEquals('ISO-8859-1', $this->_response->getEncoding());
        $this->assertEquals('ISO-8859-1', AbstractValue::getGenerator()->getEncoding());
    }

    public function testLoadXmlCreatesFaultWithMissingNodes()
    {
        $sxl = new \SimpleXMLElement('<?xml version="1.0"?><methodResponse><params><param>foo</param></params></methodResponse>');

        $this->assertFalse($this->_response->loadXml($sxl->asXML()));
        $this->assertTrue($this->_response->isFault());
        $fault = $this->_response->getFault();
        $this->assertEquals(653, $fault->getCode());
    }

    public function testLoadXmlCreatesFaultWithMissingNodes2()
    {
        $sxl = new \SimpleXMLElement('<?xml version="1.0"?><methodResponse><params>foo</params></methodResponse>');

        $this->assertFalse($this->_response->loadXml($sxl->asXML()));
        $this->assertTrue($this->_response->isFault());
        $fault = $this->_response->getFault();
        $this->assertEquals(653, $fault->getCode());
    }

    public function testLoadXmlThrowsExceptionWithMissingNodes3()
    {
        $sxl = new \SimpleXMLElement('<?xml version="1.0"?><methodResponse><bar>foo</bar></methodResponse>');

        $this->assertFalse($this->_response->loadXml($sxl->asXML()));
        $this->assertTrue($this->_response->isFault());
        $fault = $this->_response->getFault();
        $this->assertEquals(652, $fault->getCode());
    }


    public function trackError($error)
    {
        $this->_errorOccured = true;
    }

    /**
     * @group ZF-12293
     */
    public function testDoesNotAllowExternalEntities()
    {
        $payload = file_get_contents(dirname(__FILE__) . '/_files/ZF12293-response.xml');
        $payload = sprintf($payload, 'file://' . realpath(dirname(__FILE__) . '/_files/ZF12293-payload.txt'));
        $this->_response->loadXml($payload);
        $value = $this->_response->getReturnValue();
        $this->assertTrue(empty($value));
        if (is_string($value)) {
            $this->assertNotContains('Local file inclusion', $value);
        }
    }

    public function testShouldDisallowsDoctypeInRequestXmlAndReturnFalseOnLoading()
    {
        $payload = file_get_contents(dirname(__FILE__) . '/_files/ZF12293-response.xml');
        $payload = sprintf($payload, 'file://' . realpath(dirname(__FILE__) . '/_files/ZF12293-payload.txt'));
        $this->assertFalse($this->_response->loadXml($payload));
    }
}
