<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Bytes as BytesFilter;

/**
 * @group      Zend_Filter
 */
class BytesTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultOptions()
    {
        $filter = new BytesFilter();

        $this->assertEquals(BytesFilter::MODE_DECIMAL, $filter->getMode());
        $this->assertEquals(BytesFilter::TYPE_BYTES, $filter->getType());
        $this->assertEquals(2, $filter->getPrecision());
    }

    public function testConstructorOptions()
    {
        $filter = new BytesFilter(array(
            'mode'         => BytesFilter::MODE_BINARY,
            'type'         => BytesFilter::TYPE_BITS,
            'precision'    => 3,
            'prefixes'     => array('', 'kilo'),
        ));

        $this->assertEquals(BytesFilter::MODE_BINARY, $filter->getMode());
        $this->assertEquals(BytesFilter::TYPE_BITS, $filter->getType());
        $this->assertEquals(3, $filter->getPrecision());
        $this->assertEquals(array('', 'kilo'), $filter->getPrefixes());
    }

    /**
     * @param float $value
     * @param string $expected
     * @dataProvider decimalBytesTestProvider
     */
    public function testDecimalBytes($value, $expected)
    {
        $filter = new BytesFilter(array(
            'mode' => BytesFilter::MODE_DECIMAL,
            'type' => BytesFilter::TYPE_BYTES
        ));
        $this->assertEquals($expected, $filter->filter($value));
    }

    /**
     * @param float $value
     * @param string $expected
     * @dataProvider binaryBytesTestProvider
     */
    public function testBinaryBytes($value, $expected)
    {
        $filter = new BytesFilter(array(
            'mode' => BytesFilter::MODE_BINARY,
            'type' => BytesFilter::TYPE_BYTES
        ));
        $this->assertEquals($expected, $filter->filter($value));
    }

    /**
     * @param float $value
     * @param string $expected
     * @dataProvider decimalBitsTestProvider
     */
    public function testDecimalBits($value, $expected)
    {
        $filter = new BytesFilter(array(
            'mode' => BytesFilter::MODE_DECIMAL,
            'type' => BytesFilter::TYPE_BITS
        ));
        $this->assertEquals($expected, $filter->filter($value));
    }

    /**
     * @param float $value
     * @param string $expected
     * @dataProvider binaryBitsTestProvider
     */
    public function testBinaryBits($value, $expected)
    {
        $filter = new BytesFilter(array(
            'mode' => BytesFilter::MODE_BINARY,
            'type' => BytesFilter::TYPE_BITS
        ));
        $this->assertEquals($expected, $filter->filter($value));
    }

    public function testPrecision()
    {
        $filter = new BytesFilter(array(
            'precision' => 3,
        ));

        $this->assertEquals('1.500kB', $filter->filter(1500));
    }

    public function testCustomPrefixes()
    {
        $filter = new BytesFilter(array(
            'prefixes' => array('', 'kilos'),
        ));

        $this->assertEquals('1.50kilosB', $filter->filter(1500));
    }

    public function testSettingFalseMode()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException');
        $filter = new BytesFilter(array(
            'mode' => 'invalid',
        ));
    }

    public function testSettingFalseType()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException');
        $filter = new BytesFilter(array(
            'type' => 'invalid',
        ));
    }

    public static function decimalBytesTestProvider()
    {
        return array(
            array(0, '0B'),
            array(1, '1.00B'),
            array(pow(1000, 1), '1.00kB'),
            array(pow(1500, 1), '1.50kB'),
            array(pow(1000, 2), '1.00MB'),
            array(pow(1000, 3), '1.00GB'),
            array(pow(1000, 4), '1.00TB'),
            array(pow(1000, 5), '1.00PB'),
            array(pow(1000, 6), '1.00EB'),
            array(pow(1000, 7), '1.00ZB'),
            array(pow(1000, 8), '1.00YB'),
            array(pow(1000, 9), (pow(1000, 9) . 'B')),
        );
    }

    public static function binaryBytesTestProvider()
    {
        return array(
            array(0, '0B'),
            array(1, '1.00B'),
            array(pow(1024, 1), '1.00KiB'),
            array(pow(1536, 1), '1.50KiB'),
            array(pow(1024, 2), '1.00MiB'),
            array(pow(1024, 3), '1.00GiB'),
            array(pow(1024, 4), '1.00TiB'),
            array(pow(1024, 5), '1.00PiB'),
            array(pow(1024, 6), '1.00EiB'),
            array(pow(1024, 7), '1.00ZiB'),
            array(pow(1024, 8), '1.00YiB'),
            array(pow(1024, 9), (pow(1024, 9) . 'B')),
        );
    }

    public static function decimalBitsTestProvider()
    {
        return array(
            array(0, '0b'),
            array(1, '1.00b'),
            array(pow(1000, 1), '1.00kb'),
            array(pow(1500, 1), '1.50kb'),
            array(pow(1000, 2), '1.00Mb'),
            array(pow(1000, 3), '1.00Gb'),
            array(pow(1000, 4), '1.00Tb'),
            array(pow(1000, 5), '1.00Pb'),
            array(pow(1000, 6), '1.00Eb'),
            array(pow(1000, 7), '1.00Zb'),
            array(pow(1000, 8), '1.00Yb'),
            array(pow(1000, 9), (pow(1000, 9) . 'b')),
        );
    }

    public static function binaryBitsTestProvider()
    {
        return array(
            array(0, '0b'),
            array(1, '1.00b'),
            array(pow(1024, 1), '1.00Kib'),
            array(pow(1536, 1), '1.50Kib'),
            array(pow(1024, 2), '1.00Mib'),
            array(pow(1024, 3), '1.00Gib'),
            array(pow(1024, 4), '1.00Tib'),
            array(pow(1024, 5), '1.00Pib'),
            array(pow(1024, 6), '1.00Eib'),
            array(pow(1024, 7), '1.00Zib'),
            array(pow(1024, 8), '1.00Yib'),
            array(pow(1024, 9), (pow(1024, 9) . 'b')),
        );
    }
}
