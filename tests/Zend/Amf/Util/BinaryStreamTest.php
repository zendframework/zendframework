<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_Util_BinaryStreamTest::main');
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'Zend/Amf/Util/BinaryStream.php';

/**
 * Test case for Zend_Amf_Util_BinaryStream
 *
 * @package Zend_Amf
 * @subpackage UnitTests
 * @version $Id$
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
