<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mime;

use Zend\Mime;

/**
 * @group      Zend_Mime
 */
class PartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * MIME part test object
     *
     * @var Mime\Part
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
        $fp = fopen($testfile, 'rb');
        $this->assertInternalType('resource', $fp);
        $part = new Mime\Part($fp);
        $part->encoding = Mime\Mime::ENCODING_BASE64;
        $fp2 = $part->getEncodedStream();
        $this->assertInternalType('resource', $fp2);
        $encoded = stream_get_contents($fp2);
        fclose($fp);
        $this->assertEquals(base64_decode($encoded), $original);

        // test QuotedPrintable
        $fp = fopen($testfile, 'rb');
        $this->assertInternalType('resource', $fp);
        $part = new Mime\Part($fp);
        $part->encoding = Mime\Mime::ENCODING_QUOTEDPRINTABLE;
        $fp2 = $part->getEncodedStream();
        $this->assertInternalType('resource', $fp2);
        $encoded = stream_get_contents($fp2);
        fclose($fp);
        $this->assertEquals(quoted_printable_decode($encoded), $original);
    }

    /**
     * @group ZF-1491
     */
    public function testGetRawContentFromPart()
    {
        $this->assertEquals($this->testText, $this->part->getRawContent());
    }

    /**
     * @link https://github.com/zendframework/zf2/issues/5428
     * @group 5428
     */
    public function testContentEncodingWithStreamReadTwiceINaRow()
    {
        $testfile = realpath(__FILE__);
        $original = file_get_contents($testfile);

        $fp = fopen($testfile, 'rb');
        $part = new Mime\Part($fp);
        $part->encoding = Mime\Mime::ENCODING_BASE64;
        $contentEncodedFirstTime  = $part->getContent();
        $contentEncodedSecondTime = $part->getContent();
        $this->assertEquals($contentEncodedFirstTime, $contentEncodedSecondTime);
        fclose($fp);

        $fp = fopen($testfile, 'rb');
        $part = new Mime\Part($fp);
        $part->encoding = Mime\Mime::ENCODING_QUOTEDPRINTABLE;
        $contentEncodedFirstTime  = $part->getContent();
        $contentEncodedSecondTime = $part->getContent();
        $this->assertEquals($contentEncodedFirstTime, $contentEncodedSecondTime);
        fclose($fp);
    }

    public function testSettersGetters()
    {
        $part = new Mime\Part();
        $part->setContent($this->testText)
             ->setEncoding(Mime\Mime::ENCODING_8BIT)
             ->setType('text/plain')
             ->setFilename('test.txt')
             ->setDisposition('attachment')
             ->setCharset('iso8859-1')
             ->setId('4711')
             ->setBoundary('frontier')
             ->setLocation('fiction1/fiction2')
             ->setLanguage('en')
             ->setIsStream(false)
             ->setFilters(array('foo'))
             ->setDescription('foobar');

        $this->assertEquals($this->testText, $part->getContent());
        $this->assertEquals(Mime\Mime::ENCODING_8BIT, $part->getEncoding());
        $this->assertEquals('text/plain', $part->getType());
        $this->assertEquals('test.txt', $part->getFileName());
        $this->assertEquals('attachment', $part->getDisposition());
        $this->assertEquals('iso8859-1', $part->getCharset());
        $this->assertEquals('4711', $part->getId());
        $this->assertEquals('frontier', $part->getBoundary());
        $this->assertEquals('fiction1/fiction2', $part->getLocation());
        $this->assertEquals('en', $part->getLanguage());
        $this->assertEquals(false, $part->isStream());
        $this->assertEquals(array('foo'), $part->getFilters());
        $this->assertEquals('foobar', $part->getDescription());
    }

    public function testConstructGetInvalidArgumentException()
    {
        $this->setExpectedException('Zend\Mime\Exception\InvalidArgumentException');
        $part = new Mime\Part(1);
    }

    public function testSetContentGetInvalidArgumentException()
    {
        $this->setExpectedException('Zend\Mime\Exception\InvalidArgumentException');
        $part = new Mime\Part();
        $part->setContent(1);
    }
}
