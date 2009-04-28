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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Service_Akismet
 */
require_once 'Zend/Service/Akismet.php';

/**
 * @see Zend_Http_Client_Adapter_Test
 */
require_once 'Zend/Http/Client/Adapter/Test.php';


/**
 * @package     Zend_Service
 * @subpackage  UnitTests
 */
class Zend_Service_AkismetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->akismet = new Zend_Service_Akismet('somebogusapikey', 'http://framework.zend.com/wiki/');
        $adapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Zend_Service_Akismet::setHttpClient($client);

        $this->comment = array(
            'user_ip'         => '71.161.221.76',
            'user_agent'      => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1)',
            'comment_type'    => 'comment',
            'comment_content' => 'spam check'
        );
    }

    public function testBlogUrl()
    {
        $this->assertEquals('http://framework.zend.com/wiki/', $this->akismet->getBlogUrl());
        $this->akismet->setBlogUrl('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $this->akismet->getBlogUrl());
    }

    public function testApiKey()
    {
        $this->assertEquals('somebogusapikey', $this->akismet->getApiKey());
        $this->akismet->setApiKey('invalidapikey');
        $this->assertEquals('invalidapikey', $this->akismet->getApiKey());
    }

    public function testCharset()
    {
        $this->assertEquals('UTF-8', $this->akismet->getCharset());
        $this->akismet->setCharset('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->akismet->getCharset());
    }

    public function testPort()
    {
        $this->assertEquals(80, $this->akismet->getPort());
        $this->akismet->setPort(8080);
        $this->assertEquals(8080, $this->akismet->getPort());
    }

    public function testUserAgent()
    {
        $this->akismet->setUserAgent('MyUserAgent/1.0 | Akismet/1.11');
        $this->assertEquals('MyUserAgent/1.0 | Akismet/1.11', $this->akismet->getUserAgent());
    }
    
    public function testUserAgentDefaultMatchesFrameworkVersion()
    {
        $this->assertContains('Zend Framework/' . Zend_Version::VERSION, $this->akismet->getUserAgent());
    }

    public function testVerifyKey()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "Content-length: 5\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:41:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "valid";
        $this->adapter->setResponse($response);
        $this->assertTrue($this->akismet->verifyKey());

        $response = "HTTP/1.0 200 OK\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "Content-length: 7\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:41:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "invalid";
        $this->adapter->setResponse($response);
        $this->assertFalse($this->akismet->verifyKey());
    }

    public function testIsSpamThrowsExceptionOnInvalidKey()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "X-akismet-server: 72.21.44.242\r\n"
                  . "Content-length: 7\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:50:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "invalid";
        $this->adapter->setResponse($response);
        try {
            $this->akismet->isSpam($this->comment);
            $this->fail('Response of "invalid" should trigger exception');
        } catch (Exception $e) {
            // success
        }
    }

    public function testIsSpam()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "X-akismet-server: 72.21.44.242\r\n"
                  . "Content-length: 4\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:50:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "true";
        $this->adapter->setResponse($response);
        $this->assertTrue($this->akismet->isSpam($this->comment));

        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "X-akismet-server: 72.21.44.242\r\n"
                  . "Content-length: 5\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:50:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "false";
        $this->adapter->setResponse($response);
        $this->assertFalse($this->akismet->isSpam($this->comment));
    }

    public function testSubmitSpamThrowsExceptionOnInvalidKey()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "X-akismet-server: 72.21.44.242\r\n"
                  . "Content-length: 7\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:50:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "invalid";
        $this->adapter->setResponse($response);
        try {
            $this->akismet->submitSpam($this->comment);
            $this->fail('Response of "invalid" should trigger exception');
        } catch (Exception $e) {
            // success
        }
    }

    public function testSubmitSpam()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/html\r\n"
                  . "Content-length: 41\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "Thanks for making the web a better place.";
        $this->adapter->setResponse($response);
        try {
            $this->akismet->submitSpam($this->comment);
        } catch (Exception $e) {
            $this->fail('Valid key should not throw exceptions');
        }
    }

    public function testSubmitHam()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/html\r\n"
                  . "Content-length: 41\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "Thanks for making the web a better place.";
        $this->adapter->setResponse($response);
        try {
            $this->akismet->submitHam($this->comment);
        } catch (Exception $e) {
            $this->fail('Valid key should not throw exceptions');
        }
    }
}
