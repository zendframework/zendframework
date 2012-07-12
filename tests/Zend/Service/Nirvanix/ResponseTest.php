<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Nirvanix;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Service\Nirvanix\Response;

/**
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Nirvanix
 */
class ResponseTest extends TestCase
{
    // Constructor

    public function testThrowsWhenInputStringIsNotXML()
    {
        $notXml = 'foo';
        $this->setExpectedException('Zend\Service\Nirvanix\Exception\RuntimeException', 'XML could not be parsed');
        $response = new Response($notXml);
    }

    public function testThrowsWhenXmlElementNameIsNotResponse()
    {
        $xml = "<?xml version='1.0'?>
                  <foo></foo>";
        $this->setExpectedException('Zend\Service\Nirvanix\Exception\DomainException', 'Expected XML element Response');
        $response = new Response($xml);
    }

    public function testThrowsCodeAndMessageWhenResponseCodeIsNotZero()
    {
        $xml = "<?xml version='1.0'?>
                  <Response>
                    <ResponseCode>42</ResponseCode>
                    <ErrorMessage>foo</ErrorMessage>
                  </Response>";
        $this->setExpectedException('Zend\Service\Nirvanix\Exception\DomainException', 'foo', 42);
        new Response($xml);
    }

    // getSxml()

    public function testGetSxmlReturnsSimpleXmlElement()
    {
        $xml = "<?xml version='1.0'?>
                  <Response>
                    <ResponseCode>0</ResponseCode>
                    <foo>bar</foo>
                  </Response>";

        $resp = new Response($xml);
        $this->assertInstanceOf('SimpleXMLElement', $resp->getSxml());
    }

    // __get()

    public function testUndefinedPropertyIsDelegatedToSimpleXMLElement()
    {
        $xml = "<?xml version='1.0'?>
                  <Response>
                    <ResponseCode>0</ResponseCode>
                    <foo>bar</foo>
                  </Response>";
        $resp = new Response($xml);
        $this->assertEquals('bar', (string)$resp->foo);
    }

    // __call()

    public function testUndefinedMethodIsDelegatedToSimpleXMLElement()
    {
        $xml = "<?xml version='1.0'?>
                  <Response>
                    <ResponseCode>0</ResponseCode>
                    <foo>bar</foo>
                  </Response>";
        $resp = new Response($xml);
        $this->assertEquals('Response', $resp->getName());
    }

}
