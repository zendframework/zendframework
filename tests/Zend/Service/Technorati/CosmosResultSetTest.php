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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

/**
 * @see Zend_Service_Technorati_CosmosResultSet
 */
require_once 'Zend/Service/Technorati/CosmosResultSet.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_CosmosResultSetTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestCosmosResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_CosmosResultSet', array($this->dom));
    }

    public function testConstructThrowsExceptionWithInvalidDom()
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_CosmosResultSet', 'DOMDocument');
    }

    public function testCosmosResultSet()
    {
        $object = new Zend_Service_Technorati_CosmosResultSet($this->dom);

        // check counts
        $this->assertType('integer', $object->totalResults());
        $this->assertEquals(2, $object->totalResults());
        $this->assertType('integer', $object->totalResultsAvailable());
        $this->assertEquals(278, $object->totalResultsAvailable());

        // check properties
        $this->assertType('Zend_Uri_Http', $object->getUrl());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com/blog'), $object->getUrl());
        $this->assertType('integer', $object->getInboundLinks());
        $this->assertEquals(278, $object->getInboundLinks());

        // check weblog
        $this->assertType('Zend_Service_Technorati_Weblog', $object->getWeblog());
        $this->assertEquals('Simone Carletti\'s Blog', $object->getWeblog()->getName());
    }

    public function testCosmosResultSetItemsInstanceOfResult()
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend_Service_Technorati_CosmosResultSet',
                    array($this->dom),
                    'Zend_Service_Technorati_CosmosResult');
    }

    public function testCosmosResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Zend_Service_Technorati_CosmosResultSet($this->dom));
    }

    public function testCosmosResultSetSiteLink()
    {
        $dom = self::getTestFileContentAsDom('TestCosmosResultSetSiteLink.xml');
        $object = new Zend_Service_Technorati_CosmosResultSet($dom);

        $this->assertEquals(3, $object->totalResults());
        $this->assertEquals(949, $object->totalResultsAvailable());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com'), $object->getUrl());
        $this->assertEquals(949, $object->getInboundLinks());
    }

    public function testCosmosResultSetSiteWeblog()
    {
        $dom = self::getTestFileContentAsDom('TestCosmosResultSetSiteWeblog.xml');
        $object = new Zend_Service_Technorati_CosmosResultSet($dom);

        $this->assertEquals(3, $object->totalResults());
        $this->assertEquals(39, $object->totalResultsAvailable());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com'), $object->getUrl());
        $this->assertEquals(39, $object->getInboundBlogs());
    }

    public function testCosmosResultSetSiteWeblogWithMissingInboundblogs()
    {
        // I can't do nothing to fix this issue in charge of Technorati
        // I only have to ensure the class doens't fail and $object->totalResultsAvailable == 0

        $dom = self::getTestFileContentAsDom('TestCosmosResultSetSiteWeblogWithMissingInboundblogs.xml');
        $object = new Zend_Service_Technorati_CosmosResultSet($dom);

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

        // $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com'), $object->getUrl());
    }

    public function testCosmosResultSetBlogLink()
    {
        $dom = self::getTestFileContentAsDom('TestCosmosResultSetBlogLink.xml');
        $object = new Zend_Service_Technorati_CosmosResultSet($dom);

        $this->assertEquals(20, $object->totalResults());
        $this->assertEquals(298, $object->totalResultsAvailable());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com/blog'), $object->getUrl());
        $this->assertEquals(298, $object->getInboundLinks());
        $this->assertEquals('Simone Carletti\'s Blog', $object->getWeblog()->getName());
    }

    public function testCosmosResultSetBlogWeblog()
    {
        $dom = self::getTestFileContentAsDom('TestCosmosResultSetBlogWeblog.xml');
        $object = new Zend_Service_Technorati_CosmosResultSet($dom);

        $this->assertEquals(20, $object->totalResults());
        $this->assertEquals(85, $object->totalResultsAvailable());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com/blog'), $object->getUrl());
        $this->assertEquals(85, $object->getInboundBlogs());
        $this->assertEquals('Simone Carletti\'s Blog', $object->getWeblog()->getName());
    }
}
