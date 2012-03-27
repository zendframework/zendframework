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
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData;
use Zend\GData;

/**
 * Disabled; was not enabled in ZF1 test suite, and tests indicate different 
 * functionality than actually present in class
 *
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      disable
 */
class MediaMimeStreamTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->locationOfFakeBinary =
            'Zend/GData/_files/MediaMimeStreamSample1.txt';
        $this->smallXMLString = '<xml><entry><title>foo</title></entry>';
        $this->testMediaType = 'video/mpeg';
        $this->mediaMimeStream = new GData\MediaMimeStream(
            $this->smallXMLString, $this->locationOfFakeBinary,
            $this->testMediaType);
        $this->exceptedLenOfMimeMessage = 283;
    }

    public function testExceptionOnUnreadableFile()
    {
        $exceptionThrown = false;
        try {
            $mediaMimeStream = new GData\MediaMimeStream(
                $this->smallXMLString, '/non/existant/path/to/nowhere');
        } catch (\Zend\GData\App\IOException $e) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown, 'Was expecting an exception on ' .
            'attempting to read an unreadable or non-existant file');
    }

    public function testGetTotalSize()
    {
        $this->assertEquals($this->exceptedLenOfMimeMessage,
            $this->mediaMimeStream->getTotalSize());
    }

    /**
     * hasData() not currently implemented
     *
     * @group disable
     */
    public function testHasData()
    {
        $this->assertTrue($this->mediaMimeStream->hasData());
    }

    public function testGetContentType()
    {
        $pattern =
        '/multipart\/related;\sboundary=\"=_[a-z0-9]{32,}.*\"/';
        $this->assertEquals(1, preg_match($pattern,
            $this->mediaMimeStream->getContentType()));
    }

    /**
     * Ensure that nothing breaks if we read past the end of the messsage in a
     * single read.
     *
     * Note: The test message has the following part sizes in length:
     * 211, 22, 39 for a total size of 272. This test performs a single read
     * for 400 bytes.
     */
    public function testReadAll()
    {
        $this->assertEquals($this->exceptedLenOfMimeMessage,
            $this->mediaMimeStream->getTotalSize());
        $outputArray = array();
        while ($this->mediaMimeStream->hasData()) {
            $outputArray = explode("\r\n", $this->mediaMimeStream->read(400));
        }
        $mimeBoundaryPattern = '/--=_[a-z0-9]{32,}/';
        $mimeClosingBoundaryPattern = '/--=_[a-z0-9]{32,}--/';
        $this->assertEquals('', $outputArray[0]);
        $this->assertEquals(1,
            preg_match($mimeBoundaryPattern, $outputArray[1]));
        $this->assertEquals('Content-Type: application/atom+xml',
            $outputArray[2]);
        $this->assertEquals('', $outputArray[3]);
        $this->assertEquals($this->smallXMLString, $outputArray[4]);
        $this->assertEquals('', $outputArray[5]);
        $this->assertEquals(1,
            preg_match($mimeBoundaryPattern, $outputArray[6]));
        $this->assertEquals('Content-Type: video/mpeg', $outputArray[7]);
        $this->assertEquals('Content-Transfer-Encoding: binary',
            $outputArray[8]);
        $this->assertEquals('', $outputArray[9]);
        $this->assertEquals(file_get_contents($this->locationOfFakeBinary),
            $outputArray[10]);
        $this->assertEquals(1,
            preg_match($mimeClosingBoundaryPattern, $outputArray[11]));
    }

    /**
     * Ensure that a variety of different stream sizes work.
     *
     * Note: The test message has the following part sizes in length:
     * 211, 22, 39 for a total size of 287.
     */
    public function testReadVariousBufferSizes()
    {
        $bufferSizesToTest = array(2, 20, 33, 44, 88, 100, 201);
        foreach($bufferSizesToTest as $sizeToTest) {
            $mediaMimeStream = new GData\MediaMimeStream(
                $this->smallXMLString, $this->locationOfFakeBinary,
                $this->testMediaType);
            $this->assertEquals($sizeToTest,
                strlen($mediaMimeStream->read($sizeToTest)));
        }
    }

    /**
     * Ensure that nothing breaks if we read a message 1 byte at time.
     *
     * Disabled, as hasData() is not implemented
     *
     * @group disable
     */
    public function testReadWithoutCrossingSections()
    {
        $outputString = '';
        while ($this->mediaMimeStream->hasData()) {
            $outputString .= $this->mediaMimeStream->read(1);
        }
        $this->assertEquals($this->exceptedLenOfMimeMessage,
            strlen($outputString));
    }

    /**
     * Ensure that nothing breaks if we read past at least two sections of
     * the message.
     *
     * Note: The test message has the following part sizes in length:
     * 211, 22, 39 for a total size of 272. This test reads 250 bytes at a time
     * to make sure that we cross sections 1 and 2 and then read part of
     * section 3.
     *
     * Disabled, as hasData() is not implemented
     *
     * @group disable
     */
    public function testReadCrossing2Sections()
    {
        $outputString = '';
        while ($this->mediaMimeStream->hasData()) {
            $outputString .= $this->mediaMimeStream->read(250);
        }
        $this->assertEquals($this->exceptedLenOfMimeMessage,
            strlen($outputString));
    }

    /**
     * Ensure that nothing breaks if we read past at least one section of
     * the message.
     *
     * Note: The test message has the following part sizes in length:
     * 211, 22, 39 for a total size of 272. This test reads 230 bytes at a time
     * to make sure that we cross section 1 and then read sections 2 and 3.
     *
     * Disabled, as hasData() is not implemented
     *
     * @group disable
     */
    public function testReadCrossing1Section()
    {
        $outputString = '';
        while ($this->mediaMimeStream->hasData()) {
            $outputString .= $this->mediaMimeStream->read(230);
        }
        $this->assertEquals($this->exceptedLenOfMimeMessage,
            strlen($outputString));
    }

}
