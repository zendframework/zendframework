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
 * \Zend\Pdf\InternalType\NameObject
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
class NameTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFName()
    {
        $nameObj = new InternalType\NameObject('MyName');
        $this->assertTrue($nameObj instanceof InternalType\NameObject);
    }

    public function testPDFNameBadString()
    {
        $this->setExpectedException('\Zend\Pdf\Exception\RuntimeException', 'Null character is not allowed');
        $nameObj = new InternalType\NameObject("MyName\x00");
    }

    public function testGetType()
    {
        $nameObj = new InternalType\NameObject('MyName');
        $this->assertEquals($nameObj->getType(), InternalType\AbstractTypeObject::TYPE_NAME);
    }

    public function testToString()
    {
        $nameObj = new InternalType\NameObject('MyName');
        $this->assertEquals($nameObj->toString(), '/MyName');
    }

    public function testEscape()
    {
        $this->assertEquals(InternalType\NameObject::escape('My Cool Name()'), 'My#20Cool#20Name#28#29');
    }

    public function testUnescape()
    {
        $this->assertEquals(InternalType\NameObject::unescape('My#20Cool#20Name#28#29'), 'My Cool Name()');
    }
}
