<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
 */

namespace ZendTest\OAuth\Signature;

use Zend\OAuth\Signature;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @group      Zend_OAuth
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{

    public function testNormaliseHttpBaseSignatureUrl()
    {
        $sign = new Signature\Plaintext('foo');
        $url = 'HTTP://WWW.EXAMPLE.COM:80/REQUEST';
        $this->assertEquals('http://www.example.com/REQUEST', $sign->normaliseBaseSignatureUrl($url));
    }

    public function testNormaliseHttpsBaseSignatureUrl()
    {
        $sign = new Signature\Plaintext('foo');
        $url = 'HTTPS://WWW.EXAMPLE.COM:443/REQUEST';
        $this->assertEquals('https://www.example.com/REQUEST', $sign->normaliseBaseSignatureUrl($url));
    }

    public function testNormaliseHttpPortBaseSignatureUrl()
    {
        $sign = new Signature\Plaintext('foo');
        $url = 'HTTP://WWW.EXAMPLE.COM:443/REQUEST';
        $this->assertEquals('http://www.example.com:443/REQUEST', $sign->normaliseBaseSignatureUrl($url));
    }

    public function testNormaliseHttpsPortBaseSignatureUrl()
    {
        $sign = new Signature\Plaintext('foo');
        $url = 'HTTPS://WWW.EXAMPLE.COM:80/REQUEST';
        $this->assertEquals('https://www.example.com:80/REQUEST', $sign->normaliseBaseSignatureUrl($url));
    }

    public function testNormaliseHttpsRemovesFragmentFromBaseSignatureUrl()
    {
        $sign = new Signature\Plaintext('foo');
        $url = 'https://www.example.com/request#foo';
        $this->assertEquals('https://www.example.com/request', $sign->normaliseBaseSignatureUrl($url));
    }

    public function testNormaliseHttpsRemovesQueryFromBaseSignatureUrl()
    {
        $sign = new Signature\Plaintext('foo');
        $url = 'https://www.example.com/request?foo=bar';
        $this->assertEquals('https://www.example.com/request', $sign->normaliseBaseSignatureUrl($url));
    }
}
