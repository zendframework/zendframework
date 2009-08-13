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
 * Zend_Pdf_Element_Boolean
 */
require_once 'Zend/Pdf/Element/Boolean.php';

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
class Zend_Pdf_Element_BooleanTest extends PHPUnit_Framework_TestCase
{
    public function testPDFBoolean()
    {
        $boolObj = new Zend_Pdf_Element_Boolean(false);
        $this->assertTrue($boolObj instanceof Zend_Pdf_Element_Boolean);
    }

    public function testPDFBooleanBadArgument()
    {
        try {
            $boolObj = new Zend_Pdf_Element_Boolean('some input');
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/must be boolean/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testGetType()
    {
        $boolObj = new Zend_Pdf_Element_Boolean((boolean) 100);
        $this->assertEquals($boolObj->getType(), Zend_Pdf_Element::TYPE_BOOL);
    }

    public function testToString()
    {
        $boolObj = new Zend_Pdf_Element_Boolean(true);
        $this->assertEquals($boolObj->toString(), 'true');
    }
}
