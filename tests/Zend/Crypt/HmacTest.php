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
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Crypt;
use Zend\Crypt\Hmac as HMAC;

/**
 * Outside the Internal Function tests, tests do not distinguish between hash and mhash
 * when available. All tests use Hashing algorithms both extensions implement.
 */

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Crypt
 */
class HmacTest extends \PHPUnit_Framework_TestCase
{

    // MD5 tests taken from RFC 2202

    public function testHmacMD5_1()
    {
        $data = 'Hi There';
        $key  = str_repeat("\x0b", 16);
        $hmac = HMAC::compute($key, 'MD5', $data);
        $this->assertEquals('9294727a3638bb1c13f48ef8158bfc9d', $hmac);
    }

    public function testHmacMD5_2()
    {
        $data = 'what do ya want for nothing?';
        $key  = 'Jefe';
        $hmac = HMAC::compute($key, 'MD5', $data);
        $this->assertEquals('750c783e6ab0b503eaa86e310a5db738', $hmac);
    }

    public function testHmacMD5_3()
    {
        $data = str_repeat("\xdd", 50);
        $key  = str_repeat("\xaa", 16);
        $hmac = HMAC::compute($key, 'MD5', $data);
        $this->assertEquals('56be34521d144c88dbb8c733f0e8b3f6', $hmac);
    }

    public function testHmacMD5_4()
    {
        $data = str_repeat("\xcd", 50);
        $key  = "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19";
        $hmac = HMAC::compute($key, 'MD5', $data);
        $this->assertEquals('697eaf0aca3a3aea3a75164746ffaa79', $hmac);
    }

    public function testHmacMD5_5()
    {
        $data = 'Test With Truncation';
        $key  = str_repeat("\x0c", 16);
        $hmac = HMAC::compute($key, 'MD5', $data);
        $this->assertEquals('56461ef2342edc00f9bab995690efd4c', $hmac);
    }

    public function testHmacMD5_6()
    {
        $data = 'Test Using Larger Than Block-Size Key - Hash Key First';
        $key  = str_repeat("\xaa", 80);
        $hmac = HMAC::compute($key, 'MD5', $data);
        $this->assertEquals('6b1ab7fe4bd7bf8f0b62e6ce61b9d0cd', $hmac);
    }

    public function testHmacMD5_7()
    {
        $data = 'Test Using Larger Than Block-Size Key and Larger Than One Block-Size Data';
        $key  = str_repeat("\xaa", 80);
        $hmac = HMAC::compute($key, 'MD5', $data);
        $this->assertEquals('6f630fad67cda0ee1fb1f562db3aa53e', $hmac);
    }

    // SHA1 tests taken from RFC 2202

    public function testHmacSHA1_1()
    {
        $data = 'Hi There';
        $key  = str_repeat("\x0b", 20);
        $hmac = HMAC::compute($key, 'SHA1', $data);
        $this->assertEquals('b617318655057264e28bc0b6fb378c8ef146be00', $hmac);
    }

    public function testHmacSHA1_2()
    {
        $data = 'what do ya want for nothing?';
        $key  = 'Jefe';
        $hmac = HMAC::compute($key, 'SHA1', $data);
        $this->assertEquals('effcdf6ae5eb2fa2d27416d5f184df9c259a7c79', $hmac);
    }

    public function testHmacSHA1_3()
    {
        $data = str_repeat("\xdd", 50);
        $key  = str_repeat("\xaa", 20);
        $hmac = HMAC::compute($key, 'SHA1', $data);
        $this->assertEquals('125d7342b9ac11cd91a39af48aa17b4f63f175d3', $hmac);
    }

    public function testHmacSHA1_4()
    {
        $data = str_repeat("\xcd", 50);
        $key  = "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19";
        $hmac = HMAC::compute($key, 'SHA1', $data);
        $this->assertEquals('4c9007f4026250c6bc8414f9bf50c86c2d7235da', $hmac);
    }

