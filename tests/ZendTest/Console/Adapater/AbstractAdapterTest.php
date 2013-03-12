<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Console\Adapater;

use ZendTest\Console\TestAssets\ConsoleAdapter;

/**
 * @group      Zend_Console
 */
class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConsoleAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new ConsoleAdapter();
        $this->adapter->stream = fopen('php://memory', 'w+');
    }

    public function tearDown()
    {
        fclose($this->adapter->stream);
    }

    public function testWriteChar()
    {
        ob_start();
        $this->adapter->write('foo');
        $this->assertEquals('foo', ob_get_clean());
    }

    public function testWriteText()
    {
        ob_start();
        $this->adapter->writeText('foo');
        $this->assertEquals('foo', ob_get_clean());
    }

    public function testWriteLine()
    {
        ob_start();
        $this->adapter->writeLine('foo');
        $this->assertEquals("foo\n", ob_get_clean());

        ob_start();
        $this->adapter->writeLine("foo\nbar");
        $this->assertEquals("foo bar\n", ob_get_clean());

        ob_start();
        $this->adapter->writeLine("\rfoo\r");
        $this->assertEquals("foo\n", ob_get_clean());
    }

    public function testReadLine()
    {
        fwrite($this->adapter->stream, 'baz');

        $line = $this->adapter->readLine();
        $this->assertEquals($line, 'baz');
    }

    public function testReadLineWithLimit()
    {
        fwrite($this->adapter->stream, 'baz, bar, foo');

        $line = $this->adapter->readLine(6);
        $this->assertEquals($line, 'baz, b');
    }

    public function testReadChar()
    {
        fwrite($this->adapter->stream, 'bar');

        $char = $this->adapter->readChar();
        $this->assertEquals($char, 'b');
    }

    public function testReadCharWithMask()
    {
        fwrite($this->adapter->stream, 'bar');

        $char = $this->adapter->readChar('ar');
        $this->assertEquals($char, 'a');
    }

    public function testReadCharWithMaskInsensitiveCase()
    {
        fwrite($this->adapter->stream, 'bAr');

        $char = $this->adapter->readChar('ar');
        $this->assertEquals($char, 'r');
    }
}
