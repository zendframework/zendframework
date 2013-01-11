<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace ZendTest\Crypt;

use Zend\Crypt\Hmac;

/**
 * Outside the Internal Function tests, tests do not distinguish between hash and mhash
 * when available. All tests use Hashing algorithms both extensions implement.
 */

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @group      Zend_Crypt
 */
class HmacTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSupportedAndCache()
    {
        Hmac::clearLastAlgorithmCache();
        $this->assertAttributeEquals(null, 'lastAlgorithmSupported', 'Zend\Crypt\Hmac');

        $algorithm = 'sha512';

        // cache value must be exactly equal to the original input
        $this->assertTrue(Hmac::isSupported($algorithm));
        $this->assertAttributeEquals($algorithm, 'lastAlgorithmSupported', 'Zend\Crypt\Hmac');
        $this->assertAttributeNotEquals('sHa512', 'lastAlgorithmSupported', 'Zend\Crypt\Hmac');

        // cache value must be exactly equal to the first input (cache hit)
        Hmac::isSupported('sha512');
        $this->assertAttributeEquals($algorithm, 'lastAlgorithmSupported', 'Zend\Crypt\Hmac');

        // cache changes with a new algorithm
        $this->assertTrue(Hmac::isSupported('MD5'));
        $this->assertAttributeEquals('MD5', 'lastAlgorithmSupported', 'Zend\Crypt\Hmac');

        // cache don't change due wrong algorithm
        $this->assertFalse(Hmac::isSupported('wrong'));
        $this->assertAttributeEquals('MD5', 'lastAlgorithmSupported', 'Zend\Crypt\Hmac');

        Hmac::clearLastAlgorithmCache();
        $this->assertAttributeEquals(null, 'lastAlgorithmSupported', 'Zend\Crypt\Hmac');
    }

    // MD5 tests taken from RFC 2202
    public function provideMd5Data()
    {
        return array(
            array('Hi There', str_repeat("\x0b", 16), '9294727a3638bb1c13f48ef8158bfc9d'),
            array('what do ya want for nothing?', 'Jefe', '750c783e6ab0b503eaa86e310a5db738'),
            array(str_repeat("\xdd", 50), str_repeat("\xaa", 16), '56be34521d144c88dbb8c733f0e8b3f6'),
            array(str_repeat("\xcd", 50),
                  "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19",
                  '697eaf0aca3a3aea3a75164746ffaa79'),
            array('Test With Truncation', str_repeat("\x0c", 16), '56461ef2342edc00f9bab995690efd4c'),
            array('Test Using Larger Than Block-Size Key - Hash Key First', str_repeat("\xaa", 80),
                  '6b1ab7fe4bd7bf8f0b62e6ce61b9d0cd'),
            array('Test Using Larger Than Block-Size Key and Larger Than One Block-Size Data',
                  str_repeat("\xaa", 80), '6f630fad67cda0ee1fb1f562db3aa53e'),
        );
    }

    /**
     * @dataProvider provideMd5Data
     */
    public function testMd5($data, $key, $output)
    {
        $hash = Hmac::compute($key, 'MD5', $data);
        $this->assertEquals($output, $hash);
    }


    // SHA1 tests taken from RFC 2202
    public function provideSha1Data()
    {
        return array(
            array('Hi There', str_repeat("\x0b", 20), 'b617318655057264e28bc0b6fb378c8ef146be00'),
            array('what do ya want for nothing?', 'Jefe', 'effcdf6ae5eb2fa2d27416d5f184df9c259a7c79'),
            array(str_repeat("\xdd", 50), str_repeat("\xaa", 20), '125d7342b9ac11cd91a39af48aa17b4f63f175d3'),
            array(str_repeat("\xcd", 50),
                  "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19",
                  '4c9007f4026250c6bc8414f9bf50c86c2d7235da'),
            array('Test With Truncation', str_repeat("\x0c", 20), '4c1a03424b55e07fe7f27be1d58bb9324a9a5a04'),
            array('Test Using Larger Than Block-Size Key - Hash Key First', str_repeat("\xaa", 80),
                  'aa4ae5e15272d00e95705637ce8a3b55ed402112'),
            array('Test Using Larger Than Block-Size Key and Larger Than One Block-Size Data',
                  str_repeat("\xaa", 80), 'e8e99d0f45237d786d6bbaa7965c7808bbff1a91'),
        );
    }

    /**
     * @dataProvider provideSha1Data
     */
    public function testSha1($data, $key, $output)
    {
        $hash = Hmac::compute($key, 'SHA1', $data);
        $this->assertEquals($output, $hash);
    }

    // RIPEMD160 tests taken from RFC 2286
    public function provideRipemd160Data()
    {
        return array(
            array('Hi There', str_repeat("\x0b", 20), '24cb4bd67d20fc1a5d2ed7732dcc39377f0a5668'),
            array('what do ya want for nothing?', 'Jefe', 'dda6c0213a485a9e24f4742064a7f033b43c4069'),
            array(str_repeat("\xdd", 50), str_repeat("\xaa", 20), 'b0b105360de759960ab4f35298e116e295d8e7c1'),
            array(str_repeat("\xcd", 50),
                  "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19",
                  'd5ca862f4d21d5e610e18b4cf1beb97a4365ecf4'),
            array('Test With Truncation', str_repeat("\x0c", 20), '7619693978f91d90539ae786500ff3d8e0518e39'),
            array('Test Using Larger Than Block-Size Key - Hash Key First', str_repeat("\xaa", 80),
                  '6466ca07ac5eac29e1bd523e5ada7605b791fd8b'),
            array('Test Using Larger Than Block-Size Key and Larger Than One Block-Size Data',
                  str_repeat("\xaa", 80), '69ea60798d71616cce5fd0871e23754cd75d5a0a'),
        );
    }

    /**
     * @dataProvider provideRipemd160Data
     */
    public function testRipemd160($data, $key, $output)
    {
        $hash = Hmac::compute($key, 'RIPEMD160', $data);
        $this->assertEquals($output, $hash);
    }

    public function testEmptyKey()
    {
        Hmac::clearLastAlgorithmCache();
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'Provided key is null or empty');
        Hmac::compute(null, 'md5', 'test');
    }

    public function testNullHashAlgorithm()
    {
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'Hash algorithm is not supported on this PHP installation');
        Hmac::compute('key', null, 'test');
    }

    public function testWrongHashAlgorithm()
    {
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'Hash algorithm is not supported on this PHP installation');
        Hmac::compute('key', 'wrong', 'test');
    }

    public function testBinaryOutput()
    {
        $data = Hmac::compute('key', 'sha256', 'test', Hmac::OUTPUT_BINARY);
        $this->assertEquals('Aq+1YwSQLGVvy3N83QPeYgW7bUAdooEu/ZstNqCK8Vk=', base64_encode($data));
    }
}
