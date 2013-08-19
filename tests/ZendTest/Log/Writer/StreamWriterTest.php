<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Writer;

use Zend\Log\Writer\Stream as StreamWriter;
use Zend\Log\Formatter\Simple as SimpleFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
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

    public function testAllowSpecifyingLogSeparator()
    {
        $stream = fopen('php://memory', 'w+');
        $writer = new StreamWriter($stream);
        $writer->setLogSeparator('::');

        $fields = array('message' => 'message1');
        $writer->write($fields);
        $fields['message'] = 'message2';
        $writer->write($fields);

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $this->assertRegexp('/message1.*?::.*?message2/', $contents);
        $this->assertNotContains(PHP_EOL, $contents);
    }

    public function testAllowsSpecifyingLogSeparatorAsConstructorArgument()
    {
        $writer = new StreamWriter('php://memory', 'w+', '::');
        $this->assertEquals('::', $writer->getLogSeparator());
    }

    public function testAllowsSpecifyingLogSeparatorWithinArrayPassedToConstructor()
    {
        $options = array(
            'stream'        => 'php://memory',
            'mode'          => 'w+',
            'log_separator' => '::',
        );
        $writer = new StreamWriter($options);
        $this->assertEquals('::', $writer->getLogSeparator());
    }

    public function testConstructWithOptions()
    {
        $formatter = new \Zend\Log\Formatter\Simple();
        $filter    = new \Zend\Log\Filter\Mock();
        $writer = new StreamWriter(array(
                'filters'   => $filter,
                'formatter' => $formatter,
                'stream'        => 'php://memory',
                'mode'          => 'w+',
                'log_separator' => '::',

        ));
        $this->assertEquals('::', $writer->getLogSeparator());
        $this->assertAttributeEquals($formatter, 'formatter', $writer);

        $filters = self::readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertEquals($filter, $filters[0]);
    }

    public function testDefaultFormatter()
    {
        $writer = new StreamWriter('php://memory');
        $this->assertAttributeInstanceOf('Zend\Log\Formatter\Simple', 'formatter', $writer);
    }
}
