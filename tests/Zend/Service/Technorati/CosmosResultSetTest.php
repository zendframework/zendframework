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

namespace ZendTest\Service\Technorati;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\CosmosResultSet
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
class CosmosResultSetTest extends TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestCosmosResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\CosmosResultSet', array($this->dom));
    }

    public function testCosmosResultSet()
    {
        $object = new Technorati\CosmosResultSet($this->dom);

        // check counts
        $this->assertInternalType('integer', $object->totalResults());
        $this->assertEquals(2, $object->totalResults());
        $this->assertInternalType('integer', $object->totalResultsAvailable());
        $this->assertEquals(278, $object->totalResultsAvailable());

        // check properties
        $this->assertInstanceOf('Zend\Uri\Http', $object->getUrl());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://www.simonecarletti.com/blog'), $object->getUrl());
        $this->assertInternalType('integer', $object->getInboundLinks());
        $this->assertEquals(278, $object->getInboundLinks());

        // check weblog
        $this->assertInstanceOf('Zend\Service\Technorati\Weblog', $object->getWeblog());
        $this->assertEquals('Simone Carletti\'s Blog', $object->getWeblog()->getName());
    }

    public function testCosmosResultSetItemsInstanceOfResult()
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend\Service\Technorati\CosmosResultSet',
                    array($this->dom),
                    'Zend\Service\Technorati\CosmosResult');
    }

    public function testCosmosResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Technorati\CosmosResultSet($this->dom));
    }

    public function testCosmosResultSetSiteLink()
    {
        $dom = self::getTestFileContentAsDom('TestCosmosResultSetSiteLink.xml');
        $object = new Technorati\CosmosResultSet($dom);

        $this->assertEquals(3, $object->totalResults());
        $this->assertEquals(949, $object->totalResultsAvailable());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://www.simonecarletti.com'), $object->getUrl());
        $this->assertEquals(949, $object->getInboundLinks());
    }

    public function testCosmosResultSetSiteWeblog()
    {
        $dom = self::getTestFileContentAsDom('TestCosmosResultSetSiteWeblog.xml');
        $object = new Technorati\CosmosResultSet($dom);

        $this->assertEquals(3, $object->totalResults());
        $this->assertEquals(39, $object->totalResultsAvailable());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://www.simonecarletti.com'), $object->getUrl());
        $this->assertEquals(39, $object->getInboundBlogs());
    }

    public function testCosmosResultSetSiteWeblogWithMissingInboundblogs()
    {
        // I can't do nothing to fix this issue in charge of Technorati
        // I only have to ensure the class doens't fail and $object->totalResultsAvailable == 0

        $dom = self::getTestFileContentAsDom('TestCosmosResultSetSiteWeblogWithMissingInboundblogs.xml');
        $object = new Technorati\CosmosResultSet($dom);

        $this->assertEquals(3, $object->totalResults());
        $this->assertEquals(0, $object->totalResultsAvailable());
        $this->assertEquals(null, $object->getInboundBlogs());
    }

    public function testCosmosResultSetSiteUrlWithInvalidSchema()
    {
        // FIXME
        // Technorati allows 'url' parameter to be specified with or without www and/or schema.
        // Technorati interface works well but returned responses may include invalid URIs.
        // I have 2 possibility to fix the following issue:
        // 1. using a default http schema when URL has an invalid one
        // 2. force developers to provide a valid schema with 'url' parameter
        // The second options is the best one because not only <url>
        // but other tags are affected by this issue if you don't provide a valid schema

        // $dom = self::getTestFileContentAsDom('TestCosmosResultSetSiteUrlWithInvalidSchema.xml');
        // $object = new Zend_Service_Technorati_CosmosResultSet($dom);

        // $this->assertEquals(Zend_UriFactory::factory('http://www.simonecarletti.com'), $object->getUrl());
    }

    public function testCosmosResultSetBlogLink()
    {
        $dom = self::getTestFileContentAsDom('TestCosmosResultSetBlogLink.xml');
        $object = new Technorati\CosmosResultSet($dom);

        $this->assertEquals(20, $object->totalResults());
        $this->assertEquals(298, $object->totalResultsAvailable());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://www.simonecarletti.com/blog'), $object->getUrl());
        $this->assertEquals(298, $object->getInboundLinks());
        $this->assertEquals('Simone Carletti\'s Blog', $object->getWeblog()->getName());
    }

    public function testCosmosResultSetBlogWeblog()
    {
        $dom = self::getTestFileContentAsDom('TestCosmosResultSetBlogWeblog.xml');
        $object = new Technorati\CosmosResultSet($dom);

        $this->assertEquals(20, $object->totalResults());
        $this->assertEquals(85, $object->totalResultsAvailable());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://www.simonecarletti.com/blog'), $object->getUrl());
        $this->assertEquals(85, $object->getInboundBlogs());
        $this->assertEquals('Simone Carletti\'s Blog', $object->getWeblog()->getName());
    }
}
