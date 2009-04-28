<?php

require_once 'Zend/Crypt/Math/BigInteger/Gmp.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Crypt/Math/BigInteger/Bcmath.php';

class Zend_Crypt_Math_BigInteger_GmpTest extends PHPUnit_Framework_TestCase
{

    private $_math = null;

    public function setUp()
    {
        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('Skipped: Zend_Crypt_Math_BigInteger_GmpTest due to ext/gmp being unavailable');
            return;
        }
        $this->_math = new Zend_Crypt_Math_BigInteger_Gmp;
    }

    public function testAdd()
    {
        $this->assertEquals('2', $this->_math->add(1,1));
    }

    public function testSubtract()
    {
        $this->assertEquals('-2', $this->_math->subtract(2,4));
    }

    public function testCompare()
    {
        $this->assertEquals('0', $this->_math->compare(2,2));
        $this->assertEquals('-1', $this->_math->compare(2,4));
        $this->assertEquals('1', $this->_math->compare(4,2));
    }

    public function testDivide()
    {
        $this->assertEquals('2', $this->_math->divide(4,2));
        $this->assertEquals('2', $this->_math->divide(9,4));
    }

    public function testModulus()
    {
        $this->assertEquals('1', $this->_math->modulus(3,2));
    }

    public function testMultiply()
    {
        $this->assertEquals('4', $this->_math->multiply(2,2));
    }

    public function testPow()
    {
        $this->assertEquals('4', $this->_math->pow(2,2));
    }

    public function testPowMod()
    {
        $this->assertEquals('1', $this->_math->powmod(2,2,3));
    }

    public function testSqrt()
    {
        $this->assertEquals('2', $this->_math->sqrt(4));
    }

    public function testIntegerToBinaryConversion()
    {
        $binary = base64_decode(
        'ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX+YkcLiemOcPym2CBRYHNOyyjmG0mg3BVd9RcLn5S3IHHoXGHblzqdLFEi/368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI+XUkKJX8Fvf8W8vsixYOr'
        );
        $integer = '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638443';
        $gmpBinary = $this->_math->integerToBinary($integer);
        $this->assertEquals($binary, $gmpBinary);
    }

    public function testBinaryToIntegerConversion()
    {
        $binary = base64_decode(
        'ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX+YkcLiemOcPym2CBRYHNOyyjmG0mg3BVd9RcLn5S3IHHoXGHblzqdLFEi/368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI+XUkKJX8Fvf8W8vsixYOr'
        );
        $integer = '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638443';
        $gmpInteger = $this->_math->binaryToInteger($binary);
        $this->assertEquals($integer, $gmpInteger);
    }

}