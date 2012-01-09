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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Mail\Storage;

use Zend\Mime,
    Zend\Mail\Storage,
    Zend\Mail\Storage\Exception,
    Zend\Mail\Storage\Message;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    protected $_file;

    public function setUp()
    {
        $this->_file = __DIR__ . '/../_files/mail.txt';
    }

    public function testInvalidFile()
    {
        try {
            $message = new Message(array('file' => '/this/file/does/not/exists'));
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while loading unknown file');
    }

    public function testIsMultipart()
    {
        $message = new Message(array('file' => $this->_file));

        $this->assertTrue($message->isMultipart());
    }

    public function testGetHeader()
    {
        $message = new Message(array('file' => $this->_file));

        $this->assertEquals($message->subject, 'multipart');
    }

    public function testGetDecodedHeader()
    {
        $message = new Message(array('file' => $this->_file));

        $this->assertEquals($message->from, iconv('UTF-8', iconv_get_encoding('internal_encoding'),
                                                                   '"Peter Müller" <peter-mueller@example.com>'));
    }

    public function testGetHeaderAsArray()
    {
        $message = new Message(array('file' => $this->_file));

        $this->assertEquals($message->getHeader('subject', 'array'), array('multipart'));
    }

    public function testGetHeaderFromOpenFile()
    {
        $fh = fopen($this->_file, 'r');
        $message = new Message(array('file' => $fh));

        $this->assertEquals($message->subject, 'multipart');
    }

    public function testGetFirstPart()
    {
        $message = new Message(array('file' => $this->_file));

        $this->assertEquals(substr($message->getPart(1)->getContent(), 0, 14), 'The first part');
    }

    public function testGetFirstPartTwice()
    {
        $message = new Message(array('file' => $this->_file));

        $message->getPart(1);
        $this->assertEquals(substr($message->getPart(1)->getContent(), 0, 14), 'The first part');
    }


    public function testGetWrongPart()
    {
        $message = new Message(array('file' => $this->_file));

        try {
            $message->getPart(-1);
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while fetching unknown part');
    }

    public function testNoHeaderMessage()
    {
        $message = new Message(array('file' => __FILE__));

        $this->assertEquals(substr($message->getContent(), 0, 5), '<?php');

        $raw = file_get_contents(__FILE__);
        $raw = "\t" . $raw;
        $message = new Message(array('raw' => $raw));

        $this->assertEquals(substr($message->getContent(), 0, 6), "\t<?php");
    }

    public function testMultipleHeader()
    {
        $raw = file_get_contents($this->_file);
        $raw = "sUBject: test\nSubJect: test2\n" . $raw;
        $message = new Message(array('raw' => $raw));

        $this->assertEquals($message->getHeader('subject', 'string'),
                           'test' . Mime\Mime::LINEEND . 'test2' . Mime\Mime::LINEEND .  'multipart');
        $this->assertEquals($message->getHeader('subject'),  array('test', 'test2', 'multipart'));
    }

    public function testContentTypeDecode()
    {
        $message = new Message(array('file' => $this->_file));

        $this->assertEquals(Mime\Decode::splitContentType($message->ContentType),
                            array('type' => 'multipart/alternative', 'boundary' => 'crazy-multipart'));
    }

    public function testSplitEmptyMessage()
    {
        $this->assertEquals(Mime\Decode::splitMessageStruct('', 'xxx'), null);
    }

    public function testSplitInvalidMessage()
    {
        try {
            Mime\Decode::splitMessageStruct("--xxx\n", 'xxx');
        } catch (\Zend\Mime\Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while decoding invalid message');
    }

    public function testInvalidMailHandler()
    {
        try {
            $message = new Message(array('handler' => 1));
        } catch (Exception\InvalidArgumentException $e) {
            return; // ok
        }

        $this->fail('no exception raised while using invalid mail handler');

    }

    public function testMissingId()
    {
        $mail = new Storage\Mbox(array('filename' => __DIR__ . '/../_files/test.mbox/INBOX'));

        try {
            $message = new Message(array('handler' => $mail));
        } catch (Exception\InvalidArgumentException $e) {
            return; // ok
        }

        $this->fail('no exception raised while mail handler without id');

    }

    public function testIterator()
    {
        $message = new Message(array('file' => $this->_file));
        foreach (new \RecursiveIteratorIterator($message) as $num => $part) {
            if ($num == 1) {
                // explicit call of __toString() needed for PHP < 5.2
                $this->assertEquals(substr($part->__toString(), 0, 14), 'The first part');
            }
        }
        $this->assertEquals($part->contentType, 'text/x-vertical');
    }

    public function testDecodeString()
    {
        $is = Mime\Decode::decodeQuotedPrintable('=?UTF-8?Q?"Peter M=C3=BCller"?= <peter-mueller@example.com>');
        $should = iconv('UTF-8', iconv_get_encoding('internal_encoding'),
                        '"Peter Müller" <peter-mueller@example.com>');
        $this->assertEquals($is, $should);
    }

    public function testSplitHeader()
    {
        $header = 'foo; x=y; y="x"';
        $this->assertEquals(Mime\Decode::splitHeaderField($header), array('foo', 'x' => 'y', 'y' => 'x'));
        $this->assertEquals(Mime\Decode::splitHeaderField($header, 'x'), 'y');
        $this->assertEquals(Mime\Decode::splitHeaderField($header, 'y'), 'x');
        $this->assertEquals(Mime\Decode::splitHeaderField($header, 'foo', 'foo'), 'foo');
        $this->assertEquals(Mime\Decode::splitHeaderField($header, 'foo'), null);
    }

    public function testSplitInvalidHeader()
    {
        $header = '';
        try {
            Mime\Decode::splitHeaderField($header);
        } catch (\Zend\Mime\Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while decoding invalid header field');
    }

    public function testSplitMessage()
    {
        $header = 'Test: test';
        $body   = 'body';
        $newlines = array("\r\n", "\n\r", "\n", "\r");

        $decoded_body   = null; // "Declare" variable befor first "read" usage to avoid IDEs warning
        $decoded_header = null; // "Declare" variable befor first "read" usage to avoid IDEs warning

        foreach ($newlines as $contentEOL) {
            foreach ($newlines as $decodeEOL) {
                $content = $header . $contentEOL . $contentEOL . $body;
                $decoded = Mime\Decode::splitMessage($content, $decoded_header, $decoded_body, $decodeEOL);
                $this->assertEquals(array('test' => 'test'), $decoded_header);
                $this->assertEquals($body, $decoded_body);
            }
        }
    }

    public function testToplines()
    {
        $message = new Message(array('headers' => file_get_contents($this->_file)));
        $this->assertTrue(strpos($message->getToplines(), 'multipart message') === 0);
    }

    public function testNoContent()
    {
        $message = new Message(array('raw' => 'Subject: test'));

        try {
            $message->getContent();
        } catch (Exception\RuntimeException $e) {
            return; // ok
        }

        $this->fail('no exception raised while getting content of message without body');
    }

    public function testEmptyHeader()
    {
        $message = new Message(array());
        $this->assertEquals(array(), $message->getHeaders());

        $message = new Message(array());
        $subject = null;
        try {
            $subject = $message->subject;
        } catch (Exception\InvalidArgumentException $e) {
            // ok
        }
        if ($subject) {
            $this->fail('no exception raised while getting header from empty message');
        }
    }

    public function testEmptyBody()
    {
        $message = new Message(array());
        $part = null;
        try {
            $part = $message->getPart(1);
        } catch (Exception\RuntimeException $e) {
            // ok
        }
        if ($part) {
            $this->fail('no exception raised while getting part from empty message');
        }

        $message = new Message(array());
        $this->assertTrue($message->countParts() == 0);
    }

    /**
     * @group ZF-5209
     */
    public function testCheckingHasHeaderFunctionality()
    {
        $message = new Message(array('headers' => array('subject' => 'foo')));

        $this->assertTrue( $message->headerExists('subject'));
        $this->assertTrue( isset($message->subject) );
        $this->assertTrue( $message->headerExists('SuBject'));
        $this->assertTrue( isset($message->suBjeCt) );
        $this->assertFalse($message->headerExists('From'));
    }

    public function testWrongMultipart()
    {
        $message = new Message(array('raw' => "Content-Type: multipart/mixed\r\n\r\ncontent"));

        try {
            $message->getPart(1);
        } catch (Exception\RuntimeException $e) {
            return; // ok
        }
        $this->fail('no exception raised while getting part from message without boundary');
    }

    public function testLateFetch()
    {
        $mail = new Storage\Mbox(array('filename' => __DIR__ . '/../_files/test.mbox/INBOX'));

        $message = new Message(array('handler' => $mail, 'id' => 5));
        $this->assertEquals($message->countParts(), 2);
        $this->assertEquals($message->countParts(), 2);

        $message = new Message(array('handler' => $mail, 'id' => 5));
        $this->assertEquals($message->subject, 'multipart');

        $message = new Message(array('handler' => $mail, 'id' => 5));
        $this->assertTrue(strpos($message->getContent(), 'multipart message') === 0);
    }

    public function testManualIterator()
    {
        $message = new Message(array('file' => $this->_file));

        $this->assertTrue($message->valid());
        $this->assertEquals($message->getChildren(), $message->current());
        $this->assertEquals($message->key(), 1);

        $message->next();
        $this->assertTrue($message->valid());
        $this->assertEquals($message->getChildren(), $message->current());
        $this->assertEquals($message->key(), 2);

        $message->next();
        $this->assertFalse($message->valid());

        $message->rewind();
        $this->assertTrue($message->valid());
        $this->assertEquals($message->getChildren(), $message->current());
        $this->assertEquals($message->key(), 1);
    }

    public function testMessageFlagsAreSet()
    {
        $origFlags = array(
            'foo' => 'bar',
            'baz' => 'bat'
        );
        $message = new Message(array('flags' => $origFlags));

        $messageFlags = $message->getFlags();
        $this->assertTrue($message->hasFlag('bar'), var_export($messageFlags, 1));
        $this->assertTrue($message->hasFlag('bat'), var_export($messageFlags, 1));
        $this->assertEquals(array('bar' => 'bar', 'bat' => 'bat'), $messageFlags);
    }

    public function testGetHeaderFieldSingle()
    {
        $message = new Message(array('file' => $this->_file));
        $this->assertEquals($message->getHeaderField('subject'), 'multipart');
    }

    public function testGetHeaderFieldDefault()
    {
        $message = new Message(array('file' => $this->_file));
        $this->assertEquals($message->getHeaderField('content-type'), 'multipart/alternative');
    }

    public function testGetHeaderFieldNamed()
    {
        $message = new Message(array('file' => $this->_file));
        $this->assertEquals($message->getHeaderField('content-type', 'boundary'), 'crazy-multipart');
    }

    public function testGetHeaderFieldMissing()
    {
        $message = new Message(array('file' => $this->_file));
        $this->assertNull($message->getHeaderField('content-type', 'foo'));
    }

    public function testGetHeaderFieldInvalid()
    {
        $message = new Message(array('file' => $this->_file));
        try {
            $message->getHeaderField('fake-header-name', 'foo');
        } catch (\Zend\Mail\Exception $e) {
            return;
        }
        $this->fail('No exception thrown while requesting invalid field name');
    }

    public function testCaseInsensitiveMultipart()
    {
        $message = new Message(array('raw' => "coNTent-TYpe: muLTIpaRT/x-empty\r\n\r\n"));
        $this->assertTrue($message->isMultipart());
    }

    public function testCaseInsensitiveField()
    {
        $header = 'test; fOO="this is a test"';
        $this->assertEquals(Mime\Decode::splitHeaderField($header, 'Foo'), 'this is a test');
        $this->assertEquals(Mime\Decode::splitHeaderField($header, 'bar'), null);
    }

    public function testSpaceInFieldName()
    {
        $header = 'test; foo =bar; baz      =42';
        $this->assertEquals(Mime\Decode::splitHeaderField($header, 'foo'), 'bar');
        $this->assertEquals(Mime\Decode::splitHeaderField($header, 'baz'), 42);
    }
}
