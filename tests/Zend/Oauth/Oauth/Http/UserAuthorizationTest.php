<?php


class Zend_Oauth_Http_UserAuthorizationTest extends PHPUnit_Framework_TestCase
{

    protected $stubConsumer = null;

    public function setup()
    {
        $this->stubConsumer = new Test_Consumer_34879;
    }

    public function testConstructorSetsConsumerInstance()
    {
        $redirect = new Zend_Oauth_Http_UserAuthorization($this->stubConsumer);
        $this->assertType('Test_Consumer_34879', $redirect->getConsumer());
    }

    public function testConstructorSetsCustomServiceParameters()
    {
        $redirect = new Zend_Oauth_Http_UserAuthorization($this->stubConsumer, array(1,2,3));
        $this->assertEquals(array(1,2,3), $redirect->getParameters());
    }

    public function testAssembleParametersReturnsUserAuthorizationParamArray()
    {
        $redirect = new Zend_Oauth_Http_UserAuthorization($this->stubConsumer, array('foo '=>'bar~'));
        $expected = array(
            'oauth_token'=>'1234567890',
            'oauth_callback'=>'http://www.example.com/local',
            'foo '=>'bar~'
        );
        $this->assertEquals($expected, $redirect->assembleParams());
    }

    public function testGetUrlReturnsEncodedQueryStringParamsAppendedToLocalUrl()
    {
        $redirect = new Zend_Oauth_Http_UserAuthorization($this->stubConsumer, array('foo '=>'bar~'));
        $expected =
            'http://www.example.com/authorize?oauth_token=1234567890&oauth_callback=http%3A%2F%2Fwww.example.com%2Flocal&foo%20=bar~';
        $this->assertEquals($expected, $redirect->getUrl());
    }

}

class Test_Consumer_34879 extends Zend_Oauth_Consumer
{
    public function getUserAuthorizationUrl(){return 'http://www.example.com/authorize';}
    public function getCallbackUrl(){return 'http://www.example.com/local';}
    public function getLastRequestToken(){$r=new Test_Token_34879;return $r;}
}
class Test_Token_34879
{
    public function getToken(){return '1234567890';}
}
