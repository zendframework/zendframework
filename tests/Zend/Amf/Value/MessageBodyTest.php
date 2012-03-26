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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Amf\Value;

/**
 * Test case for Zend_Amf_Value_MessageBody
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class MessageBodyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->body = new \Zend\Amf\Value\MessageBody('/foo', '/bar', 'data');
    }

    public function testMessageBodyShouldAllowSettingData()
    {
        $this->assertEquals('data', $this->body->getData());
        $this->body->setData('foobar');
        $this->assertEquals('foobar', $this->body->getData());
    }

    public function testMessageBodyShouldAttachDataAsIs()
    {
        $object = new \ZendTest\Amf\TestAsset\SerializableData();
        $this->body->setData($object);
        $this->assertSame($object, $this->body->getData());
    }

    public function testReplyMethodShouldModifyTargetUri()
    {
        $this->body->setReplyMethod('?action=bar');
        $this->assertEquals('/foo?action=bar', $this->body->getTargetUri());
    }

    public function testReplyMethodShouldInsertPathSeparatorIfNoQueryStringProvided()
    {
        $this->body->setReplyMethod('bar');
        $this->assertEquals('/foo/bar', $this->body->getTargetUri());
    }

    public function testPassingNullToTargetUriShouldResultInEmptyString()
    {
        $this->body->setTargetUri(null);
        $this->assertSame('', $this->body->getTargetUri());
    }
}
