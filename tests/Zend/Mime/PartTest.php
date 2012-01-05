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
 * @package    Zend_Mime
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Mime;
use Zend\Mime;

/**
 * @category   Zend
 * @package    Zend_Mime
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mime
 */
class PartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * MIME part test object
     *
     * @var Zend_Mime_Part
     */
    protected $part = null;
    protected $testText;

    protected function setUp()
    {
        $this->testText = 'safdsafsa�lg ��gd�� sd�jg�sdjg�ld�gksd�gj�sdfg�dsj�gjsd�gj�dfsjg�dsfj�djs�g kjhdkj '
                       . 'fgaskjfdh gksjhgjkdh gjhfsdghdhgksdjhg';
        $this->part = new Mime\Part($this->testText);
        $this->part->encoding = Mime\Mime::ENCODING_BASE64;
        $this->part->type = "text/plain";
        $this->part->filename = 'test.txt';
        $this->part->disposition = 'attachment';
        $this->part->charset = 'iso8859-1';
        $this->part->id = '4711';
    }

    public function testHeaders()
    {
        $expectedHeaders = array('Content-Type: text/plain',
                                 'Content-Transfer-Encoding: ' . Mime\Mime::ENCODING_BASE64,
                                 'Content-Disposition: attachment',
                                 'filename="test.txt"',
                                 'charset=iso8859-1',
                                 'Content-ID: <4711>');

        $actual = $this->part->getHeaders();

        foreach ($expectedHeaders as $expected) {
            $this->assertContains($expected, $actual);
        }
    }

    public function testContentEncoding()
    {
        // Test with base64 encoding
        $content = $this->part->getContent();
        $this->assertEquals($this->testText, base64_decode($content));
        // Test with quotedPrintable Encoding:
        $this->part->encoding = Mime\Mime::ENCODING_QUOTEDPRINTABLE;
        $content = $this->part->getContent();
        $this->assertEquals($this->testText, quoted_printable_decode($content));
        // Test with 8Bit encoding
        $this->part->encoding = Mime\Mime::ENCODING_8BIT;
        $content = $this->part->getContent();
        $this->assertEquals($this->testText, $content);
    }

    public function testStreamEncoding()
    {
        $testfile = realpath(__FILE__);
        $original = file_get_contents($testfile);

        // Test Base64
        $fp = fopen($testfile,'rb');
        $this->assertTrue(is_resource($fp));
        $part = new Mime\Part($fp);
        $part->encoding = Mime\Mime::ENCODING_BASE64;
        $fp2 = $part->getEncodedStream();
        $this->assertTrue(is_resource($fp2));
        $encoded = stream_get_contents($fp2);
        fclose($fp);
        $this->assertEquals(base64_decode($encoded),$original);

        // test QuotedPrintable
        $fp = fopen($testfile,'rb');
        $this->assertTrue(is_resource($fp));
        $part = new Mime\Part($fp);
        $part->encoding = Mime\Mime::ENCODING_QUOTEDPRINTABLE;
        $fp2 = $part->getEncodedStream();
        $this->assertTrue(is_resource($fp2));
        $encoded = stream_get_contents($fp2);
        fclose($fp);
        $this->assertEquals(quoted_printable_decode($encoded),$original);
    }

    /**
     * @group ZF-1491
     */
    public function testGetRawContentFromPart()
    {
        $this->assertEquals($this->testText, $this->part->getRawContent());
    }
}
