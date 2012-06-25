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
use Zend\Crypt\Hash;

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
class HashTest extends \PHPUnit_Framework_TestCase
{
    // SHA1 tests taken from RFC 3174

    public static function provideTestSHA1Data()
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
     * @dataProvider provideTestSHA1Data
     */
    public function testSHA1($data, $output)
    {
        $hash = Hash::compute('sha1', $data);
        $this->assertEquals($output, $hash);
    }

    // SHA-224 tests taken from RFC 3874

    public static function provideTestSHA224Data()
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
     * @dataProvider provideTestSHA224Data
     */
    public function testSHA224($data, $output)
    {
        $hash = Hash::compute('sha224', $data);
        $this->assertEquals($output, $hash);
    }

    // MD5 test suite taken from RFC 1321

    public static function provideTestMD5Data()
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
     * @dataProvider provideTestMD5Data
     */
    public function testMD5($data, $output)
    {
        $hash = Hash::compute('md5', $data);
        $this->assertEquals($output, $hash);
    }

    public function testWrongHashAlgorithm()
    {
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'Hash algorithm provided is not supported on this PHP installation');
        $hash = Hash::compute('wrong', 'test');
    }

    public function testBinaryOutput()
    {
        $hash = Hash::compute('sha1', 'test', Hash::OUTPUT_BINARY);
        $this->assertEquals('qUqP5cyxm6YcTAhz05Hph5gvu9M=', base64_encode($hash));
    }

}
