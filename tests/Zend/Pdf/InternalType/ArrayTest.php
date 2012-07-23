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
 * \Zend\Pdf\InternalType\ArrayObject
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
class ArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFArray1()
    {
        $arrayObj = new InternalType\ArrayObject();
        $this->assertTrue($arrayObj instanceof InternalType\ArrayObject);
    }

    public function testPDFArray2()
    {
        $srcArray = array();
        $srcArray[] = new InternalType\BooleanObject(false);
        $srcArray[] = new InternalType\NumericObject(100.426);
        $srcArray[] = new InternalType\NameObject('MyName');
        $srcArray[] = new InternalType\StringObject('some text');
        $srcArray[] = new InternalType\BinaryStringObject('some text');

        $arrayObj = new InternalType\ArrayObject($srcArray);
        $this->assertTrue($arrayObj instanceof InternalType\ArrayObject);
    }

    public function testPDFArrayBadInput1()
    {
        $this->setExpectedException('\Zend\Pdf\Exception\RuntimeException', 'Argument must be an array');
        $arrayObj = new InternalType\ArrayObject(346);
    }

    public function testPDFArrayBadInput2()
    {
        $this->setExpectedException(
            '\Zend\Pdf\Exception\RuntimeException',
            'Array elements must be \Zend\Pdf\InternalType\AbstractTypeObject objects'
        );

        $srcArray = array();
        $srcArray[] = new InternalType\BooleanObject(false);
        $srcArray[] = new InternalType\NumericObject(100.426);
        $srcArray[] = new InternalType\NameObject('MyName');
        $srcArray[] = new InternalType\StringObject('some text');
        $srcArray[] = new InternalType\BinaryStringObject('some text');
        $srcArray[] = 24;
        $arrayObj = new InternalType\ArrayObject($srcArray);
    }

    public function testGetType()
    {
        $arrayObj = new InternalType\ArrayObject();
        $this->assertEquals($arrayObj->getType(), InternalType\AbstractTypeObject::TYPE_ARRAY);
    }

    public function testToString()
    {
        $srcArray = array();
        $srcArray[] = new InternalType\BooleanObject(false);
        $srcArray[] = new InternalType\NumericObject(100.426);
        $srcArray[] = new InternalType\NameObject('MyName');
        $srcArray[] = new InternalType\StringObject('some text');
        $arrayObj = new InternalType\ArrayObject($srcArray);
        $this->assertEquals($arrayObj->toString(), '[false 100.426 /MyName (some text) ]');
    }

    /**
     * @todo \Zend\Pdf\InternalType\ArrayObject::add() does not exist
     */
    /*
    public function testAdd()
    {
        $arrayObj = new \Zend\Pdf\InternalType\ArrayObject($srcArray);
        $arrayObj->add(new \Zend\Pdf\InternalType\BooleanObject(false));
        $arrayObj->add(new \Zend\Pdf\InternalType\NumericObject(100.426));
        $arrayObj->add(new \Zend\Pdf\InternalType\NameObject('MyName'));
        $arrayObj->add(new \Zend\Pdf\InternalType\StringObject('some text'));
        $this->assertEquals($arrayObj->toString(), '[false 100.426 /MyName (some text) ]' );
    }
    //*/

    /**
     * @todo \Zend\Pdf\InternalType\ArrayObject::add() does not exist
     */
    /*
    public function testAddBadArgument()
    {
        $this->setExpectedException(
            '\Zend\Pdf\Exception\RuntimeException',
            'Array elements must be \Zend\Pdf\InternalType\AbstractTypeObject objects'
        );

        $arrayObj = new ZPDFPDFArray();
        $arrayObj->add(100.426);
    }
    //*/
}
