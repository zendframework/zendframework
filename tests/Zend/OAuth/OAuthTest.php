<?php

namespace ZendTest\OAuth;

use Zend\OAuth;

class OauthTest extends \PHPUnit_Framework_TestCase
{

    public function teardown()
    {
        OAuth\OAuth::clearHttpClient();
    }

    public function testCanSetCustomHttpClient()
    {
        OAuth\OAuth::setHttpClient(new HTTPClient19485876());
        $this->assertType('ZendTest\\OAuth\\HttpClient19485876', OAuth\OAuth::getHttpClient());
    }

    public function testGetHttpClientResetsParameters()
    {
        $client = new HTTPClient19485876();
        $client->setParameterGet(array('key'=>'value'));
        OAuth\OAuth::setHttpClient($client);
        $resetClient = OAuth\OAuth::getHttpClient();
        $resetClient->setUri('http://www.example.com');
        $this->assertEquals('http://www.example.com:80', $resetClient->getUri(true));
    }

    public function testGetHttpClientResetsAuthorizationHeader()
    {
        $client = new HTTPClient19485876();
        $client->setHeaders('Authorization', 'realm="http://www.example.com",oauth_version="1.0"');
        OAuth\OAuth::setHttpClient($client);
        $resetClient = OAuth\OAuth::getHttpClient();
        $this->assertEquals(null, $resetClient->getHeader('Authorization'));
    }

}

class HTTPClient19485876 extends \Zend\Http\Client {}
