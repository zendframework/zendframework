<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_Name
 */
require_once 'Zend/Pdf/Element/Name.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_Element_NameTest extends PHPUnit_Framework_TestCase
{
    public function testPDFName()
    {
        $nameObj = new Zend_Pdf_Element_Name('MyName');
        $this->assertTrue($nameObj instanceof Zend_Pdf_Element_Name);
    }

    public function testPDFNameBadString()
    {
        try {
            $nameObj = new Zend_Pdf_Element_Name("MyName\x00");
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/Null character is not allowed/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testGetType()
    {
        $nameObj = new Zend_Pdf_Element_Name('MyName');
        $this->assertEquals($nameObj->getType(), Zend_Pdf_Element::TYPE_NAME);
    }

    public function testToString()
    {
        $nameObj = new Zend_Pdf_Element_Name('MyName');
        $this->assertEquals($nameObj->toString(), '/MyName');
    }

    public function testEscape()
    {
        $this->assertEquals(Zend_Pdf_Element_Name::escape('My Cool Name()'), 'My#20Cool#20Name#28#29');
    }

    public function testUnescape()
    {
        $this->assertEquals(Zend_Pdf_Element_Name::unescape('My#20Cool#20Name#28#29'), 'My Cool Name()');
    }
}
