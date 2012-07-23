<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Technorati;

use Zend\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class BlogInfoResultTest extends TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestBlogInfoResult.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\BlogInfoResult', array($this->dom));
    }

    public function testBlogInfoResult()
    {
        $object = new Technorati\BlogInfoResult($this->dom);

        // check weblog
        $weblog = $object->getWeblog();
        $this->assertInstanceOf('Zend\Service\Technorati\Weblog', $weblog);
        $this->assertEquals('Simone Carletti\'s Blog', $weblog->getName());

        // check url
        $this->assertInstanceOf('Zend\Uri\Http', $object->getUrl());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://www.simonecarletti.com/blog'), $object->getUrl());

        // check inboundblogs
        $this->assertInternalType('integer', $object->getInboundBlogs());
        $this->assertEquals(86, $object->getInboundBlogs());

        // check inboundlinks
        $this->assertInternalType('integer', $object->getInboundLinks());
        $this->assertEquals(114, $object->getInboundLinks());
    }

    public function testBlogInfoResultUrlWithInvalidSchemaEqualsToWeblogUrl()
    {
        $this->markTestSkipped('Incorrect test');
        $dom = self::getTestFileContentAsDom('TestBlogInfoResultUrlWithInvalidSchema.xml');
        $object = new Technorati\BlogInfoResult($dom);

        // check url
        $this->assertInstanceOf('Zend\Uri\Http', $object->getUrl());
        $this->assertEquals($object->getWeblog()->getUrl(), $object->getUrl());
    }
}
