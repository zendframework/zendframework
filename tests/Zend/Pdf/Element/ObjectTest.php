<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/** Zend_Pdf_Element_Object */
require_once 'Zend/Pdf/Element/Object.php';

/** Zend_Pdf_Element_Numeric */
require_once 'Zend/Pdf/Element/Numeric.php';

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_Element_ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testPDFObject()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 1, 0, new Zend_Pdf_ElementFactory(1));

        $this->assertTrue($obj instanceof Zend_Pdf_Element_Object);
    }

    public function testPDFObjectBadObjectType1()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj1   = new Zend_Pdf_Element_Object($intObj, 1, 0, new Zend_Pdf_ElementFactory(1));

        try {
            $obj2 = new Zend_Pdf_Element_Object($obj1, 1, 0, new Zend_Pdf_ElementFactory(1));
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must not be instance of Zend_Pdf_Element_Object/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testPDFObjectBadGenNumber1()
    {
        try {
            $intObj = new Zend_Pdf_Element_Numeric(100);
            $obj    = new Zend_Pdf_Element_Object($intObj, 1, -1, new Zend_Pdf_ElementFactory(1));
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/non-negative integer/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testPDFObjectBadGenNumber2()
    {
        try {
            $intObj = new Zend_Pdf_Element_Numeric(100);
            $obj    = new Zend_Pdf_Element_Object($intObj, 1, 1.2, new Zend_Pdf_ElementFactory(1));
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/non-negative integer/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testPDFObjectBadObjectNumber1()
    {
        try {
            $intObj = new Zend_Pdf_Element_Numeric(100);
            $obj    = new Zend_Pdf_Element_Object($intObj, 0, 0, new Zend_Pdf_ElementFactory(1));
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/positive integer/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testPDFObjectBadObjectNumber2()
    {
        try {
            $intObj = new Zend_Pdf_Element_Numeric(100);
            $obj    = new Zend_Pdf_Element_Object($intObj, -1, 0, new Zend_Pdf_ElementFactory(1));
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/positive integer/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testPDFObjectBadObjectNumber3()
    {
        try {
            $intObj = new Zend_Pdf_Element_Numeric(100);
            $obj    = new Zend_Pdf_Element_Object($intObj, 1.2, 0, new Zend_Pdf_ElementFactory(1));
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/positive integer/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testGetType()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 1, 0, new Zend_Pdf_ElementFactory(1));

        $this->assertEquals($obj->getType(), $intObj->getType());
    }

    public function testToString()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 55, 3, new Zend_Pdf_ElementFactory(1));

        $this->assertEquals($obj->toString(), '55 3 R');
    }

    public function testDump()
    {
        $factory = new Zend_Pdf_ElementFactory(1);

        $intObj  = new Zend_Pdf_Element_Numeric(100);
        $obj     = new Zend_Pdf_Element_Object($intObj, 55, 3, $factory);

        $this->assertEquals($obj->dump($factory), "55 3 obj \n100\nendobj\n");
    }
}
