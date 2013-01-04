<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace ZendTest\Crypt\Symmetric\Padding;

use Zend\Crypt\Symmetric\Padding\Pkcs7;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 */
class Pkcs7Test extends \PHPUnit_Framework_TestCase
{
    /** @var Pkcs7 */
    public $padding;
    /** @var integer */
    public $start;
    /** @var integer */
    public $end;

    public function setUp()
    {
        $this->padding = new Pkcs7();
        $this->start   = 1;
        $this->end     = 32;
    }

    public function testPad()
    {
        for ($blockSize = $this->start; $blockSize <= $this->end; $blockSize++) {
            for ($i = 1; $i <= $blockSize; $i++) {
                $input  = str_repeat(chr(rand(0, 255)), $i);
                $output = $this->padding->pad($input, $blockSize);
                $num    = $blockSize - ($i % $blockSize);
                $this->assertEquals($output, $input . str_repeat(chr($num), $num));
            }
        }
    }

    public function testStrip()
    {
        for ($blockSize = $this->start; $blockSize <= $this->end; $blockSize++) {
            for ($i = 1; $i < $blockSize; $i++) {
                $input  = str_repeat('a', $i);
                $num    = $blockSize - ($i % $blockSize);
                $output = $this->padding->strip($input . str_repeat(chr($num), $num));
                $this->assertEquals($output, $input);
            }
        }
    }
}
