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

namespace ZendTest\Crypt\Symmetric\Padding;

use Zend\Crypt\Symmetric\Padding\Pkcs7;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
