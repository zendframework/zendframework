<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_Boolean
 */
require_once 'Zend/Pdf/Element/Boolean.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_Element_BooleanTest extends PHPUnit_Framework_TestCase
{
    public function testPDFBoolean()
    {
        $boolObj = new Zend_Pdf_Element_Boolean(false);
        $this->assertTrue($boolObj instanceof Zend_Pdf_Element_Boolean);
    }

    public function testPDFBooleanBadArgument()
    {
        try {
            $boolObj = new Zend_Pdf_Element_Boolean('some input');
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must be boolean/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testGetType()
    {
        $boolObj = new Zend_Pdf_Element_Boolean((boolean) 100);
        $this->assertEquals($boolObj->getType(), Zend_Pdf_Element::TYPE_BOOL);
    }

    public function testToString()
    {
        $boolObj = new Zend_Pdf_Element_Boolean(true);
        $this->assertEquals($boolObj->toString(), 'true');
    }
}
