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
 * Zend_Pdf_Element_String
 */

/**
 * PHPUnit Test Case
 */

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_Element_StringTest extends PHPUnit_Framework_TestCase
{
    public function testPDFString()
    {
        $stringObj = new Zend_Pdf_Element_String('some text');
        $this->assertTrue($stringObj instanceof Zend_Pdf_Element_String);
    }

    public function testGetType()
    {
        $stringObj = new Zend_Pdf_Element_String('some text');
        $this->assertEquals($stringObj->getType(), Zend_Pdf_Element::TYPE_STRING);
    }

    public function testToString()
    {
        $stringObj = new Zend_Pdf_Element_String('some text ()');
        $this->assertEquals($stringObj->toString(), '(some text \\(\\))' );
    }

    public function testEscape()
    {
        $this->assertEquals(Zend_Pdf_Element_String::escape("\n\r\t\x08\x0C()\\"), "\\n\\r\\t\\b\\f\\(\\)\\\\");
    }

    public function testUnescape()
    {
        $this->assertEquals(Zend_Pdf_Element_String::unescape("\\n\\r\\t\\b\\f\\(\\)\\\\  \nsome \\\ntext"),
                            "\n\r\t\x08\x0C()\\  \nsome text");
    }
}
