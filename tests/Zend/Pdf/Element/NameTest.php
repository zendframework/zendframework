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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Pdf_Element_Name
 */
require_once 'Zend/Pdf/Element/Name.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_Element_NameTest extends PHPUnit_Framework_TestCase
{
    public function testPDFName()
    {
        $nameObj = new Zend_Pdf_Element_Name('MyName');
        $this->assertTrue($nameObj instanceof Zend_Pdf_Element_Name);
    }

    public function testPDFNameBadString()
    {
        try {
            $nameObj = new Zend_Pdf_Element_Name("MyName\x00");
        } catch (Zend_Pdf_Exception $e) {
            $this->assertRegExp('/Null character is not allowed/i', $e->getMessage());
            return;
        }
        $this->fail('Expected Zend_Pdf_Exception to be thrown');
    }

    public function testGetType()
    {
        $nameObj = new Zend_Pdf_Element_Name('MyName');
        $this->assertEquals($nameObj->getType(), Zend_Pdf_Element::TYPE_NAME);
    }

    public function testToString()
    {
        $nameObj = new Zend_Pdf_Element_Name('MyName');
        $this->assertEquals($nameObj->toString(), '/MyName');
    }

    public function testEscape()
    {
        $this->assertEquals(Zend_Pdf_Element_Name::escape('My Cool Name()'), 'My#20Cool#20Name#28#29');
    }

    public function testUnescape()
    {
        $this->assertEquals(Zend_Pdf_Element_Name::unescape('My#20Cool#20Name#28#29'), 'My Cool Name()');
    }
}
