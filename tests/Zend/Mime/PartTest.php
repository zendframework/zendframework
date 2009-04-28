<?php
/**
 * @package 	Zend_Mime
 * @subpackage  UnitTests
 */


/**
 * Zend_Mime_Part
 */
require_once 'Zend/Mime/Part.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package 	Zend_Mime
 * @subpackage  UnitTests
 */
class Zend_Mime_PartTest extends PHPUnit_Framework_TestCase
{
    /**
     * MIME part test object
     *
     * @var Zend_Mime_Part
     */
    protected $_part = null;
    protected $_testText;

    protected function setUp()
    {
        $this->_testText = 'safdsafsa�lg ��gd�� sd�jg�sdjg�ld�gksd�gj�sdfg�dsj�gjsd�gj�dfsjg�dsfj�djs�g kjhdkj '
                       . 'fgaskjfdh gksjhgjkdh gjhfsdghdhgksdjhg';
        $this->part = new Zend_Mime_Part($this->_testText);
        $this->part->encoding = Zend_Mime::ENCODING_BASE64;
        $this->part->type = "text/plain";
        $this->part->filename = 'test.txt';
        $this->part->disposition = 'attachment';
        $this->part->charset = 'iso8859-1';
        $this->part->id = '4711';
    }

    public function testHeaders()
    {
        $expectedHeaders = array('Content-Type: text/plain',
                                 'Content-Transfer-Encoding: ' . Zend_Mime::ENCODING_BASE64,
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
        $this->assertEquals($this->_testText, base64_decode($content));
        // Test with quotedPrintable Encoding:
        $this->part->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $content = $this->part->getContent();
        $this->assertEquals($this->_testText, quoted_printable_decode($content));
        // Test with 8Bit encoding
        $this->part->encoding = Zend_Mime::ENCODING_8BIT;
        $content = $this->part->getContent();
        $this->assertEquals($this->_testText, $content);
    }

    public function testStreamEncoding()
    {
        $testfile = realpath(__FILE__);
        $original = file_get_contents($testfile);

        // Test Base64
        $fp = fopen($testfile,'rb');
        $this->assertTrue(is_resource($fp));
        $part = new Zend_Mime_Part($fp);
        $part->encoding = Zend_Mime::ENCODING_BASE64;
        $fp2 = $part->getEncodedStream();
        $this->assertTrue(is_resource($fp2));
        $encoded = stream_get_contents($fp2);
        fclose($fp);
        $this->assertEquals(base64_decode($encoded),$original);

        // test QuotedPrintable
        $fp = fopen($testfile,'rb');
        $this->assertTrue(is_resource($fp));
        $part = new Zend_Mime_Part($fp);
        $part->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $fp2 = $part->getEncodedStream();
        $this->assertTrue(is_resource($fp2));
        $encoded = stream_get_contents($fp2);
        fclose($fp);
        $this->assertEquals(quoted_printable_decode($encoded),$original);
    }
}
