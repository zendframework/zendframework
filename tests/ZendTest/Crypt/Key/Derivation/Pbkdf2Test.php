<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace ZendTest\Crypt\Key\Derivation;

use Zend\Crypt\Key\Derivation\Pbkdf2;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 */
class Pbkdf2Test extends \PHPUnit_Framework_TestCase
{

    /** @var string */
    public $salt;

    public function setUp()
    {
        $this->salt = '12345678901234567890123456789012';
    }

    public function testCalc()
    {
        $password = Pbkdf2::calc('sha256', 'test', $this->salt, 5000, 32);
        $this->assertEquals(32, strlen($password));
        $this->assertEquals('JVNgHc1AeBl/S9H6Jo2tUUi838snakDBMcsNJP0+0O0=', base64_encode($password));
    }

    public function testCalcWithWrongHash()
    {
        $this->setExpectedException('Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException',
                                    'The hash algorithm wrong is not supported by Zend\Crypt\Key\Derivation\Pbkdf2');
        $password = Pbkdf2::calc('wrong', 'test', $this->salt, 5000, 32);
    }

    /**
     * Test vectors from RFC 6070
     *
     * @see http://tools.ietf.org/html/draft-josefsson-pbkdf2-test-vectors-06
     */
    public static function provideTestVectors()
    {
        return array (
            array('sha1', 'password', 'salt', 1, 20, '0c60c80f961f0e71f3a9b524af6012062fe037a6'),
            array('sha1', 'password', 'salt', 2, 20, 'ea6c014dc72d6f8ccd1ed92ace1d41f0d8de8957'),
            array('sha1', 'password', 'salt', 4096, 20, '4b007901b765489abead49d926f721d065a429c1'),
        array('sha1', 'passwordPASSWORDpassword', 'saltSALTsaltSALTsaltSALTsaltSALTsalt', 4096, 25, '3d2eec4fe41c849b80c8d83662c0e44a8b291a964cf2f07038'),
            array('sha1', "pass\0word", "sa\0lt", 4096, 16, '56fa6aa75548099dcc37d7f03425e0c3')
        );
    }

    /**
     * @dataProvider provideTestVectors
     */
    public function testRFC670($hash, $password, $salt, $cycles, $length, $expect)
    {
        $result = Pbkdf2::calc($hash, $password, $salt, $cycles, $length);
        $this->assertEquals($expect, bin2hex($result));
    }
}
