<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendTest\Pdf\InternalType;

use Zend\Pdf\InternalType;
use Zend\Pdf;

/**
 * \Zend\Pdf\InternalType\DictionaryObject
 */

/**
 * PHPUnit Test Case
 */

/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @group      Zend_PDF
 */
class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFDictionary1()
    {
        $dictionaryObj = new InternalType\DictionaryObject();
        $this->assertTrue($dictionaryObj instanceof InternalType\DictionaryObject);
    }

    public function testPDFDictionary2()
    {
        $srcArray = array();
        $srcArray['Bool'] = new InternalType\BooleanObject(false);
        $srcArray['Number'] = new InternalType\NumericObject(100.426);
        $srcArray['Name'] = new InternalType\NameObject('MyName');
        $srcArray['Text'] = new InternalType\StringObject('some text');
        $srcArray['BinaryText'] = new InternalType\BinaryStringObject('some text');

        $dictionaryObj = new InternalType\DictionaryObject($srcArray);
        $this->assertTrue($dictionaryObj instanceof InternalType\DictionaryObject);
    }

    public function testPDFDictionaryBadInput1()
    {
        $this->setExpectedException('\Zend\Pdf\Exception\RuntimeException', 'must be an array');
        $dictionaryObj = new InternalType\DictionaryObject(346);
    }

    public function testPDFDictionaryBadInput2()
    {
        $this->setExpectedException(
            '\Zend\Pdf\Exception\RuntimeException',
            'must be \Zend\Pdf\InternalType\AbstractTypeObject'
        );

        $srcArray = array();
        $srcArray['Bool'] = new InternalType\BooleanObject(false);
        $srcArray['Number'] = new InternalType\NumericObject(100.426);
        $srcArray['Name'] = new InternalType\NameObject('MyName');
        $srcArray['Text'] = new InternalType\StringObject('some text');
        $srcArray['BinaryText'] = new InternalType\BinaryStringObject('some text');
        $srcArray['bad value'] = 24;
        $dictionaryObj = new InternalType\DictionaryObject($srcArray);
    }

    public function testPDFDictionaryBadInput3()
    {
        $this->setExpectedException('\Zend\Pdf\Exception\RuntimeException', 'keys must be strings');
        $srcArray = array();
        $srcArray['Bool'] = new InternalType\BooleanObject(false);
        $srcArray['Number'] = new InternalType\NumericObject(100.426);
        $srcArray['Name'] = new InternalType\NameObject('MyName');
        $srcArray['Text'] = new InternalType\StringObject('some text');
        $srcArray['BinaryText'] = new InternalType\BinaryStringObject('some text');
        $srcArray[5] = new InternalType\StringObject('bad name');
        $dictionaryObj = new InternalType\DictionaryObject($srcArray);
    }

    public function testGetType()
    {
        $dictionaryObj = new InternalType\DictionaryObject();
        $this->assertEquals($dictionaryObj->getType(), InternalType\AbstractTypeObject::TYPE_DICTIONARY);
    }

    public function testToString()
    {
        $srcArray = array();
        $srcArray['Bool'] = new InternalType\BooleanObject(false);
        $srcArray['Number'] = new InternalType\NumericObject(100.426);
        $srcArray['Name'] = new InternalType\NameObject('MyName');
        $srcArray['Text'] = new InternalType\StringObject('some text');
        $srcArray['BinaryText'] = new InternalType\BinaryStringObject("\x01\x02\x00\xff");
        $dictionaryObj = new InternalType\DictionaryObject($srcArray);
        $this->assertEquals($dictionaryObj->toString(),
                            '<</Bool false /Number 100.426 /Name /MyName /Text (some text) /BinaryText <010200FF> >>');
    }

    public function testAdd()
    {
        $dictionaryObj = new InternalType\DictionaryObject();
        $dictionaryObj->add(new InternalType\NameObject('Var1'), new InternalType\BooleanObject(false));
        $dictionaryObj->add(new InternalType\NameObject('Var2'), new InternalType\NumericObject(100.426));
        $dictionaryObj->add(new InternalType\NameObject('Var3'), new InternalType\NameObject('MyName'));
        $dictionaryObj->add(new InternalType\NameObject('Var4'), new InternalType\StringObject('some text'));
        $this->assertEquals($dictionaryObj->toString(),
                            '<</Var1 false /Var2 100.426 /Var3 /MyName /Var4 (some text) >>');
    }

}
