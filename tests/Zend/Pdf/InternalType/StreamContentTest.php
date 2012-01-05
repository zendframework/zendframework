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
 * \Zend\Pdf\InternalType\StreamContent
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
class StreamContentTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFStream()
    {
        $streamObj = new InternalType\StreamContent('some text');
        $this->assertTrue($streamObj instanceof InternalType\StreamContent);
    }

    public function testGetType()
    {
        $streamObj = new InternalType\StreamContent('some text');
        $this->assertEquals($streamObj->getType(), InternalType\AbstractTypeObject::TYPE_STREAM);
    }

    public function testValueAccess()
    {
        $streamObj = new InternalType\StreamContent("some text (\x00\x01\x02)\n");
        $this->assertEquals($streamObj->value->getRef(), "some text (\x00\x01\x02)\n");

        $valueRef = &$streamObj->value->getRef();
        $valueRef = "another text (\x02\x03\x04)\n";
        $streamObj->value->touch();

        $this->assertEquals($streamObj->value->getRef(), "another text (\x02\x03\x04)\n");
    }

    public function testToString()
    {
        $streamObj = new InternalType\StreamContent("some text (\x00\x01\x02)\n");
        $this->assertEquals($streamObj->toString(), "stream\nsome text (\x00\x01\x02)\n\nendstream");
    }

    public function testLength()
    {
        $streamObj = new InternalType\StreamContent("some text (\x00\x01\x02)\n");
        $this->assertEquals($streamObj->length(), 16);
    }

    public function testClear()
    {
        $streamObj = new InternalType\StreamContent("some text (\x00\x01\x02)\n");
        $streamObj->clear();
        $this->assertEquals($streamObj->length(), 0);
        $this->assertEquals($streamObj->toString(), "stream\n\nendstream");
    }

    public function testAppend()
    {
        $streamObj = new InternalType\StreamContent("some text (\x00\x01\x02)\n");
        $streamObj->append("something\xAF");
        $this->assertEquals($streamObj->length(), 16 + 10);
        $this->assertEquals($streamObj->toString(), "stream\nsome text (\x00\x01\x02)\nsomething\xAF\nendstream");
    }
}
