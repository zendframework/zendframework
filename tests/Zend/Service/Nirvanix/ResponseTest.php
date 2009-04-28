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
 * @package    Zend_Service
 * @subpackage Nirvanix
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * @see Zend_Service_Nirvanix_Response
 */
require_once 'Zend/Service/Nirvanix/Response.php';

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Nirvanix_ResponseTest extends PHPUnit_Framework_TestCase
{
    // Constructor
    
    public function testThrowsWhenInputStringIsNotXML()
    {
        $notXml = 'foo';
        try {
            new Zend_Service_Nirvanix_Response($notXml);
        } catch (Zend_Service_Nirvanix_Exception $e) {
            $this->assertRegExp('/xml could not be parsed/i', $e->getMessage());
        }
    }

    public function testThrowsWhenXmlElementNameIsNotResponse()
    {
        $xml = "<?xml version='1.0'?>
                  <foo></foo>";
        try {
            new Zend_Service_Nirvanix_Response($xml);
        } catch (Zend_Service_Nirvanix_Exception $e) {
            $this->assertRegExp('/expected xml element response/i', $e->getMessage());
        }
    }

    public function testThrowsCodeAndMessageWhenResponseCodeIsNotZero()
    {
        $xml = "<?xml version='1.0'?>
                  <Response>
                    <ResponseCode>42</ResponseCode>
                    <ErrorMessage>foo</ErrorMessage>
                  </Response>";
        try {
            new Zend_Service_Nirvanix_Response($xml);
        } catch (Zend_Service_Nirvanix_Exception $e) {
            $this->assertEquals(42, $e->getCode());
            $this->assertEquals('foo', $e->getMessage());
        }        
    }

    // getSxml()

    public function testGetSxmlReturnsSimpleXmlElement()
    {
        $xml = "<?xml version='1.0'?>
                  <Response>
                    <ResponseCode>0</ResponseCode>
                    <foo>bar</foo>
                  </Response>";

        $resp = new Zend_Service_Nirvanix_Response($xml);
        $this->assertType('SimpleXMLElement', $resp->getSxml());
    }

    // __get()

    public function testUndefinedPropertyIsDelegatedToSimpleXMLElement()
    {
        $xml = "<?xml version='1.0'?>
                  <Response>
                    <ResponseCode>0</ResponseCode>
                    <foo>bar</foo>
                  </Response>";
        $resp = new Zend_Service_Nirvanix_Response($xml);
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
        $resp = new Zend_Service_Nirvanix_Response($xml);
        $this->assertEquals('Response', $resp->getName());        
    }
    
}