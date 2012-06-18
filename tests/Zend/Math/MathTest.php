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
 * @package    Zend_Math
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Math;

use Zend\Math\Math;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Math
 */
class MathTest extends \PHPUnit_Framework_TestCase
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
        for ($length=1; $length<4096; $length++) {
            $rand = Math::randBytes($length);
            $this->assertTrue($rand !== false);
            $this->assertEquals($length, strlen($rand));
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
    public function testRandInt($num, $valid, $cycles, $tot, $min, $max, $strong)
    {
        try {
            $test = Math::randBytes(1, $strong);
        } catch (\Zend\Math\Exception\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }
        $i     = 0;
        $count = 0;
        do {
            $up   = 0;
            $down = 0;
            for ($i=0; $i<$cycles; $i++) {
                $x = Math::rand(0, $tot, $strong);
                $y = Math::rand(0, $tot, $strong);
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
    
    public function testRandBigInteger()
    {
        try {
            $math = new \Zend\Math\BigInteger\BigInteger();
        } catch (\Zend\Math\BigInteger\Exception\RuntimeException $e) {
            if (strpos($e->getMessage(), 'math support is not detected') !== false) {
                $this->markTestSkipped($e->getMessage());
            } else {
                throw $e;
            }
        }
        $math = new Math;
        $higher = '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638443';
        $lower  = '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638442';
        $result = $math->rand($lower, $higher);
        $this->assertTrue(bccomp($result, $higher) !== '1');
        $this->assertTrue(bccomp($result, $lower) !== '-1');
    }

}
