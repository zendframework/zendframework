<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_String_Binary
 */
require_once 'Zend/Pdf/Element/String/Binary.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
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
