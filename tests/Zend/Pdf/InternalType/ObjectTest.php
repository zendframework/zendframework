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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Pdf\InternalType;
use Zend\Pdf\InternalType;
use Zend\Pdf\ObjectFactory;
use Zend\Pdf;

/** \Zend\Pdf\InternalType\IndirectObject */


/** PHPUnit Test Case */

/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_PDF
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFObject()
    {
        $intObj = new InternalType\NumericObject(100);
        $obj    = new InternalType\IndirectObject($intObj, 1, 0, new ObjectFactory\ElementFactory(1));

        $this->assertTrue($obj instanceof InternalType\IndirectObject);
    }

    public function testPDFObjectBadObjectType1()
    {
        $intObj = new InternalType\NumericObject(100);
        $obj1   = new InternalType\IndirectObject($intObj, 1, 0, new ObjectFactory\ElementFactory(1));

        try {
            $obj2 = new InternalType\IndirectObject($obj1, 1, 0, new ObjectFactory\ElementFactory(1));
        } catch (Pdf\Exception $e) {
            $this->assertContains('must not be an instance of \Zend\Pdf\InternalType\IndirectObject', $e->getMessage());
            return;
        }
        $this->fail('Expected \Zend\Pdf\Exception to be thrown');
    }

    public function testPDFObjectBadGenNumber1()
    {
        try {
            $intObj = new InternalType\NumericObject(100);
            $obj    = new InternalType\IndirectObject($intObj, 1, -1, new ObjectFactory\ElementFactory(1));
        } catch (Pdf\Exception $e) {
            $this->assertContains('non-negative integer', $e->getMessage());
            return;
        }
        $this->fail('Expected \Zend\Pdf\Exception to be thrown');
    }

    public function testPDFObjectBadGenNumber2()
    {
        try {
            $intObj = new InternalType\NumericObject(100);
            $obj    = new InternalType\IndirectObject($intObj, 1, 1.2, new ObjectFactory\ElementFactory(1));
        } catch (Pdf\Exception $e) {
            $this->assertContains('non-negative integer', $e->getMessage());
            return;
        }
        $this->fail('Expected \Zend\Pdf\Exception to be thrown');
    }

    public function testPDFObjectBadObjectNumber1()
    {
        try {
            $intObj = new InternalType\NumericObject(100);
            $obj    = new InternalType\IndirectObject($intObj, 0, 0, new ObjectFactory\ElementFactory(1));
        } catch (Pdf\Exception $e) {
            $this->assertContains('positive integer', $e->getMessage());
            return;
        }
        $this->fail('Expected \Zend\Pdf\Exception to be thrown');
    }

    public function testPDFObjectBadObjectNumber2()
    {
        try {
            $intObj = new InternalType\NumericObject(100);
            $obj    = new InternalType\IndirectObject($intObj, -1, 0, new ObjectFactory\ElementFactory(1));
        } catch (Pdf\Exception $e) {
            $this->assertContains('positive integer', $e->getMessage());
            return;
        }
        $this->fail('Expected \Zend\Pdf\Exception to be thrown');
    }

    public function testPDFObjectBadObjectNumber3()
    {
        try {
            $intObj = new InternalType\NumericObject(100);
            $obj    = new InternalType\IndirectObject($intObj, 1.2, 0, new ObjectFactory\ElementFactory(1));
        } catch (Pdf\Exception $e) {
            $this->assertContains('positive integer', $e->getMessage());
            return;
        }
        $this->fail('Expected \Zend\Pdf\Exception to be thrown');
    }

    public function testGetType()
    {
        $intObj = new InternalType\NumericObject(100);
        $obj    = new InternalType\IndirectObject($intObj, 1, 0, new ObjectFactory\ElementFactory(1));

        $this->assertEquals($obj->getType(), $intObj->getType());
    }

    public function testToString()
    {
        $intObj = new InternalType\NumericObject(100);
        $obj    = new InternalType\IndirectObject($intObj, 55, 3, new ObjectFactory\ElementFactory(1));

        $this->assertEquals($obj->toString(), '55 3 R');
    }

    public function testDump()
    {
        $factory = new ObjectFactory\ElementFactory(1);

        $intObj  = new InternalType\NumericObject(100);
        $obj     = new InternalType\IndirectObject($intObj, 55, 3, $factory);

        $this->assertEquals($obj->dump($factory), "55 3 obj \n100\nendobj\n");
    }
}
