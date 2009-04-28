<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_Array
 */
require_once 'Zend/Pdf/Element/Array.php';

/**
 * Zend_Pdf_Element_Boolean
 */
require_once 'Zend/Pdf/Element/Boolean.php';

/**
 * Zend_Pdf_Element_Numeric
 */
require_once 'Zend/Pdf/Element/Numeric.php';

/**
 * Zend_Pdf_Element_Name
 */
require_once 'Zend/Pdf/Element/Name.php';

/**
 * Zend_Pdf_Element_String
 */
require_once 'Zend/Pdf/Element/String.php';

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
class Zend_Pdf_Element_ArrayTest extends PHPUnit_Framework_TestCase
{
    public function testPDFArray1()
    {
        $arrayObj = new Zend_Pdf_Element_Array();
        $this->assertTrue($arrayObj instanceof Zend_Pdf_Element_Array);
    }

    public function testPDFArray2()
    {
        $srcArray = array();
        $srcArray[] = new Zend_Pdf_Element_Boolean(false);
        $srcArray[] = new Zend_Pdf_Element_Numeric(100.426);
        $srcArray[] = new Zend_Pdf_Element_Name('MyName');
        $srcArray[] = new Zend_Pdf_Element_String('some text');
        $srcArray[] = new Zend_Pdf_Element_String_Binary('some text');

        $arrayObj = new Zend_Pdf_Element_Array($srcArray);
        $this->assertTrue($arrayObj instanceof Zend_Pdf_Element_Array);
    }

    public function testPDFArrayBadInput1()
    {
        try {
            $arrayObj = new Zend_Pdf_Element_Array(346);
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must be an array/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testPDFArrayBadInput2()
    {
        try {
            $srcArray = array();
            $srcArray[] = new Zend_Pdf_Element_Boolean(false);
            $srcArray[] = new Zend_Pdf_Element_Numeric(100.426);
            $srcArray[] = new Zend_Pdf_Element_Name('MyName');
            $srcArray[] = new Zend_Pdf_Element_String('some text');
            $srcArray[] = new Zend_Pdf_Element_String_Binary('some text');
            $srcArray[] = 24;
            $arrayObj = new Zend_Pdf_Element_Array($srcArray);
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must be Zend_Pdf_Element/i', $e->getMessage());
            return;
        }
        $this->fail('No exception thrown.');
    }

    public function testGetType()
    {
        $arrayObj = new Zend_Pdf_Element_Array();
        $this->assertEquals($arrayObj->getType(), Zend_Pdf_Element::TYPE_ARRAY);
    }

    public function testToString()
    {
        $srcArray = array();
        $srcArray[] = new Zend_Pdf_Element_Boolean(false);
        $srcArray[] = new Zend_Pdf_Element_Numeric(100.426);
        $srcArray[] = new Zend_Pdf_Element_Name('MyName');
        $srcArray[] = new Zend_Pdf_Element_String('some text');
        $arrayObj = new Zend_Pdf_Element_Array($srcArray);
        $this->assertEquals($arrayObj->toString(), '[false 100.426 /MyName (some text) ]');
    }

    /**
     * @todo Zend_Pdf_Element_Array::add() does not exist
     */
    /*
    public function testAdd()
    {
        $arrayObj = new Zend_Pdf_Element_Array($srcArray);
        $arrayObj->add(new Zend_Pdf_Element_Boolean(false));
        $arrayObj->add(new Zend_Pdf_Element_Numeric(100.426));
        $arrayObj->add(new Zend_Pdf_Element_Name('MyName'));
        $arrayObj->add(new Zend_Pdf_Element_String('some text'));
        $this->assertEquals($arrayObj->toString(), '[false 100.426 /MyName (some text) ]' );
    }
    //*/

    /**
     * @todo Zend_Pdf_Element_Array::add() does not exist
     */
    /*
    public function testAddBadArgument()
    {
        try {
            $arrayObj = new ZPdfPDFArray();
            $arrayObj->add(100.426);
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must be Zend_Pdf_Element/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }
    //*/
}
