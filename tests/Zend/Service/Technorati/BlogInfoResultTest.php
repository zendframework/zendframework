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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Technorati;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\BlogInfoResult
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $dom = self::getTestFileContentAsDom('TestBlogInfoResultUrlWithInvalidSchema.xml');
        $object = new Technorati\BlogInfoResult($dom);

        // check url
        $this->assertInstanceOf('Zend\Uri\Http', $object->getUrl());
        $this->assertEquals($object->getWeblog()->getUrl(), $object->getUrl());
    }
}
