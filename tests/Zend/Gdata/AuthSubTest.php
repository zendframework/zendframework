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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/AuthSub.php';
require_once 'Zend/Gdata/HttpClient.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_AuthSubTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testNormalGetAuthSubTokenUri()
    {
        $uri = Zend_Gdata_AuthSub::getAuthSubTokenUri(
                'http://www.example.com/foo.php', //next
                'http://www.google.com/calendar/feeds', //scope
                0, //secure 
                1); //session
   
        // Note: the scope here is not encoded.  It should be encoded, 
        // but the method getAuthSubTokenUri calls urldecode($scope).  
        // This currently works (no reported bugs) as web browsers will
        // handle the encoding in most cases.
       $this->assertEquals('https://www.google.com/accounts/AuthSubRequest?next=http%3A%2F%2Fwww.example.com%2Ffoo.php&scope=http://www.google.com/calendar/feeds&secure=0&session=1', $uri);
    }

    public function testGetAuthSubTokenUriModifiedBase()
    {
        $uri = Zend_Gdata_AuthSub::getAuthSubTokenUri(
                'http://www.example.com/foo.php', //next
                'http://www.google.com/calendar/feeds', //scope
                0, //secure 
                1, //session
                'http://www.otherauthservice.com/accounts/AuthSubRequest');
   
        // Note: the scope here is not encoded.  It should be encoded, 
        // but the method getAuthSubTokenUri calls urldecode($scope).  
        // This currently works (no reported bugs) as web browsers will
        // handle the encoding in most cases.
       $this->assertEquals('http://www.otherauthservice.com/accounts/AuthSubRequest?next=http%3A%2F%2Fwww.example.com%2Ffoo.php&scope=http://www.google.com/calendar/feeds&secure=0&session=1', $uri);
    }

    public function testSecureAuthSubSigning()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The openssl extension is not available');
        } else {
            $c = new Zend_Gdata_HttpClient();
            $c->setAuthSubPrivateKeyFile("Zend/Gdata/_files/RsaKey.pem", 
                                         null, true);
            $c->setAuthSubToken('abcdefg');
            $requestData = $c->filterHttpRequest('POST', 
                                                 'http://www.example.com/feed', 
                                                  array(),
                                                  'foo bar', 
                                                  'text/plain');
    
            $authHeaderCheckPassed = false;
            $headers = $requestData['headers'];
            foreach ($headers as $headerName => $headerValue) {
                if (strtolower($headerName) == 'authorization') {
                    preg_match('/data="([^"]*)"/', $headerValue, $matches);
                    $dataToSign = $matches[1];
                    preg_match('/sig="([^"]*)"/', $headerValue, $matches);
                    $sig = $matches[1];
                    if (function_exists('openssl_verify')) {
                        $fp = fopen('Zend/Gdata/_files/RsaCert.pem', 'r', true);
                        $cert = '';
                        while (!feof($fp)) {
                            $cert .= fread($fp, 8192);
                        }
                        fclose($fp);
                        $pubkeyid = openssl_get_publickey($cert);
                        $verified = openssl_verify($dataToSign, 
                                               base64_decode($sig), $pubkeyid);
                        $this->assertEquals(
                            1, $verified, 
                            'The generated signature was unable ' .
                            'to be verified.');
                        $authHeaderCheckPassed = true;
                    }
                }
            }
            $this->assertEquals(true, $authHeaderCheckPassed, 
                                'Auth header not found for sig verification.');
        }
    }
}
