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
namespace ZendTest\Pdf\InternalType\String;
use Zend\Pdf\InternalType;

/**
 * Zend\Pdf\InternalType\BinaryStringObject
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
class BinaryStringTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFBinaryString()
    {
        $stringObj = new InternalType\BinaryStringObject('some text');
        $this->assertTrue($stringObj instanceof InternalType\BinaryStringObject);
    }

    public function testGetType()
    {
        $stringObj = new InternalType\BinaryStringObject('some text');
        $this->assertEquals($stringObj->getType(), InternalType\AbstractTypeObject::TYPE_STRING);
    }

    public function testToString()
    {
        $stringObj = new InternalType\BinaryStringObject("\x00\x01\x02\x03\x04\x05\x06\x07\x22\xFF\xF3");
        $this->assertEquals($stringObj->toString(), '<000102030405060722FFF3>');
    }

    public function testEscape()
    {
        $this->assertEquals(InternalType\BinaryStringObject::escape("\n\r\t\x08\x0C()\\"), '0A0D09080C28295C');
    }

    public function testUnescape1()
    {
        $this->assertEquals(InternalType\BinaryStringObject::unescape('01020304FF20'), "\x01\x02\x03\x04\xFF ");
    }

    public function testUnescape2()
    {
        $this->assertEquals(InternalType\BinaryStringObject::unescape('01020304FF2'), "\x01\x02\x03\x04\xFF ");
    }
}
