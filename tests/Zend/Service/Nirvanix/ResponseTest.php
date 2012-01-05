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
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Nirvanix;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Service\Nirvanix\Response;

/**
 * @see        Zend\Service\Nirvanix\Response
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
