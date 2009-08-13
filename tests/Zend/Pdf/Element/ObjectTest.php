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
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Pdf_Element_Object */
require_once 'Zend/Pdf/Element/Object.php';

/** Zend_Pdf_Element_Numeric */
require_once 'Zend/Pdf/Element/Numeric.php';

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
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
            $this->assertRegExp('/must not be an instance of Zend_Pdf_Element_Object/i', $e->getMessage());
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
