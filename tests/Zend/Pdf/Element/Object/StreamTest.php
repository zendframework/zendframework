<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_Object_Stream
 */
require_once 'Zend/Pdf/Element/Object/Stream.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_Element_Object_StreamTest extends PHPUnit_Framework_TestCase
{
    public function testPDFStreamObject()
    {
        $obj = new Zend_Pdf_Element_Object_Stream('some data', 1, 0, new Zend_Pdf_ElementFactory(1));
        $this->assertTrue($obj instanceof Zend_Pdf_Element_Object_Stream);
    }

    public function testGetType()
    {
        $obj = new Zend_Pdf_Element_Object_Stream('some data', 1, 0, new Zend_Pdf_ElementFactory(1));
        $this->assertEquals($obj->getType(), Zend_Pdf_Element::TYPE_STREAM);
    }

    public function testDump()
    {
        $factory = new Zend_Pdf_ElementFactory(1);

        $obj = new Zend_Pdf_Element_Object_Stream('some data', 55, 3, $factory);
        $this->assertEquals($obj->dump($factory), "55 3 obj \n<</Length 9 >>\nstream\nsome data\nendstream\nendobj\n");
    }
}
