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

/**
 * Zend_Pdf_Element_Numeric
 */
require_once 'Zend/Pdf/Element/Numeric.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_Element_NumericTest extends PHPUnit_Framework_TestCase
{
    public function testPDFNumeric()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $this->assertTrue($intObj instanceof Zend_Pdf_Element_Numeric);
    }

    public function testPDFNumericBadArgument()
    {
        try {
            $intObj = new Zend_Pdf_Element_Numeric('some input');
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must be numeric/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testGetType()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $this->assertEquals($intObj->getType(), Zend_Pdf_Element::TYPE_NUMERIC);
    }

    public function testToString()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $this->assertEquals($intObj->toString(), '100');
    }

    public function testToStringFloat1()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100.426);
        $this->assertEquals($intObj->toString(), '100.426');
    }

    public function testToStringFloat2()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100.42633);
        $this->assertEquals($intObj->toString(), '100.42633');
    }

    public function testToStringFloat3()
    {
        $intObj = new Zend_Pdf_Element_Numeric(-100.426);
        $this->assertEquals($intObj->toString(), '-100.426');
    }
}
