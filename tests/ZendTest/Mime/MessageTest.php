<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mime
 */

namespace ZendTest\Mime;

use Zend\Mime;

/**
 * @category   Zend
 * @package    Zend_Mime
 * @subpackage UnitTests
 * @group      Zend_Mime
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{

    public function testMultiPart()
    {
        $msg = new Mime\Message();  // No Parts
        $this->assertFalse($msg->isMultiPart());
    }

    public function testSetGetParts()
    {
        $msg = new Mime\Message();  // No Parts
        $p = $msg->getParts();
        $this->assertTrue(is_array($p));
        $this->assertTrue(count($p) == 0);

        $p2 = array();
        $p2[] = new Mime\Part('This is a test');
        $p2[] = new Mime\Part('This is another test');
        $msg->setParts($p2);
        $p = $msg->getParts();
        $this->assertTrue(is_array($p));
        $this->assertTrue(count($p) == 2);
    }

    public function testGetMime()
    {
        $msg = new Mime\Message();  // No Parts
        $m = $msg->getMime();
        $this->assertInstanceOf('Zend\\Mime\\Mime', $m);

        $msg = new Mime\Message();  // No Parts
        $mime = new Mime\Mime('1234');
        $msg->setMime($mime);
        $m2 = $msg->getMime();
        $this->assertInstanceOf('Zend\\Mime\\Mime', $m2);
        $this->assertEquals('1234', $m2->boundary());
    }

    public function testGenerate()
    {
        $msg = new Mime\Message();  // No Parts
        $p1 = new Mime\Part('This is a test');
        $p2 = new Mime\Part('This is another test');
        $msg->addPart($p1);
        $msg->addPart($p2);
        $res = $msg->generateMessage();
        $mime = $msg->getMime();
        $boundary = $mime->boundary();
        $p1 = strpos($res, $boundary);
        // $boundary must appear once for every mime part
        $this->assertTrue($p1 !== false);
        if ($p1) {
            $p2 = strpos($res, $boundary, $p1 + strlen($boundary));
            $this->assertTrue($p2 !== false);
        }
        // check if the two test messages appear:
        $this->assertTrue(strpos($res, 'This is a test') !== false);
        $this->assertTrue(strpos($res, 'This is another test') !== false);
        // ... more in ZMailTest
    }

    /**
     * check if decoding a string into a \Zend\Mime\Message object works
     *
     */
    public function testDecodeMimeMessage()
    {
        $text = <<<EOD
This is a message in Mime Format.  If you see this, your mail reader does not support this format.

--=_af4357ef34b786aae1491b0a2d14399f
Content-Type: application/octet-stream
Content-Transfer-Encoding: 8bit

This is a test
--=_af4357ef34b786aae1491b0a2d14399f
Content-Type: image/gif
Content-Transfer-Encoding: base64
Content-ID: <12>

This is another test
--=_af4357ef34b786aae1491b0a2d14399f--
EOD;
        $res = Mime\Message::createFromMessage($text, '=_af4357ef34b786aae1491b0a2d14399f');

        $parts = $res->getParts();
        $this->assertEquals(2, count($parts));

        $part1 = $parts[0];
        $this->assertEquals('application/octet-stream', $part1->type);
        $this->assertEquals('8bit', $part1->encoding);

        $part2 = $parts[1];
        $this->assertEquals('image/gif', $part2->type);
        $this->assertEquals('base64', $part2->encoding);
        $this->assertEquals('12', $part2->id);
    }

    public function testNonMultipartMessageShouldNotRemovePartFromMessage()
    {
        $message = new Mime\Message();  // No Parts
        $part    = new Mime\Part('This is a test');
        $message->addPart($part);
        $message->generateMessage();

        $parts = $message->getParts();
        $test  = current($parts);
        $this->assertSame($part, $test);
    }
}
