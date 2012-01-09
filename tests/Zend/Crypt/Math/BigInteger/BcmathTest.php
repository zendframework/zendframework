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

/**
 * @namespace
 */
namespace ZendTest\Crypt\Math\BigInteger;

use Zend\Crypt\Math\BigInteger\Bcmath;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Crypt
 */
class BcmathTest extends \PHPUnit_Framework_TestCase
{

    private $_math = null;

    public function setUp()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('Skipped: Zend_Crypt_Math_BigInteger_BcmathTest due to ext/bcmath being unavailable');
            return;
        }
        $this->_math = new Bcmath;
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
