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
 * @package    Zend_Math_BigInteger
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Math\BigInteger\Adapter;

use Zend\Math\BigInteger\Adapter\Gmp;

/**
 * @category   Zend
 * @package    Zend_Math_BigInteger
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Crypt
 */
class GmpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Gmp
     */
    protected $adapter = null;

    protected $opts = array();

    public function setUp()
    {
        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('Missing ext/gmp');
            return;
        }

        $this->opts = array(
            'pos_int' => '1551728981814736974712322577637155399157248019669154044797077953140576293785419' .
                         '1758065122742369818899372781615264663143856159582568818888995127215884267541995' .
                         '0341258706556549803580104870537681476726513255747040765857479291291572334510643' .
                         '245094715007229621094194349783925984760375594985848253359305585439638443',

            'pos_bin' => base64_decode(
                '3Pk6C4g5cuwOGZiaxaLOMQ4dN3F+jZVxu3Yjcxhm5h73Wi4niYsFf5iRwuJ6Y5w/KbYIFFgc07LKOYbSaDc' .
                'FV31FwuflLcgcehcYduXOp0sUSL/frxiCjv0lGfFOReOCZjSvGUnltTXMgppIO4p2Ij5dSQolfwW9/xby+yLFg6s='
            ),
            'pos_bin_twoc' => base64_decode(
                'ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX+YkcLiemOcPym2CBRYHNOyyjmG0mg3B' .
                'Vd9RcLn5S3IHHoXGHblzqdLFEi/368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI+XUkKJX8Fvf8W8vsixYOr'
            ),
            'neg_bin_twoc' => base64_decode(
                '/yMGxfR3xo0T8eZnZTpdMc7x4siOgXJqjkSJ3IznmRnhCKXR2HZ0+oBnbj0dhZxjwNZJ9+un4yxNNcZ5LZfI+q' .
                'iCuj0YGtI344Xo54kaMVi067dAIFDnfXEC2uYOsbocfZnLUOa2GkrKM31lt8R1id3Borb12oD6QgDpDQTdOnxV'
            ),
        );

        $this->adapter = new Gmp();
    }

    public function testInit()
    {
        // decimal
        $r = $this->adapter->init('+1234567890');
        $this->assertTrue((is_resource($r) && get_resource_type($r) == 'GMP integer'));
        $this->assertEquals('1234567890', gmp_strval($r));
        // octal
        $r = $this->adapter->init('011145401322');
        $this->assertTrue((is_resource($r) && get_resource_type($r) == 'GMP integer'));
        $this->assertEquals('1234567890', gmp_strval($r));
        // hex
        $r = $this->adapter->init('-0x499602d2');
        $this->assertTrue((is_resource($r) && get_resource_type($r) == 'GMP integer'));
        $this->assertEquals('-1234567890', gmp_strval($r));
        // scientific
        $r = $this->adapter->init('-1.23456789e+10');
        $this->assertTrue((is_resource($r) && get_resource_type($r) == 'GMP integer'));
        $this->assertEquals('-12345678900', gmp_strval($r));
    }

    public function testInitFalse()
    {
        $this->assertFalse($this->adapter->init('123-456'));
        $this->assertFalse($this->adapter->init('1.23456789e+3'));
    }

    public function testAdd()
    {
        $this->assertEquals('2', $this->adapter->add('1', '1'));
    }

    public function testSubtract()
    {
        $this->assertEquals('-2', $this->adapter->sub('2', '4'));
    }

    public function testDivide()
    {
        $this->assertEquals('2', $this->adapter->div('4', '2'));
        $this->assertEquals('2', $this->adapter->div('9', '4'));
    }

    public function testDivideByZero()
    {
        $this->setExpectedException('Zend\Math\BigInteger\Exception\DivisionByZeroException',
                                    'Division by zero');
        $this->adapter->div('1', '0');
    }

    public function testMultiply()
    {
        $this->assertEquals('4', $this->adapter->mul('2', '2'));
    }

    public function testCompare()
    {
        $this->assertSame(0, $this->adapter->comp('2', '2'));
        $this->assertSame(-1, $this->adapter->comp('2', '4'));
        $this->assertSame(1, $this->adapter->comp('4', '2'));
    }

    public function testModulus()
    {
        $this->assertEquals('1', $this->adapter->mod('3', '2'));
    }

    public function testPow()
    {
        $this->assertEquals('4', $this->adapter->pow('2', '2'));
    }

    public function testPowMod()
    {
        $this->assertEquals('1', $this->adapter->powmod('2', '2', '3'));
    }

    public function testSqrt()
    {
        $this->assertEquals('2', $this->adapter->sqrt('4'));
    }

    public function testAbs()
    {
        $this->assertSame('1152921504606847103', $this->adapter->abs('1152921504606847103'));
        $this->assertSame('1152921504606847103', $this->adapter->abs('-1152921504606847103'));
    }

    public function testIntegerToBinaryConversion()
    {
        // zero
        $gmpBin = $this->adapter->intToBin('0');
        $this->assertEquals(chr(0), $gmpBin);

        // positive
        $gmpBin = $this->adapter->intToBin($this->opts['pos_int']);
        $this->assertEquals($this->opts['pos_bin'], $gmpBin);

        // positive, two's compliment
        $gmpBin = $this->adapter->intToBin($this->opts['pos_int'], true);
        $this->assertEquals($this->opts['pos_bin_twoc'], $gmpBin);

        // negative, no two's compliment
        $gmpBin = $this->adapter->intToBin('-' . $this->opts['pos_int']);
        $this->assertEquals($this->opts['pos_bin'], $gmpBin);

        // negative, two's compliment
        $gmpBin = $this->adapter->intToBin('-' . $this->opts['pos_int'], true);
        $this->assertEquals($this->opts['neg_bin_twoc'], $gmpBin);

    }

    public function testBinaryToIntegerConversion()
    {
        // zero
        $gmpInteger = $this->adapter->binToInt(chr(0));
        $this->assertEquals('0', $gmpInteger);

        // positive
        $gmpInt = $this->adapter->binToInt($this->opts['pos_bin']);
        $this->assertEquals($this->opts['pos_int'], $gmpInt);

        // positive, two's compliment
        $gmpInt = $this->adapter->binToInt($this->opts['pos_bin_twoc'], true);
        $this->assertEquals($this->opts['pos_int'], $gmpInt);

        // negative, two's compliment
        $gmpInt = $this->adapter->binToInt($this->opts['neg_bin_twoc'], true);
        $this->assertEquals('-' . $this->opts['pos_int'], $gmpInt);
    }

    public function testBaseConversion()
    {
        $dec = '1234567890';
        $this->assertEquals('1001001100101100000001011010010',
                            $this->adapter->baseConvert($dec, 10, 2));
        $this->assertEquals('11145401322',
                            $this->adapter->baseConvert($dec, 10, 8));
        $this->assertEquals('499602d2',
                            $this->adapter->baseConvert($dec, 10, 16));
        $this->assertEquals('kf12oi',
                            $this->adapter->baseConvert($dec, 10, 36));
        $this->assertEquals('1ly7vk',
                            $this->adapter->baseConvert($dec, 10, 62));

    }

    public function testBaseConversionTwoWay()
    {
        for ($b = 2; $b <= 62; $b++) {
            $x = $this->adapter->baseConvert('1234567890', 10, $b);
            $y = $this->adapter->baseConvert($x, $b, 10);
            $this->assertEquals('1234567890', $y);
        }
    }
}