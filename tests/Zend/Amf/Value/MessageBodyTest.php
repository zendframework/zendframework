<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\Value;

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
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
