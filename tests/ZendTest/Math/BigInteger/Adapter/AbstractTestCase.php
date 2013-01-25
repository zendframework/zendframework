<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace ZendTest\Math\BigInteger\Adapter;

use Zend\Math\BigInteger\Adapter\AdapterInterface;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdapterInterface
     */
    protected $adapter = null;

    /**
     * @param string $operand
     * @param string $expected
     * @dataProvider validInitProvider
     */
    public function testInit($operand, $expected)
    {
        $this->assertEquals($expected, $this->adapter->init($operand));
    }

    /**
     * @param string $operand
     * @dataProvider invalidInitProvider
     */
    public function testInitReturnsFalse($operand)
    {
        $this->assertFalse($this->adapter->init($operand));
    }

    /**
     * @param string $operation
     * @param string $op1
     * @param string $op2
     * @param string $expected
     * @dataProvider basicCalcProvider
     */
    public function testBasicCalc($operation, $op1, $op2, $expected)
    {
        $result = '';
        switch ($operation) {
            case 'add':
                $result = $this->adapter->add($op1, $op2);
                break;
            case 'sub':
                $result = $this->adapter->sub($op1, $op2);
                break;
            case 'mul':
                $result = $this->adapter->mul($op1, $op2);
                break;
            case 'div':
                $result = $this->adapter->div($op1, $op2);
                break;
            case 'pow':
                $result = $this->adapter->pow($op1, $op2);
                break;
            case 'mod':
                $result = $this->adapter->mod($op1, $op2);
                break;
        }

        $this->assertEquals($expected, $result, "Operation [{$op1} {$operation} {$op2}] has failed");
    }

    /**
     * @param string $op
     * @param string $expected
     * @dataProvider sqrtProvider
     */
    public function testSqrt($op, $expected)
    {
        $this->assertEquals($expected, $this->adapter->sqrt($op));
    }

    /**
     * @param string $op1
     * @param string $op2
     * @param string $mod
     * @param string $expected
     * @dataProvider powmodProvider
     */
    public function testPowMod($op1, $op2, $mod, $expected)
    {
        $this->assertEquals($expected, $this->adapter->powmod($op1, $op2, $mod));
    }

    /**
     * @param string $op
     * @param string $expected
     * @dataProvider absProvider
     */
    public function testAbs($op, $expected)
    {
        $this->assertEquals($expected, $this->adapter->abs($op));
    }

    /**
     * @param string $op1
     * @param string $op2
     * @param string $expected
     * @dataProvider comparisonProvider
     */
    public function testComparison($op1, $op2, $expected)
    {
        $this->assertEquals($expected, $this->adapter->comp($op1, $op2));
    }

    /**
     * @param string $op
     * @param string $baseFrom
     * @param string $baseTo
     * @param string $expected
     * @dataProvider baseConversionProvider
     */
    public function testBaseConversion($op, $baseFrom, $baseTo, $expected)
    {
        $this->assertEquals($expected, $this->adapter->baseConvert($op, $baseFrom, $baseTo));
    }

    /**
     * @param string $op
     * @param string $bin
     * @param string $bin2c
     * @dataProvider binaryConversionProvider
     */
    public function testBinaryConversion($op, $bin, $bin2c)
    {
        $bin   = base64_decode($bin);
        $bin2c = base64_decode($bin2c);
        $opPos = ltrim($op, '-');

        $this->assertEquals($bin, $this->adapter->intToBin($op));
        $this->assertEquals($bin2c, $this->adapter->intToBin($op, true));
        $this->assertEquals($opPos, $this->adapter->binToInt($bin));
        $this->assertEquals($op, $this->adapter->binToInt($bin2c, true));
    }

    public function testDivisionByZeroRaisesException()
    {
        $this->setExpectedException('Zend\Math\BigInteger\Exception\DivisionByZeroException',
                                    'Division by zero');
        $this->adapter->div('12345', '0');
    }

    /**
     * Data provider for init() tests
     *
     * @return array
     */
    public function validInitProvider()
    {
        return array(
            array('+0', '0'),
            array('-0', '0'),
            // decimal
            array('12345678', '12345678'),
            array('-12345678', '-12345678'),
            // octal
            array('0726746425', '123456789'),
            array('-0726746425', '-123456789'),
            // hex
            array('0X75BCD15', '123456789'),
            array('0x75bcd15', '123456789'),
            array('-0X75BCD15', '-123456789'),
            // scientific notation
            array('1.23456e5', '123456'),
            array('-1.23456789e8', '-123456789'),
        );
    }

    /**
     * Data provider for init() tests
     * Expects iit() to return false on these values
     *
     * @return array
     */
    public function invalidInitProvider()
    {
        return array(
            array('zzz'),
            array('1/2'),
            array('1 + 2'),
            array('0.2E12'),
            array('1.2E-12'),
        );
    }

    /**
     * Basic calculation data provider
     * add, sub, mul, div, pow, mod
     *
     * @return array
     */
    public function basicCalcProvider()
    {
        return array(
            // addition
            array('add', '0', '12345', '12345'),
            array('add', '12345', '0', '12345'),
            array('add', '2', '2', '4'),
            array('add', '-2', '2', '0'),
            array('add', '-2', '-2', '-4'),

            // subtraction
            array('sub', '2', '0', '2'),
            array('sub', '0', '2', '-2'),
            array('sub', '2', '1', '1'),
            array('sub', '2', '-2', '4'),

            // multiplication
            array('mul', '2', '2', '4'),
            array('mul', '2', '-2', '-4'),
            array('mul', '2', '0', '0'),
            array('mul', '-2', '-2', '4'),

            // division
            array('div', '4', '2', '2'),
            array('div', '3', '2', '1'),
            array('div', '1', '2', '0'),
            array('div', '-2', '-2', '1'),

            // pow
            array('pow', '2', '2', '4'),
            array('pow', '2', '0', '1'),
            array('pow', '2', '64', '18446744073709551616'),
            array('pow', '-2', '64', '18446744073709551616'),

            // modulus
            array('mod', '3', '2', '1'),
            array('mod', '2', '2', '0'),
            array('mod', '2', '18446744073709551616', '2'),
        );
    }

    /**
     * Square root tests data provider
     *
     * @return array
     */
    public function sqrtProvider()
    {
        return array(
            array('4', '2'),
            array('4294967296', '65536'),
            array('12345678901234567890', '3513641828'), // truncated to int
        );
    }

    /**
     * Power modulus data provider
     *
     * @return array
     */
    public function powmodProvider()
    {
        return array(
            array('2', '2', '3', '1'),
        );
    }

    /**
     * abs() tests data provider
     *
     * @return array
     */
    public function absProvider()
    {
        return array(
            array('0', '0'),
            array('2', '2'),
            array('-2', '2'),
        );
    }

    /**
     * Comparison function data provider
     *
     * @return array
     */
    public function comparisonProvider()
    {
        return array(
            array('1', '0', 1),
            array('1', '1', 0),
            array('0', '1', -1),
            array('12345678901234567890', '1234567890123456789', 1),
            array('12345678901234567890', '12345678901234567890', 0),
            array('1234567890123456789', '12345678901234567890', -1),
        );
    }

    /**
     * Base conversion data provider
     *
     * @return array
     */
    public function baseConversionProvider()
    {
        return array(
            array('1234567890', 10, 2,  '1001001100101100000001011010010'),
            array('1234567890', 10, 8,  '11145401322'),
            array('1234567890', 10, 16, '499602d2'),
            array('1234567890', 10, 36, 'kf12oi'),
            array('1234567890', 10, 62, '1ly7vk'),

            // reverse
            array('1001001100101100000001011010010', 2, 10, '1234567890'),
            array('11145401322', 8, 10,  '1234567890'),
            array('499602d2',    16, 10,  '1234567890'),
            array('kf12oi',      36, 10,  '1234567890'),
            array('1ly7vk',      62, 10,  '1234567890'),
        );
    }

    /**
     * binToInt() intToBin() tests provider
     *
     * @return array
     */
    public function binaryConversionProvider()
    {
        return array(
            array(
                '0',
                'AA==',
                'AA==',
            ),
            array(
                // integer
                '1551728981814736974712322577637155399157248019669154044797077953140576293785419' .
                '1758065122742369818899372781615264663143856159582568818888995127215884267541995' .
                '0341258706556549803580104870537681476726513255747040765857479291291572334510643' .
                '245094715007229621094194349783925984760375594985848253359305585439638443',

                // binary
                '3Pk6C4g5cuwOGZiaxaLOMQ4dN3F+jZVxu3Yjcxhm5h73Wi4niYsFf5iRwuJ6Y5w/KbYIFFgc07LKOYbSaDc' .
                'FV31FwuflLcgcehcYduXOp0sUSL/frxiCjv0lGfFOReOCZjSvGUnltTXMgppIO4p2Ij5dSQolfwW9/xby+yLFg6s=',

                // binary two's complement
                'ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX+YkcLiemOcPym2CBRYHNOyyjmG0mg3B' .
                'Vd9RcLn5S3IHHoXGHblzqdLFEi/368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI+XUkKJX8Fvf8W8vsixYOr',
            ),
            array(
                '-1551728981814736974712322577637155399157248019669154044797077953140576293785419' .
                '1758065122742369818899372781615264663143856159582568818888995127215884267541995' .
                '0341258706556549803580104870537681476726513255747040765857479291291572334510643' .
                '245094715007229621094194349783925984760375594985848253359305585439638443',

                // binary
                '3Pk6C4g5cuwOGZiaxaLOMQ4dN3F+jZVxu3Yjcxhm5h73Wi4niYsFf5iRwuJ6Y5w/KbYIFFgc07LKOYbSaDc' .
                'FV31FwuflLcgcehcYduXOp0sUSL/frxiCjv0lGfFOReOCZjSvGUnltTXMgppIO4p2Ij5dSQolfwW9/xby+yLFg6s=',

                // negative binary, two's complement
                '/yMGxfR3xo0T8eZnZTpdMc7x4siOgXJqjkSJ3IznmRnhCKXR2HZ0+oBnbj0dhZxjwNZJ9+un4yxNNcZ5LZfI+q' .
                'iCuj0YGtI344Xo54kaMVi067dAIFDnfXEC2uYOsbocfZnLUOa2GkrKM31lt8R1id3Borb12oD6QgDpDQTdOnxV',
            ),
        );
    }
}
