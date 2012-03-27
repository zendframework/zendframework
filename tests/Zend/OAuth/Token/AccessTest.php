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
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\OAuth\Token;

use Zend\OAuth\Token\Access as AccessToken,
    Zend\Http\Response as HTTPResponse;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @group      Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AccessTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructorSetsResponseObject()
    {
        $response = new HTTPResponse(200, array());
        $token = new AccessToken($response);
        $this->assertInstanceOf('Zend\\Http\\Response', $token->getResponse());
    }

    public function testConstructorParsesRequestTokenFromResponseBody()
    {
        $body   = 'oauth_token=jZaee4GF52O3lUb9&oauth_token_secret=J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri';
        $response = new HTTPResponse;
        $response->setContent($body)
                 ->setStatusCode(200);
        
        $token = new AccessToken($response);
        $this->assertEquals('jZaee4GF52O3lUb9', $token->getToken());
    }

    public function testConstructorParsesRequestTokenSecretFromResponseBody()
    {
        $body = 'oauth_token=jZaee4GF52O3lUb9&oauth_token_secret=J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri';
        $response = new HTTPResponse;
        $response->setContent($body)
                 ->setStatusCode(200);
                 
        $token = new AccessToken($response);
        $this->assertEquals('J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri', $token->getTokenSecret());
    }

    public function testPropertyAccessWorks()
    {
        $body = 'oauth_token=jZaee4GF52O3lUb9&oauth_token_secret=J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri&foo=bar';
        $response = new HTTPResponse;
        $response->setContent($body)
                 ->setStatusCode(200);
                 
        $token = new AccessToken($response);
        $this->assertEquals('J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri', $token->oauth_token_secret);
    }

    public function testTokenCastsToEncodedResponseBody()
    {
        $body = 'oauth_token=jZaee4GF52O3lUb9&oauth_token_secret=J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri';
        $token = new AccessToken();
        $token->setToken('jZaee4GF52O3lUb9');
        $token->setTokenSecret('J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri');
        $this->assertEquals($body, (string) $token);
    }

    public function testToStringReturnsEncodedResponseBody()
    {
        $body = 'oauth_token=jZaee4GF52O3lUb9&oauth_token_secret=J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri';
        $token = new AccessToken();
        $token->setToken('jZaee4GF52O3lUb9');
        $token->setTokenSecret('J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri');
        $this->assertEquals($body, $token->toString());
    }

    public function testIsValidDetectsBadResponse()
    {
        $body = 'oauthtoken=jZaee4GF52O3lUb9&oauthtokensecret=J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri';
        $response = new HTTPResponse;
        $response->setContent($body)
                 ->setStatusCode(200);
                 
        $token = new AccessToken($response);
        $this->assertFalse($token->isValid());
    }

    public function testIsValidDetectsGoodResponse()
    {
        $body = 'oauth_token=jZaee4GF52O3lUb9&oauth_token_secret=J4Ms4n8sxjYc0A8K0KOQFCTL0EwUQTri';
        $response = new HTTPResponse;
        $response->setContent($body)
                 ->setStatusCode(200);
                 
        $token = new AccessToken($response);
        $this->assertTrue($token->isValid());
    }

    public function testToHeaderReturnsValidHeaderString() 
    {
        $token = new AccessToken(null, new HTTPUtility90244);
        $value = $token->toHeader(
            'http://www.example.com',
            new Config90244
        );
        $this->assertEquals('OAuth realm="",oauth_consumer_key="1234567890",oauth_nonce="e807f1fcf82d132f9bb018ca6738a19f",oauth_signature_method="HMAC-SHA1",oauth_timestamp="12345678901",oauth_version="1.0",oauth_token="abcde",oauth_signature="6fb42da0e32e07b61c9f0251fe627a9c"', $value);
    }

}

class HTTPUtility90244 extends \Zend\OAuth\Http\Utility
{
    public function __construct(){}
    public function generateNonce(){return md5('1234567890');}
    public function generateTimestamp(){return '12345678901';}
    public function sign(array $params, $signatureMethod, $consumerSecret,
        $accessTokenSecret = null, $method = null, $url = null)
    {
        return md5('0987654321');
    }
}

class Config90244 extends \Zend\OAuth\Config\StandardConfig
{
    public function getConsumerKey(){return '1234567890';}
    public function getSignatureMethod(){return 'HMAC-SHA1';}
    public function getVersion(){return '1.0';}
    public function getRequestTokenUrl(){return 'http://www.example.com/request';}
    public function getToken(){$token = new AccessToken;
        $token->setToken('abcde');
        return $token;}
    public function getRequestMethod() 
    {return 'POST';}
}
