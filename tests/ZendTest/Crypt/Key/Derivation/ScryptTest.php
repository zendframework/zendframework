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

use Zend\Crypt\Key\Derivation\Scrypt;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @group      Zend_Crypt
 */
class ScryptTest extends \PHPUnit_Framework_TestCase
{

    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('Zend\Crypt\Key\Derivation\Scrypt');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Test vector of Salsa 20/8 core
     *
     * @see https://tools.ietf.org/html/draft-josefsson-scrypt-kdf-01#section-7
     */
    public function testVectorSalsa208Core()
    {
        $hexInput  = '7e 87 9a 21 4f 3e c9 86 7c a9 40 e6 41 71 8f 26
                      ba ee 55 5b 8c 61 c1 b5 0d f8 46 11 6d cd 3b 1d
                      ee 24 f3 19 df 9b 3d 85 14 12 1e 4b 5a c5 aa 32
                      76 02 1d 29 09 c7 48 29 ed eb c6 8d b8 b8 c2 5e';

        $hexOutput = 'a4 1f 85 9c 66 08 cc 99 3b 81 ca cb 02 0c ef 05
                      04 4b 21 81 a2 fd 33 7d fd 7b 1c 63 96 68 2f 29
                      b4 39 31 68 e3 c9 e6 bc fe 6b c5 b7 a0 6d 96 ba
                      e4 24 cc 10 2c 91 74 5c 24 ad 67 3d c7 61 8f 81';


        $salsaAlg = 'salsa208Core32';
        if (PHP_INT_SIZE === 8) {
            $salsaAlg = 'salsa208Core64';
        }
        $salsa20 = self::getMethod($salsaAlg);
        $obj     = $this->getMockForAbstractClass('Zend\Crypt\Key\Derivation\Scrypt');
        $input   = self::hex2bin(str_replace(array(' ',"\n"),'',$hexInput));
        $result  = $salsa20->invokeArgs($obj, array($input));

        $this->assertEquals(64, strlen($input), 'Input must be a string of 64 bytes');
        $this->assertEquals(64, strlen($result), 'Output must be a string of 64 bytes');
        $this->assertEquals(str_replace(array(' ',"\n"),'',$hexOutput), bin2hex($result));
    }
    /**
     * Test vector of Scrypt BlockMix
     *
     * @see https://tools.ietf.org/html/draft-josefsson-scrypt-kdf-01#section-8
     */
    public function testVectorScryptBlockMix()
    {
        $hexInput  = 'f7 ce 0b 65 3d 2d 72 a4 10 8c f5 ab e9 12 ff dd
                      77 76 16 db bb 27 a7 0e 82 04 f3 ae 2d 0f 6f ad
                      89 f6 8f 48 11 d1 e8 7b cc 3b d7 40 0a 9f fd 29
                      09 4f 01 84 63 95 74 f3 9a e5 a1 31 52 17 bc d7

                      89 49 91 44 72 13 bb 22 6c 25 b5 4d a8 63 70 fb
                      cd 98 43 80 37 46 66 bb 8f fc b5 bf 40 c2 54 b0
                      67 d2 7c 51 ce 4a d5 fe d8 29 c9 0b 50 5a 57 1b
                      7f 4d 1c ad 6a 52 3c da 77 0e 67 bc ea af 7e 89';

        $hexOutput = 'a4 1f 85 9c 66 08 cc 99 3b 81 ca cb 02 0c ef 05
                      04 4b 21 81 a2 fd 33 7d fd 7b 1c 63 96 68 2f 29
                      b4 39 31 68 e3 c9 e6 bc fe 6b c5 b7 a0 6d 96 ba
                      e4 24 cc 10 2c 91 74 5c 24 ad 67 3d c7 61 8f 81

                      20 ed c9 75 32 38 81 a8 05 40 f6 4c 16 2d cd 3c
                      21 07 7c fe 5f 8d 5f e2 b1 a4 16 8f 95 36 78 b7
                      7d 3b 3d 80 3b 60 e4 ab 92 09 96 e5 9b 4d 53 b6
                      5d 2a 22 58 77 d5 ed f5 84 2c b9 f1 4e ef e4 25';

        $blockMix = self::getMethod('scryptBlockMix');
        $obj      = $this->getMockForAbstractClass('Zend\Crypt\Key\Derivation\Scrypt');
        $input    = self::hex2bin(str_replace(array(' ',"\n"), '', $hexInput));
        $result   = $blockMix->invokeArgs($obj, array($input, 1));

        $this->assertEquals(str_replace(array(' ',"\n"),'',$hexOutput), bin2hex($result));
    }

