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
 * Zend_Pdf_Element_String_Binary
 */

/**
 * PHPUnit Test Case
 */

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_Element_String_BinaryTest extends PHPUnit_Framework_TestCase
{
    public function testPDFBinaryString()
    {
        $stringObj = new Zend_Pdf_Element_String_Binary('some text');
        $this->assertTrue($stringObj instanceof Zend_Pdf_Element_String_Binary);
    }

    public function testGetType()
    {
        $stringObj = new Zend_Pdf_Element_String_Binary('some text');
        $this->assertEquals($stringObj->getType(), Zend_Pdf_Element::TYPE_STRING);
    }

    public function testToString()
    {
        $stringObj = new Zend_Pdf_Element_String_Binary("\x00\x01\x02\x03\x04\x05\x06\x07\x22\xFF\xF3");
        $this->assertEquals($stringObj->toString(), '<000102030405060722FFF3>');
    }

    public function testEscape()
    {
        $this->assertEquals(Zend_Pdf_Element_String_Binary::escape("\n\r\t\x08\x0C()\\"), '0A0D09080C28295C');
    }

    public function testUnescape1()
    {
        $this->assertEquals(Zend_Pdf_Element_String_Binary::unescape('01020304FF20'), "\x01\x02\x03\x04\xFF ");
    }

    public function testUnescape2()
    {
        $this->assertEquals(Zend_Pdf_Element_String_Binary::unescape('01020304FF2'), "\x01\x02\x03\x04\xFF ");
    }
}
