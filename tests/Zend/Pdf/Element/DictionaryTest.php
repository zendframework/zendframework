<?php
/**
 * @package Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_Dictionary
 */
require_once 'Zend/Pdf/Element/Dictionary.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_Element_DictionaryTest extends PHPUnit_Framework_TestCase
{
    public function testPDFDictionary1()
    {
        $dictionaryObj = new Zend_Pdf_Element_Dictionary();
        $this->assertTrue($dictionaryObj instanceof Zend_Pdf_Element_Dictionary);
    }

    public function testPDFDictionary2()
    {
        $srcArray = array();
        $srcArray['Bool'] = new Zend_Pdf_Element_Boolean(false);
        $srcArray['Number'] = new Zend_Pdf_Element_Numeric(100.426);
        $srcArray['Name'] = new Zend_Pdf_Element_Name('MyName');
        $srcArray['Text'] = new Zend_Pdf_Element_String('some text');
        $srcArray['BinaryText'] = new Zend_Pdf_Element_String_Binary('some text');

        $dictionaryObj = new Zend_Pdf_Element_Dictionary($srcArray);
        $this->assertTrue($dictionaryObj instanceof Zend_Pdf_Element_Dictionary);
    }

    public function testPDFDictionaryBadInput1()
    {
        try {
            $arrayObj = new Zend_Pdf_Element_Dictionary(346);
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must be an array/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testPDFDictionaryBadInput2()
    {
        try {
            $srcArray = array();
            $srcArray['Bool'] = new Zend_Pdf_Element_Boolean(false);
            $srcArray['Number'] = new Zend_Pdf_Element_Numeric(100.426);
            $srcArray['Name'] = new Zend_Pdf_Element_Name('MyName');
            $srcArray['Text'] = new Zend_Pdf_Element_String('some text');
            $srcArray['BinaryText'] = new Zend_Pdf_Element_String_Binary('some text');
            $srcArray['bad value'] = 24;
            $dictionaryObj = new Zend_Pdf_Element_Dictionary($srcArray);
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must be Zend_Pdf_Element/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testPDFDictionaryBadInput3()
    {
        try {
            $srcArray = array();
            $srcArray['Bool'] = new Zend_Pdf_Element_Boolean(false);
            $srcArray['Number'] = new Zend_Pdf_Element_Numeric(100.426);
            $srcArray['Name'] = new Zend_Pdf_Element_Name('MyName');
            $srcArray['Text'] = new Zend_Pdf_Element_String('some text');
            $srcArray['BinaryText'] = new Zend_Pdf_Element_String_Binary('some text');
            $srcArray[5] = new Zend_Pdf_Element_String('bad name');
            $dictionaryObj = new Zend_Pdf_Element_Dictionary($srcArray);
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/keys must be strings/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testGetType()
    {
        $dictionaryObj = new Zend_Pdf_Element_Dictionary();
        $this->assertEquals($dictionaryObj->getType(), Zend_Pdf_Element::TYPE_DICTIONARY);
    }

    public function testToString()
    {
        $srcArray = array();
        $srcArray['Bool'] = new Zend_Pdf_Element_Boolean(false);
        $srcArray['Number'] = new Zend_Pdf_Element_Numeric(100.426);
        $srcArray['Name'] = new Zend_Pdf_Element_Name('MyName');
        $srcArray['Text'] = new Zend_Pdf_Element_String('some text');
        $srcArray['BinaryText'] = new Zend_Pdf_Element_String_Binary("\x01\x02\x00\xff");
        $dictionaryObj = new Zend_Pdf_Element_Dictionary($srcArray);
        $this->assertEquals($dictionaryObj->toString(),
                            '<</Bool false /Number 100.426 /Name /MyName /Text (some text) /BinaryText <010200FF> >>');
    }

    public function testAdd()
    {
        $dictionaryObj = new Zend_Pdf_Element_Dictionary();
        $dictionaryObj->add(new Zend_Pdf_Element_Name('Var1'), new Zend_Pdf_Element_Boolean(false));
        $dictionaryObj->add(new Zend_Pdf_Element_Name('Var2'), new Zend_Pdf_Element_Numeric(100.426));
        $dictionaryObj->add(new Zend_Pdf_Element_Name('Var3'), new Zend_Pdf_Element_Name('MyName'));
        $dictionaryObj->add(new Zend_Pdf_Element_Name('Var4'), new Zend_Pdf_Element_String('some text'));
        $this->assertEquals($dictionaryObj->toString(),
                            '<</Var1 false /Var2 100.426 /Var3 /MyName /Var4 (some text) >>');
    }

}
