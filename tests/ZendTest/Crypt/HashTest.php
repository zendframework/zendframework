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

use Zend\Crypt\Hash;

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
class HashTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSupportedAndCache()
    {
        Hash::clearLastAlgorithmCache();
        $this->assertAttributeEquals(null, 'lastAlgorithmSupported', 'Zend\Crypt\Hash');

        $algorithm = 'sha512';

        // cache value must be exactly equal to the original input
        $this->assertTrue(Hash::isSupported($algorithm));
        $this->assertAttributeEquals($algorithm, 'lastAlgorithmSupported', 'Zend\Crypt\Hash');
        $this->assertAttributeNotEquals('sHa512', 'lastAlgorithmSupported', 'Zend\Crypt\Hash');

        // cache value must be exactly equal to the first input (cache hit)
        Hash::isSupported('sha512');
        $this->assertAttributeEquals($algorithm, 'lastAlgorithmSupported', 'Zend\Crypt\Hash');

        // cache changes with a new algorithm
        $this->assertTrue(Hash::isSupported('sha1'));
        $this->assertAttributeEquals('sha1', 'lastAlgorithmSupported', 'Zend\Crypt\Hash');

        // cache don't change due wrong algorithm
        $this->assertFalse(Hash::isSupported('wrong'));
        $this->assertAttributeEquals('sha1', 'lastAlgorithmSupported', 'Zend\Crypt\Hash');

        Hash::clearLastAlgorithmCache();
        $this->assertAttributeEquals(null, 'lastAlgorithmSupported', 'Zend\Crypt\Hash');
    }

    // SHA1 tests taken from RFC 3174
    public function provideSha1Data()
    {
        return array(
            array('abc',
                  strtolower('A9993E364706816ABA3E25717850C26C9CD0D89D')),
            array('abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq',
                  strtolower('84983E441C3BD26EBAAE4AA1F95129E5E54670F1')),
            array(str_repeat('a', 1000000),
                  strtolower('34AA973CD4C4DAA4F61EEB2BDBAD27316534016F')),
            array(str_repeat('01234567', 80),
                  strtolower('DEA356A2CDDD90C7A7ECEDC5EBB563934F460452'))
        );
    }

    /**
     * @dataProvider provideSha1Data
     */
    public function testSha1($data, $output)
    {
        $hash = Hash::compute('sha1', $data);
        $this->assertEquals($output, $hash);
    }

    // SHA-224 tests taken from RFC 3874
    public function provideSha224Data()
    {
        return array(
            array('abc', '23097d223405d8228642a477bda255b32aadbce4bda0b3f7e36c9da7'),
            array('abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq',
                  '75388b16512776cc5dba5da1fd890150b0c6455cb4f58b1952522525'),
            array(str_repeat('a', 1000000),
                  '20794655980c91d8bbb4c1ea97618a4bf03f42581948b2ee4ee7ad67')
        );
    }

    /**
     * @dataProvider provideSha224Data
     */
    public function testSha224($data, $output)
    {
        $hash = Hash::compute('sha224', $data);
        $this->assertEquals($output, $hash);
    }

    // MD5 test suite taken from RFC 1321
    public function provideMd5Data()
    {
        return array(
            array('', 'd41d8cd98f00b204e9800998ecf8427e'),
            array('a', '0cc175b9c0f1b6a831c399e269772661'),
            array('abc', '900150983cd24fb0d6963f7d28e17f72'),
            array('message digest', 'f96b697d7cb7938d525a2f31aaf161d0'),
            array('abcdefghijklmnopqrstuvwxyz', 'c3fcd3d76192e4007dfb496cca67e13b'),
            array('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
                  'd174ab98d277d9f5a5611c2c9f419d9f'),
            array(str_repeat('1234567890', 8), '57edf4a22be3c955ac49da2e2107b67a')
        );
    }

    /**
     * @dataProvider provideMd5Data
     */
    public function testMd5($data, $output)
    {
        $hash = Hash::compute('md5', $data);
        $this->assertEquals($output, $hash);
    }

    public function testNullHashAlgorithm()
    {
        Hash::clearLastAlgorithmCache();
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'Hash algorithm provided is not supported on this PHP installation');
        Hash::compute(null, 'test');
    }

    public function testWrongHashAlgorithm()
    {
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'Hash algorithm provided is not supported on this PHP installation');
        Hash::compute('wrong', 'test');
    }

    public function testBinaryOutput()
    {
        $hash = Hash::compute('sha1', 'test', Hash::OUTPUT_BINARY);
        $this->assertEquals('qUqP5cyxm6YcTAhz05Hph5gvu9M=', base64_encode($hash));
    }
}
