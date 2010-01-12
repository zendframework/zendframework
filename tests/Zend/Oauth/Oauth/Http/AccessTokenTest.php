<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Oauth/Http/AccessToken.php';

class Zend_Oauth_Http_AccessTokenTest extends PHPUnit_Framework_TestCase
{

    protected $stubConsumer = null;

    public function setup()
    {
        $this->stubConsumer = new Test_Consumer_39745;
        $this->stubHttpUtility = new Test_Http_Utility_39745;
        Zend_Oauth::setHttpClient(new Test_Client_39745);
    }

    public function teardown()
    {
        Zend_Oauth::clearHttpClient();
    }

    public function testConstructorSetsConsumerInstance()
    {
        $request = new Zend_Oauth_Http_AccessToken($this->stubConsumer, null, $this->stubHttpUtility);
        $this->assertType('Test_Consumer_39745', $request->getConsumer());
    }

    public function testConstructorSetsCustomServiceParameters()
    {
        $request = new Zend_Oauth_Http_AccessToken($this->stubConsumer, array(1,2,3), $this->stubHttpUtility);
        $this->assertEquals(array(1,2,3), $request->getParameters());
    }

    public function testAssembleParametersCorrectlyAggregatesOauthParameters()
    {
        $request = new Zend_Oauth_Http_AccessToken($this->stubConsumer, null, $this->stubHttpUtility);
        $expectedParams = array (
            'oauth_consumer_key' => '1234567890',
            'oauth_nonce' => 'e807f1fcf82d132f9bb018ca6738a19f',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '12345678901',
            'oauth_token' => '0987654321',
            'oauth_version' => '1.0',
            'oauth_signature' => '6fb42da0e32e07b61c9f0251fe627a9c'
        );
        $this->assertEquals($expectedParams, $request->assembleParams());
    }
    public function testAssembleParametersCorrectlyIgnoresCustomParameters()
    {
        $request = new Zend_Oauth_Http_AccessToken($this->stubConsumer, array(
            'custom_param1'=>'foo',
            'custom_param2'=>'bar'
        ), $this->stubHttpUtility);
        $expectedParams = array (
            'oauth_consumer_key' => '1234567890',
            'oauth_nonce' => 'e807f1fcf82d132f9bb018ca6738a19f',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '12345678901',
            'oauth_token' => '0987654321',
            'oauth_version' => '1.0',
            'oauth_signature' => '6fb42da0e32e07b61c9f0251fe627a9c'
        );
        $this->assertEquals($expectedParams, $request->assembleParams());
    }

    public function testGetRequestSchemeHeaderClientSetsCorrectlyEncodedAuthorizationHeader()
    {
        $request = new Zend_Oauth_Http_AccessToken($this->stubConsumer, null, $this->stubHttpUtility);
        $params = array (
            'oauth_consumer_key' => '1234567890',
            'oauth_nonce' => 'e807f1fcf82d132f9bb018ca6738a19f',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '12345678901',
            'oauth_token' => '0987654321',
            'oauth_version' => '1.0',
            'oauth_signature' => '6fb42da0e32e07b61c9f0251fe627a9c~',
            'custom_param1' => 'foo',
            'custom_param2' => 'bar'
        );
        $client = $request->getRequestSchemeHeaderClient($params);
        $this->assertEquals(
        'OAuth realm="",oauth_consumer_key="1234567890",oauth_nonce="e807f1fcf82d132f9b'
        .'b018ca6738a19f",oauth_signature_method="HMAC-SHA1",oauth_timestamp="'
        .'12345678901",oauth_token="0987654321",oauth_version="1.0",oauth_sign'
        .'ature="6fb42da0e32e07b61c9f0251fe627a9c~"',
            $client->getHeader('Authorization')
        );
    }

    public function testGetRequestSchemePostBodyClientSetsCorrectlyEncodedRawData()
    {
        $request = new Zend_Oauth_Http_AccessToken($this->stubConsumer, null, $this->stubHttpUtility);
        $params = array (
            'oauth_consumer_key' => '1234567890',
            'oauth_nonce' => 'e807f1fcf82d132f9bb018ca6738a19f',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '12345678901',
            'oauth_token' => '0987654321',
            'oauth_version' => '1.0',
            'oauth_signature' => '6fb42da0e32e07b61c9f0251fe627a9c~',
            'custom_param1' => 'foo',
            'custom_param2' => 'bar'
        );
        $client = $request->getRequestSchemePostBodyClient($params);
        $this->assertEquals(
            'oauth_consumer_key=1234567890&oauth_nonce=e807f1fcf82d132f9bb018c'
            .'a6738a19f&oauth_signature_method=HMAC-SHA1&oauth_timestamp=12345'
            .'678901&oauth_token=0987654321&oauth_version=1.0&oauth_signature='
            .'6fb42da0e32e07b61c9f0251fe627a9c~',
            $client->getRawData()
        );
    }

    public function testGetRequestSchemeQueryStringClientSetsCorrectlyEncodedQueryString()
    {
        $request = new Zend_Oauth_Http_AccessToken($this->stubConsumer, null, $this->stubHttpUtility);
        $params = array (
            'oauth_consumer_key' => '1234567890',
            'oauth_nonce' => 'e807f1fcf82d132f9bb018ca6738a19f',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '12345678901',
            'oauth_token' => '0987654321',
            'oauth_version' => '1.0',
            'oauth_signature' => '6fb42da0e32e07b61c9f0251fe627a9c',
            'custom_param1' => 'foo',
            'custom_param2' => 'bar'
        );
        $client = $request->getRequestSchemeQueryStringClient($params, 'http://www.example.com');
        $this->assertEquals(
            'oauth_consumer_key=1234567890&oauth_nonce=e807f1fcf82d132f9bb018c'
            .'a6738a19f&oauth_signature_method=HMAC-SHA1&oauth_timestamp=12345'
            .'678901&oauth_token=0987654321&oauth_version=1.0&oauth_signature='
            .'6fb42da0e32e07b61c9f0251fe627a9c',
            $client->getUri()->getQuery()
        );
    }

}

class Test_Consumer_39745 extends Zend_Oauth_Consumer
{
    public function getConsumerKey(){return '1234567890';}
    public function getSignatureMethod(){return 'HMAC-SHA1';}
    public function getVersion(){return '1.0';}
    public function getAccessTokenUrl(){return 'http://www.example.com/access';}
    public function getLastRequestToken()
    {
        $return = new Test_Token_39745;
        return $return;
    }
}

class Test_Http_Utility_39745 extends Zend_Oauth_Http_Utility
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

class Test_Client_39745 extends Zend_Http_Client
{
    public function getRawData(){return $this->raw_post_data;}
}

class Test_Token_39745 extends Zend_Oauth_Token_Request
{
    public function getToken(){return '0987654321';}
}