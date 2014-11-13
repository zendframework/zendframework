<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Header\ContentTransferEncoding;

/**
 * @group      Zend_Mail
 */
class ContentTransferEncodingTest extends \PHPUnit_Framework_TestCase
{

    public function testContentTransferEncodingFromStringCreatesValidContentTransferEncodingHeader()
    {
        $contentTransferEncodingHeader = ContentTransferEncoding::fromString('Content-Transfer-Encoding: 7bit');
        $this->assertInstanceOf('Zend\Mail\Header\HeaderInterface', $contentTransferEncodingHeader);
        $this->assertInstanceOf('Zend\Mail\Header\ContentTransferEncoding', $contentTransferEncodingHeader);
    }

    public function testContentTransferEncodingFromStringCreateExcaption()
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        $contentTransferEncodingHeader = ContentTransferEncoding::fromString('Content-Transfer-Encoding: 9bit');
    }

    public function testContentTransferEncodingGetFieldNameReturnsHeaderName()
    {
        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $this->assertEquals('Content-Transfer-Encoding', $contentTransferEncodingHeader->getFieldName());
    }

    public function testContentTransferEncodingGetFieldValueReturnsProperValue()
    {
        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $contentTransferEncodingHeader->setTransferEncoding('7bit');
        $this->assertEquals('7bit', $contentTransferEncodingHeader->getFieldValue());
    }

    public function testContentTransferEncodingHandlesCaseInsensitivity()
    {
        $encoding = new ContentTransferEncoding();
        $encoding->setTransferEncoding('quOtED-printAble');
        $this->assertEquals('quoted-printable', strtolower($encoding->getFieldValue()));
    }

    public function testContentTransferEncodingToStringReturnsHeaderFormattedString()
    {
        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $contentTransferEncodingHeader->setTransferEncoding('8bit');
        $this->assertEquals("Content-Transfer-Encoding: 8bit", $contentTransferEncodingHeader->toString());
    }

    public function testProvidingParametersIntroducesHeaderFolding()
    {
        $header = new ContentTransferEncoding();
        $header->setTransferEncoding('quoted-printable');
        $string = $header->toString();

        $this->assertContains("Content-Transfer-Encoding: quoted-printable", $string);
    }

}
