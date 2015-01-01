<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt\Symmetric\Padding;

use Zend\Crypt\Symmetric\Padding\NoPadding;

class NoPaddingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NoPadding
     */
    protected $padding;

    public function setUp()
    {
        $this->padding = new NoPadding();
    }

    public function testPad()
    {
        $string = 'test';
        for ($size=0; $size<10; $size++) {
            $this->assertEquals($string, $this->padding->pad($string, $size));
        }
    }

    public function testStrip()
    {
        $string = 'test';
        $this->assertEquals($string, $this->padding->strip($string));
    }
}