    public function testHmacSHA1_5()
    {
        $data = 'Test With Truncation';
        $key  = str_repeat("\x0c", 20);
        $hmac = HMAC::compute($key, 'SHA1', $data);
        $this->assertEquals('4c1a03424b55e07fe7f27be1d58bb9324a9a5a04', $hmac);
    }

    public function testHmacSHA1_6()
    {
        $data = 'Test Using Larger Than Block-Size Key - Hash Key First';
        $key  = str_repeat("\xaa", 80);
        $hmac = HMAC::compute($key, 'SHA1', $data);
        $this->assertEquals('aa4ae5e15272d00e95705637ce8a3b55ed402112', $hmac);
    }

    public function testHmacSHA1_7()
    {
        $data = 'Test Using Larger Than Block-Size Key and Larger Than One Block-Size Data';
        $key  = str_repeat("\xaa", 80);
        $hmac = HMAC::compute($key, 'SHA1', $data);
        $this->assertEquals('e8e99d0f45237d786d6bbaa7965c7808bbff1a91', $hmac);
    }

    // RIPEMD160 tests taken from RFC 2286

    public function testHmacRIPEMD160_1()
    {
        $data = 'Hi There';
        $key  = str_repeat("\x0b", 20);
        $hmac = HMAC::compute($key, 'RIPEMD160', $data);
        $this->assertEquals('24cb4bd67d20fc1a5d2ed7732dcc39377f0a5668', $hmac);
    }

    public function testHmacRIPEMD160_2()
    {
        $data = 'what do ya want for nothing?';
        $key  = 'Jefe';
        $hmac = HMAC::compute($key, 'RIPEMD160', $data);
        $this->assertEquals('dda6c0213a485a9e24f4742064a7f033b43c4069', $hmac);
    }

    public function testHmacRIPEMD160_3()
    {
        $data = str_repeat("\xdd", 50);
        $key  = str_repeat("\xaa", 20);
        $hmac = HMAC::compute($key, 'RIPEMD160', $data);
        $this->assertEquals('b0b105360de759960ab4f35298e116e295d8e7c1', $hmac);
    }

    public function testHmacRIPEMD160_4()
    {
        $data = str_repeat("\xcd", 50);
        $key  = "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19";
        $hmac = HMAC::compute($key, 'RIPEMD160', $data);
        $this->assertEquals('d5ca862f4d21d5e610e18b4cf1beb97a4365ecf4', $hmac);
    }

    public function testHmacRIPEMD160_5()
    {
        $data = 'Test With Truncation';
        $key  = str_repeat("\x0c", 20);
        $hmac = HMAC::compute($key, 'RIPEMD160', $data);
        $this->assertEquals('7619693978f91d90539ae786500ff3d8e0518e39', $hmac);
    }

    public function testHmacRIPEMD160_6()
    {
        $data = 'Test Using Larger Than Block-Size Key - Hash Key First';
        $key  = str_repeat("\xaa", 80);
        $hmac = HMAC::compute($key, 'RIPEMD160', $data);
        $this->assertEquals('6466ca07ac5eac29e1bd523e5ada7605b791fd8b', $hmac);
    }

    public function testHmacRIPEMD160_7()
    {
        $data = 'Test Using Larger Than Block-Size Key and Larger Than One Block-Size Data';
        $key  = str_repeat("\xaa", 80);
        $hmac = HMAC::compute($key, 'RIPEMD160', $data);
        $this->assertEquals('69ea60798d71616cce5fd0871e23754cd75d5a0a', $hmac);
    }

    public function testEmptyKey()
    {
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'Provided key is null or empty');
        $hash = HMAC::compute(null, 'md5', 'test');
    }

    public function testWrongHashAlgorithm()
    {
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'Hash algorithm is not supported on this PHP installation');
        $hash = HMAC::compute('key', 'wrong', 'test');
    }

    public function testBinaryOutput()
    {
        $data = HMAC::compute('key', 'sha256', 'test', HMAC::OUTPUT_BINARY);
        $this->assertEquals('Aq+1YwSQLGVvy3N83QPeYgW7bUAdooEu/ZstNqCK8Vk=', base64_encode($data));
    }
}
