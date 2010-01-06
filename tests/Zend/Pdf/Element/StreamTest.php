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
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Pdf_Element_Stream
 */
require_once 'Zend/Pdf/Element/Stream.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_Element_StreamTest extends PHPUnit_Framework_TestCase
{
    public function testPDFStream()
    {
        $streamObj = new Zend_Pdf_Element_Stream('some text');
        $this->assertTrue($streamObj instanceof Zend_Pdf_Element_Stream);
    }

    public function testGetType()
    {
        $streamObj = new Zend_Pdf_Element_Stream('some text');
        $this->assertEquals($streamObj->getType(), Zend_Pdf_Element::TYPE_STREAM);
    }

    public function testValueAccess()
    {
        $streamObj = new Zend_Pdf_Element_Stream("some text (\x00\x01\x02)\n");
        $this->assertEquals($streamObj->value->getRef(), "some text (\x00\x01\x02)\n");

        $valueRef = &$streamObj->value->getRef();
        $valueRef = "another text (\x02\x03\x04)\n";
        $streamObj->value->touch();

        $this->assertEquals($streamObj->value->getRef(), "another text (\x02\x03\x04)\n");
    }

    public function testToString()
    {
        $streamObj = new Zend_Pdf_Element_Stream("some text (\x00\x01\x02)\n");
        $this->assertEquals($streamObj->toString(), "stream\nsome text (\x00\x01\x02)\n\nendstream");
    }

    public function testLength()
    {
        $streamObj = new Zend_Pdf_Element_Stream("some text (\x00\x01\x02)\n");
        $this->assertEquals($streamObj->length(), 16);
    }

    public function testClear()
    {
        $streamObj = new Zend_Pdf_Element_Stream("some text (\x00\x01\x02)\n");
        $streamObj->clear();
        $this->assertEquals($streamObj->length(), 0);
        $this->assertEquals($streamObj->toString(), "stream\n\nendstream");
    }

    public function testAppend()
    {
        $streamObj = new Zend_Pdf_Element_Stream("some text (\x00\x01\x02)\n");
        $streamObj->append("something\xAF");
        $this->assertEquals($streamObj->length(), 16 + 10);
        $this->assertEquals($streamObj->toString(), "stream\nsome text (\x00\x01\x02)\nsomething\xAF\nendstream");
    }
}
