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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Writer;

use \Zend\Log\Writer\Stream as StreamWriter,
    \Zend\Log\Logger,
    \Zend\Log\Formatter\Simple as SimpleFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class StreamWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorThrowsWhenResourceIsNotStream()
    {
        $resource = xml_parser_create();
        try {
            new StreamWriter($resource);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\Log\Exception\InvalidArgumentException', $e);
            $this->assertRegExp('/not a stream/i', $e->getMessage());
        }
        xml_parser_free($resource);
    }

    public function testConstructorWithValidStream()
    {
        $stream = fopen('php://memory', 'w+');
        new StreamWriter($stream);
    }

    public function testConstructorWithValidUrl()
    {
        new StreamWriter('php://memory');
    }

    public function testConstructorThrowsWhenModeSpecifiedForExistingStream()
    {
        $stream = fopen('php://memory', 'w+');
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'existing stream');
        new StreamWriter($stream, 'w+');
    }

    public function testConstructorThrowsWhenStreamCannotBeOpened()
    {
        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'cannot be opened');
        new StreamWriter('');
    }

    public function testWrite()
    {
        $stream = fopen('php://memory', 'w+');
        $fields = array('message' => 'message-to-log');

        $writer = new StreamWriter($stream);
        $writer->write($fields);

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $this->assertContains($fields['message'], $contents);
    }

    public function testWriteThrowsWhenStreamWriteFails()
    {
        $stream = fopen('php://memory', 'w+');
        $writer = new StreamWriter($stream);
        fclose($stream);

        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'Unable to write');
        $writer->write(array('message' => 'foo'));
    }

    public function testShutdownClosesStreamResource()
    {
        $writer = new StreamWriter('php://memory', 'w+');
        $writer->write(array('message' => 'this write should succeed'));

        $writer->shutdown();

        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'Unable to write');
        $writer->write(array('message' => 'this write should fail'));
    }

    public function testSettingNewFormatter()
    {
        $stream = fopen('php://memory', 'w+');
        $writer = new StreamWriter($stream);
        $expected = 'foo';

        $formatter = new SimpleFormatter($expected);
        $writer->setFormatter($formatter);

        $writer->write(array('bar'=>'baz'));
        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $this->assertContains($expected, $contents);
    }

    public function testFactoryStream()
    {
        $cfg = array('log' => array('memory' => array(
            'writerName'   => "Mock",
            'writerParams' => array(
                'stream' => 'php://memory',
                'mode'   => 'a'
            )
        )));

        $logger = Logger::factory($cfg['log']);
        $this->assertTrue($logger instanceof Logger);
    }

    public function testFactoryUrl()
    {
        $cfg = array('log' => array('memory' => array(
            'writerName'   => "Mock",
            'writerParams' => array(
                'url'  => 'http://localhost',
                'mode' => 'a'
            )
        )));

        $logger = Logger::factory($cfg['log']);
        $this->assertTrue($logger instanceof Logger);
    }
}
