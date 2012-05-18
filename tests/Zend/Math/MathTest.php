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
    public function testRandBytes()
    {
        for ($length=1; $length<64; $length++) {
            $rand = Math::randBytes($length);
            $this->assertTrue(!empty($rand));
            $this->assertEquals(strlen($rand), $length);
        }
    }
       
    public function testRand()
    {
        for ($i=0; $i<100; $i++) {
            $min = mt_rand(1,10000);
            $max = $min + mt_rand(1,10000);
            $rand = Math::rand($min, $max);
            $this->assertTrue(($rand >= $min) && ($rand <= $max));
        }
    }
    
    public function testRandBigInteger()
    {
        try {
            $math = new \Zend\Math\BigInteger();
        } catch (\Zend\Math\Exception\ExceptionInterface $e) {
            if (strpos($e->getMessage(), 'big integer precision math support not detected') !== false) {
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
