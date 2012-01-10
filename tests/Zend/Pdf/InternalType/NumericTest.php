<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
