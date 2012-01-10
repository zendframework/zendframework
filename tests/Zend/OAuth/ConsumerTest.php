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
 */

namespace ZendTest\OAuth;

use Zend\OAuth;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @group      Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ConsumerTest extends \PHPUnit_Framework_TestCase
{

    public function teardown()
    {
        OAuth\OAuth::clearHttpClient();
    }

    public function testConstructorSetsConsumerKey()
    {
        $config = array('consumerKey'=>'1234567890');
        $consumer = new OAuth\Consumer($config);
        $this->assertEquals('1234567890', $consumer->getConsumerKey());
    }

    public function testConstructorSetsConsumerSecret()
    {
        $config = array('consumerSecret'=>'0987654321');
        $consumer = new OAuth\Consumer($config);
        $this->assertEquals('0987654321', $consumer->getConsumerSecret());
    }

    public function testSetsSignatureMethodFromOptionsArray()
    {
        $options = array(
            'signatureMethod' => 'rsa-sha1'
        );
        $consumer = new OAuth\Consumer($options);
        $this->assertEquals('RSA-SHA1', $consumer->getSignatureMethod());
    }

    public function testSetsRequestMethodFromOptionsArray() // add back
    {
        $options = array(
            'requestMethod' => OAuth\OAuth::GET
        );
        $consumer = new OAuth\Consumer($options);
        $this->assertEquals(OAuth\OAuth::GET, $consumer->getRequestMethod());
    }

    public function testSetsRequestSchemeFromOptionsArray()
    {
        $options = array(
            'requestScheme' => OAuth\OAuth::REQUEST_SCHEME_POSTBODY
        );
        $consumer = new OAuth\Consumer($options);
        $this->assertEquals(OAuth\OAuth::REQUEST_SCHEME_POSTBODY, $consumer->getRequestScheme());
    }

    public function testSetsVersionFromOptionsArray()
    {
        $options = array(
            'version' => '1.1'
        );
        $consumer = new OAuth\Consumer($options);
        $this->assertEquals('1.1', $consumer->getVersion());
    }

    public function testSetsCallbackUrlFromOptionsArray()
    {
        $options = array(
            'callbackUrl' => 'http://www.example.com/local'
        );
        $consumer = new OAuth\Consumer($options);
        $this->assertEquals('http://www.example.com/local', $consumer->getCallbackUrl());
    }

    public function testSetsRequestTokenUrlFromOptionsArray()
    {
        $options = array(
            'requestTokenUrl' => 'http://www.example.com/request'
        );
        $consumer = new OAuth\Consumer($options);
        $this->assertEquals('http://www.example.com/request', $consumer->getRequestTokenUrl());
    }

    public function testSetsUserAuthorizationUrlFromOptionsArray()
    {
        $options = array(
            'userAuthorizationUrl' => 'http://www.example.com/authorize'
        );
        $consumer = new OAuth\Consumer($options);
        $this->assertEquals('http://www.example.com/authorize', $consumer->getUserAuthorizationUrl());
    }

    public function testSetsAccessTokenUrlFromOptionsArray()
    {
        $options = array(
            'accessTokenUrl' => 'http://www.example.com/access'
        );
        $consumer = new OAuth\Consumer($options);
        $this->assertEquals('http://www.example.com/access', $consumer->getAccessTokenUrl());
    }

    public function testSetSignatureMethodThrowsExceptionForInvalidMethod()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        try {
            $consumer->setSignatureMethod('buckyball');
            $this->fail('Invalid signature method accepted by setSignatureMethod');
        } catch (OAuth\Exception $e) {
        }
    }

    public function testSetRequestMethodThrowsExceptionForInvalidMethod()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        try {
            $consumer->setRequestMethod('buckyball');
            $this->fail('Invalid request method accepted by setRequestMethod');
        } catch (OAuth\Exception $e) {
        }
    }

    public function testSetRequestSchemeThrowsExceptionForInvalidMethod()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        try {
            $consumer->setRequestScheme('buckyball');
            $this->fail('Invalid request scheme accepted by setRequestScheme');
        } catch (OAuth\Exception $e) {
        }
    }

    public function testSetLocalUrlThrowsExceptionForInvalidUrl()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        try {
            $consumer->setLocalUrl('buckyball');
            $this->fail('Invalid url accepted by setLocalUrl');
        } catch (OAuth\Exception $e) {
        }
    }

    public function testSetRequestTokenUrlThrowsExceptionForInvalidUrl()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        try {
            $consumer->setRequestTokenUrl('buckyball');
            $this->fail('Invalid url accepted by setRequestUrl');
        } catch (OAuth\Exception $e) {
        }
    }

    public function testSetUserAuthorizationUrlThrowsExceptionForInvalidUrl()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        try {
            $consumer->setUserAuthorizationUrl('buckyball');
            $this->fail('Invalid url accepted by setUserAuthorizationUrl');
        } catch (OAuth\Exception $e) {
        }
    }

    public function testSetAccessTokenUrlThrowsExceptionForInvalidUrl()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        try {
            $consumer->setAccessTokenUrl('buckyball');
            $this->fail('Invalid url accepted by setAccessTokenUrl');
        } catch (OAuth\Exception $e) {
        }
    }

    public function testGetRequestTokenReturnsInstanceOfOauthTokenRequest()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        $token = $consumer->getRequestToken(null, null, new RequestToken48231);
        $this->assertInstanceOf('Zend\\OAuth\\Token\\Request', $token);
    }

    public function testGetRedirectUrlReturnsUserAuthorizationUrlWithParameters()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321',
            'userAuthorizationUrl'=>'http://www.example.com/authorize');
        $consumer = new Consumer48231($config);
        $params = array('foo'=>'bar');
        $uauth = new OAuth\Http\UserAuthorization($consumer, $params);
        $token = new OAuth\Token\Request;
        $token->setParams(array('oauth_token'=>'123456', 'oauth_token_secret'=>'654321'));
        $redirectUrl = $consumer->getRedirectUrl($params, $token, $uauth);
        $this->assertEquals(
            'http://www.example.com/authorize?oauth_token=123456&oauth_callback=http%3A%2F%2Fwww.example.com%2Flocal&foo=bar',
            $redirectUrl
        );
    }

    public function testGetAccessTokenReturnsInstanceOfOauthTokenAccess()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new OAuth\Consumer($config);
        $rtoken = new OAuth\Token\Request;
        $rtoken->setToken('token');
        $token = $consumer->getAccessToken(array('oauth_token'=>'token'), $rtoken, null, new AccessToken48231);
        $this->assertInstanceOf('Zend\\OAuth\\Token\\Access', $token);
    }

    public function testGetLastRequestTokenReturnsInstanceWhenExists()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new Consumer48231($config);
        $this->assertInstanceOf('Zend\\OAuth\\Token\\Request', $consumer->getLastRequestToken());
    }

    public function testGetLastAccessTokenReturnsInstanceWhenExists()
    {
        $config = array('consumerKey'=>'12345','consumerSecret'=>'54321');
        $consumer = new Consumer48231($config);
        $this->assertInstanceOf('Zend\\OAuth\\Token\\Access', $consumer->getLastAccessToken());
    }

}

class RequestToken48231 extends OAuth\Http\RequestToken
{
    public function __construct(){}
    public function execute(array $params = null){
        $return = new OAuth\Token\Request;
        return $return;}
    public function setParams(array $customServiceParameters){}
}

class AccessToken48231 extends OAuth\Http\AccessToken
{
    public function __construct(){}
    public function execute(array $params = null){
        $return = new OAuth\Token\Access;
        return $return;}
    public function setParams(array $customServiceParameters){}
}

class Consumer48231 extends OAuth\Consumer
{
    public function __construct(array $options = array()){
        $this->_requestToken = new OAuth\Token\Request;
        $this->_accessToken = new OAuth\Token\Access;
        parent::__construct($options);}
    public function getCallbackUrl(){
        return 'http://www.example.com/local';}
}
