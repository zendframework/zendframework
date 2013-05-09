<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace ZendTest\Math;

use Zend\Math;
use Zend\Math\Rand;
use RandomLib;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @group      Zend_Math
 */
class RandTest extends \PHPUnit_Framework_TestCase
{
    public static function provideRandInt()
    {
        return array(
            array(2, 1, 10000, 100, 0.9, 1.1, false),
            array(2, 1, 10000, 100, 0.8, 1.2, true)
        );
    }

    public function testRandBytes()
    {
        for ($length = 1; $length < 4096; $length++) {
            $rand = Rand::getBytes($length);
            $this->assertTrue($rand !== false);
            $this->assertEquals($length, strlen($rand));
        }
    }

    public function testRandBoolean()
    {
        for ($length = 1; $length < 512; $length++) {
            $rand = Rand::getBoolean();
            $this->assertTrue(is_bool($rand));
        }
    }

    /**
     * A Monte Carlo test that generates $cycles numbers from 0 to $tot
     * and test if the numbers are above or below the line y=x with a
     * frequency range of [$min, $max]
     *
     * Note: this code is inspired by the random number generator test
     * included in the PHP-CryptLib project of Anthony Ferrara
     * @see https://github.com/ircmaxell/PHP-CryptLib
     *
     * @dataProvider provideRandInt
     */
    public function testRandInteger($num, $valid, $cycles, $tot, $min, $max, $strong)
    {
        try {
            $test = Rand::getBytes(1, $strong);
        } catch (\Zend\Math\Exception\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $i     = 0;
        $count = 0;
        do {
            $up   = 0;
            $down = 0;
            for ($i = 0; $i < $cycles; $i++) {
                $x = Rand::getInteger(0, $tot, $strong);
                $y = Rand::getInteger(0, $tot, $strong);
                if ($x > $y) {
                    $up++;
                } elseif ($x < $y) {
                    $down++;
                }
            }
            $this->assertGreaterThan(0, $up);
            $this->assertGreaterThan(0, $down);
            $ratio = $up / $down;
            if ($ratio > $min && $ratio < $max) {
                $count++;
            }
            $i++;
        } while ($i < $num && $count < $valid);

        if ($count < $valid) {
            $this->fail('The random number generator failed the Monte Carlo test');
        }
    }

    public function testIntegerRangeFail()
    {
        $this->setExpectedException(
            'Zend\Math\Exception\DomainException',
            'min parameter must be lower than max parameter'
        );
        $rand = Rand::getInteger(100, 0);
    }

    public function testRandFloat()
    {
        for ($length = 1; $length < 512; $length++) {
            $rand = Rand::getFloat();
            $this->assertTrue(is_float($rand));
            $this->assertTrue(($rand >= 0 && $rand <= 1));
        }
    }

    public function testGetString()
    {
        for ($length = 1; $length < 512; $length++) {
            $rand = Rand::getString($length, '0123456789abcdef');
            $this->assertEquals(strlen($rand), $length);
            $this->assertTrue(preg_match('#^[0-9a-f]+$#', $rand) === 1);
        }
    }

    public function testGetStringBase64()
    {
        for ($length = 1; $length < 512; $length++) {
            $rand = Rand::getString($length);
            $this->assertEquals(strlen($rand), $length);
            $this->assertTrue(preg_match('#^[0-9a-zA-Z+/]+$#', $rand) === 1);
        }
    }

    public function testHashTimingSourceStrengthIsVeryLow()
    {
        $this->assertEquals(1, (string) Math\Source\HashTiming::getStrength());
    }

    public function testHashTimingSourceStrengthIsRandomWithCorrectLength()
    {
        $source = new Math\Source\HashTiming;
        $rand = $source->generate(32);
        $this->assertTrue(32 === strlen($rand));
        $rand2 = $source->generate(32);
        $this->assertNotEquals($rand, $rand2);
    }

    public function testAltGeneratorIsRandomWithCorrectLength()
    {
        $source = Math\Rand::getAlternativeGenerator();
        $rand = $source->generate(32);
        $this->assertTrue(32 === strlen($rand));
        $rand2 = $source->generate(32);
        $this->assertNotEquals($rand, $rand2);
    }
}
