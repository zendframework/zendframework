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

/**
 * \Zend\Pdf\InternalType\NumericObject
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
class NumericTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFNumeric()
    {
        $intObj = new InternalType\NumericObject(100);
        $this->assertTrue($intObj instanceof InternalType\NumericObject);
    }

    public function testPDFNumericBadArgument()
    {
        $this->setExpectedException('\Zend\Pdf\Exception\RuntimeException', 'must be numeric');
        $intObj = new InternalType\NumericObject('some input');
    }

    public function testGetType()
    {
        $intObj = new InternalType\NumericObject(100);
        $this->assertEquals($intObj->getType(), InternalType\AbstractTypeObject::TYPE_NUMERIC);
    }

    public function testToString()
    {
        $intObj = new InternalType\NumericObject(100);
        $this->assertEquals($intObj->toString(), '100');
    }

    public function testToStringFloat1()
    {
        $intObj = new InternalType\NumericObject(100.426);
        $this->assertEquals($intObj->toString(), '100.426');
    }

    public function testToStringFloat2()
    {
        $intObj = new InternalType\NumericObject(100.42633);
        $this->assertEquals($intObj->toString(), '100.42633');
    }

    public function testToStringFloat3()
    {
        $intObj = new InternalType\NumericObject(-100.426);
        $this->assertEquals($intObj->toString(), '-100.426');
    }
}
