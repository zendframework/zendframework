<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\Util;

use Zend\Amf\Util;

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @group      Zend_Amf
 */
class BinaryStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testConstructorShouldThrowExceptionForInvalidStream()
    {
        $test = new Util\BinaryStream(array('foo', 'bar'));
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testReadBytesShouldRaiseExceptionForBufferUnderrun()
    {
        $string = 'this is a short stream';
        $stream = new Util\BinaryStream($string);
        $length = strlen($string);
        $test   = $stream->readBytes(10 * $length);
    }

    public function testReadBytesShouldReturnSubsetOfStringFromCurrentNeedle()
    {
        $string = 'this is a short stream';
        $stream = new Util\BinaryStream($string);
        $test   = $stream->readBytes(4);
        $this->assertEquals('this', $test);
        $test   = $stream->readBytes(5);
        $this->assertEquals(' is a', $test);
    }

    public function testBinaryStreamsShouldAllowWritingUtf8()
    {
        $string = str_repeat('赵勇', 1000);
        $stream = new Util\BinaryStream('');
        $stream->writeLongUtf($string);
        $test = $stream->getStream();
        $this->assertContains($string, $test);
    }
}
