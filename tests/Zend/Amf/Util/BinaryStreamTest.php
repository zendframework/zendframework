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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_Util_BinaryStreamTest::main');
}


/**
 * Test case for Zend_Amf_Util_BinaryStream
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class Zend_Amf_Util_BinaryStreamTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Amf_Util_BinaryStreamTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * @expectedException Zend_Amf_Exception
     */
    public function testConstructorShouldThrowExceptionForInvalidStream()
    {
        $test = new Zend_Amf_Util_BinaryStream(array('foo', 'bar'));
    }

    /**
     * @expectedException Zend_Amf_Exception
     */
    public function testReadBytesShouldRaiseExceptionForBufferUnderrun()
    {
        $string = 'this is a short stream';
        $stream = new Zend_Amf_Util_BinaryStream($string);
        $length = strlen($string);
        $test   = $stream->readBytes(10 * $length);
    }

    public function testReadBytesShouldReturnSubsetOfStringFromCurrentNeedle()
    {
        $string = 'this is a short stream';
        $stream = new Zend_Amf_Util_BinaryStream($string);
        $test   = $stream->readBytes(4);
        $this->assertEquals('this', $test);
        $test   = $stream->readBytes(5);
        $this->assertEquals(' is a', $test);
    }

    public function testBinaryStreamsShouldAllowWritingUtf8()
    {
        $string = str_repeat('赵勇', 1000);
        $stream = new Zend_Amf_Util_BinaryStream('');
        $stream->writeLongUtf($string);
        $test = $stream->getStream();
        $this->assertContains($string, $test);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Amf_Util_BinaryStreamTest::main') {
    Zend_Amf_Util_BinaryStreamTest::main();
}
