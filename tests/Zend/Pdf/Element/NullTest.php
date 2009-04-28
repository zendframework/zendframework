<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_Null
 */
require_once 'Zend/Pdf/Element/Null.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_Element_NullTest extends PHPUnit_Framework_TestCase
{
    public function testPDFNull()
    {
        $nullObj = new Zend_Pdf_Element_Null();
        $this->assertTrue($nullObj instanceof Zend_Pdf_Element_Null);
    }

    public function testGetType()
    {
        $nullObj = new Zend_Pdf_Element_Null();
        $this->assertEquals($nullObj->getType(), Zend_Pdf_Element::TYPE_NULL);
    }

    public function testToString()
    {
        $nullObj = new Zend_Pdf_Element_Null();
        $this->assertEquals($nullObj->toString(), 'null');
    }
}