    /**
     * Test vector of Scrypt ROMix
     *
     * @see https://tools.ietf.org/html/draft-josefsson-scrypt-kdf-01#section-9
     */
    public function testVectorScryptROMix()
    {
        $hexInput  = 'f7 ce 0b 65 3d 2d 72 a4 10 8c f5 ab e9 12 ff dd
                      77 76 16 db bb 27 a7 0e 82 04 f3 ae 2d 0f 6f ad
                      89 f6 8f 48 11 d1 e8 7b cc 3b d7 40 0a 9f fd 29
                      09 4f 01 84 63 95 74 f3 9a e5 a1 31 52 17 bc d7
                      89 49 91 44 72 13 bb 22 6c 25 b5 4d a8 63 70 fb
                      cd 98 43 80 37 46 66 bb 8f fc b5 bf 40 c2 54 b0
                      67 d2 7c 51 ce 4a d5 fe d8 29 c9 0b 50 5a 57 1b
                      7f 4d 1c ad 6a 52 3c da 77 0e 67 bc ea af 7e 89';

        $hexOutput = '79 cc c1 93 62 9d eb ca 04 7f 0b 70 60 4b f6 b6
                      2c e3 dd 4a 96 26 e3 55 fa fc 61 98 e6 ea 2b 46
                      d5 84 13 67 3b 99 b0 29 d6 65 c3 57 60 1f b4 26
                      a0 b2 f4 bb a2 00 ee 9f 0a 43 d1 9b 57 1a 9c 71
                      ef 11 42 e6 5d 5a 26 6f dd ca 83 2c e5 9f aa 7c
                      ac 0b 9c f1 be 2b ff ca 30 0d 01 ee 38 76 19 c4
                      ae 12 fd 44 38 f2 03 a0 e4 e1 c4 7e c3 14 86 1f
                      4e 90 87 cb 33 39 6a 68 73 e8 f9 d2 53 9a 4b 8e';


        $roMix  = self::getMethod('scryptROMix');
        $obj    = $this->getMockForAbstractClass('Zend\Crypt\Key\Derivation\Scrypt');
        $input  = self::hex2bin(str_replace(array(' ',"\n"), '', $hexInput));
        $result = $roMix->invokeArgs($obj, array($input, 16, 1));

        $this->assertEquals(str_replace(array(' ',"\n"),'',$hexOutput), bin2hex($result));
    }


    /**
     * Test Vector Scrypt
     *
     * @see https://tools.ietf.org/html/draft-josefsson-scrypt-kdf-01#section-11
     */
    public function testVectorScrypt()
    {
        $hexOutput = '77 d6 57 62 38 65 7b 20 3b 19 ca 42 c1 8a 04 97
                      f1 6b 48 44 e3 07 4a e8 df df fa 3f ed e2 14 42
                      fc d0 06 9d ed 09 48 f8 32 6a 75 3a 0f c8 1f 17
                      e8 d3 e0 fb 2e 0d 36 28 cf 35 e2 0c 38 d1 89 06';

        $result = Scrypt::calc('', '', 16, 1, 1, 64);
        $this->assertEquals(64, strlen($result));
        $this->assertEquals(str_replace(array(' ',"\n"),'',$hexOutput), bin2hex($result));
    }

    /**
     * @expectedException Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException
     */
    public function testScryptWrongN()
    {
        $result = Scrypt::calc('test', 'salt', 17, 1, 1, 64);
        $result = Scrypt::calc('test', 'salt', PHP_INT_MAX, 1, 1, 64);
    }

    /**
     * @expectedException Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException
     */
    public function testScryptWrongR()
    {
         $result = Scrypt::calc('test', 'salt', PHP_INT_MAX / 128, 4, 1, 64);
    }

    /**
     * Test scrypt correct size output
     */
    public function testScryptSize()
    {
        for ($size = 0; $size < 64; $size++) {
            if (extension_loaded('Scrypt') && ($size < 16)) {
                $this->setExpectedException('Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException');
            }
            $result = Scrypt::calc('test', 'salt', 16, 1, 1, $size);
            $this->assertEquals($size, strlen($result));
        }
    }

    /**
     * Convert a string with hex values in binary string
     *
     * @param  string $hex
     * @return string
     */
    protected static function hex2bin($hex)
    {
        $len    = strlen($hex);
        $result = '';
        for ($i = 0; $i < $len; $i += 2) {
            $result .=  chr(hexdec($hex[$i] . $hex[$i+1]));
        }
        return $result;
    }
}
