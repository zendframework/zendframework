<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_String
 */
require_once 'Zend/Pdf/Element/String.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_Element_StringTest extends PHPUnit_Framework_TestCase
{
    public function testPDFString()
    {
        $stringObj = new Zend_Pdf_Element_String('some text');
        $this->assertTrue($stringObj instanceof Zend_Pdf_Element_String);
    }

    public function testGetType()
    {
        $stringObj = new Zend_Pdf_Element_String('some text');
        $this->assertEquals($stringObj->getType(), Zend_Pdf_Element::TYPE_STRING);
    }

    public function testToString()
    {
        $stringObj = new Zend_Pdf_Element_String('some text ()');
        $this->assertEquals($stringObj->toString(), '(some text \\(\\))' );
    }

    public function testEscape()
    {
        $this->assertEquals(Zend_Pdf_Element_String::escape("\n\r\t\x08\x0C()\\"), "\\n\\r\\t\\b\\f\\(\\)\\\\");
    }

    public function testUnescape()
    {
        $this->assertEquals(Zend_Pdf_Element_String::unescape("\\n\\r\\t\\b\\f\\(\\)\\\\  \nsome \\\ntext"),
                            "\n\r\t\x08\x0C()\\  \nsome text");
    }
}
