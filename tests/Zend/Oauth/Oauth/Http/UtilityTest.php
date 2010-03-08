<?php


class Zend_Oauth_Http_UtilityTest extends PHPUnit_Framework_TestCase
{
    // see: http://wiki.oauth.net/TestCases (Parameter Encoding Tests)

    public function testUrlEncodeCorrectlyEncodesAlnum()
    {
        $string = 'abcABC123';
        $this->assertEquals('abcABC123', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesUnreserved()
    {
        $string = '-._~';
        $this->assertEquals('-._~', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesPercentSign()
    {
        $string = '%';
        $this->assertEquals('%25', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesPlusSign()
    {
        $string = '+';
        $this->assertEquals('%2B', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesAmpEqualsAndAsterix()
    {
        $string = '&=*';
        $this->assertEquals('%26%3D%2A', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesSpace()
    {
        $string = ' ';
        $this->assertEquals('%20', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesLineFeed()
    {
        $string = "\n";
        $this->assertEquals('%0A', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesU007F()
    {
        $string = chr(127);
        $this->assertEquals('%7F', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesU0080()
    {
        $string = "\xC2\x80";
        $this->assertEquals('%C2%80', Zend_Oauth_Http_Utility::urlEncode($string));
    }

    public function testUrlEncodeCorrectlyEncodesU3001()
    {
        $string = '、';
        $this->assertEquals('%E3%80%81', Zend_Oauth_Http_Utility::urlEncode($string));
    }

}