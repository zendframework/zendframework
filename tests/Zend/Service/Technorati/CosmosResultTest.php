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
 * @see Zend_Service_Technorati_CosmosResult
 */
require_once 'Zend/Service/Technorati/CosmosResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_CosmosResultTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->domElements = self::getTestFileElementsAsDom('TestCosmosResultSet.xml');
    }
    
    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_CosmosResult', array($this->domElements->item(0)));
    }
    
    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_CosmosResult', 'DOMElement');
    }

    public function testCosmosResultSerialization()
    {
        $this->_testResultSerialization(new Zend_Service_Technorati_CosmosResult($this->domElements->item(0)));
    }

    public function testCosmosResultSiteLink()
    {
        $domElements = self::getTestFileElementsAsDom('TestCosmosResultSetSiteLink.xml');
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(0));
        
        $this->assertType('Zend_Service_Technorati_Weblog', $object->getWeblog());
        $this->assertContains('Gioxx', $object->getWeblog()->getName());
        
        $this->assertType('Zend_Uri_Http', $object->getNearestPermalink());
        $this->assertEquals(Zend_Uri::factory('http://gioxx.org/2007/11/05/il-passaggio-a-mac-le-11-risposte/'), $object->getNearestPermalink());
        
        $this->assertType('string', $object->getExcerpt());
        $this->assertContains('Ho intenzione di prendere il modello bianco', $object->getExcerpt());
        
        $this->assertType('Zend_Date', $object->getLinkCreated());
        $this->assertEquals(new Zend_Date('2007-11-11 20:07:11 GMT'), $object->getLinkCreated());
        
        $this->assertType('Zend_Uri_Http', $object->getLinkUrl());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com/blog/2007/04/parallels-desktop-overview.php'), $object->getLinkUrl());
        
        // test an other element to prevent cached values
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(1));
        $this->assertContains('Progetto-Seo', $object->getWeblog()->getName());
        $this->assertEquals(Zend_Uri::factory('http://www.progetto-seo.com/motori-di-ricerca/links-interni'), $object->getNearestPermalink());
        $this->assertContains('soprattutto Google', $object->getExcerpt());
        $this->assertEquals(new Zend_Date('2007-11-10 08:57:22 GMT'), $object->getLinkCreated());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com/blog/2007/04/google-yahoo-ask-nofollow.php'), $object->getLinkUrl());
    }

    public function testCosmosResultSiteLinkNearestPermalinkIsNull()
    {
        $domElements = self::getTestFileElementsAsDom('TestCosmosResultSetSiteLink.xml');
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(2));
        $this->assertContains('Controrete', $object->getWeblog()->getName());
        $this->assertNull($object->getNearestPermalink());
    }

    public function testCosmosResultSiteWeblog()
    {
        $domElements = self::getTestFileElementsAsDom('TestCosmosResultSetSiteWeblog.xml');
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(0));
        
        $this->assertType('Zend_Service_Technorati_Weblog', $object->getWeblog());
        $this->assertContains('Simone Carletti', $object->getWeblog()->getName());
        
        $this->assertType('Zend_Uri_Http', $object->getLinkUrl());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com'), $object->getLinkUrl());
        
        // test an other element to prevent cached values
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(1));
        $this->assertContains('Gioxx', $object->getWeblog()->getName());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com'), $object->getLinkUrl());
    }

    public function testCosmosResultBlogLink()
    {
        // same as testSearchResultSiteLink
    }

    public function testCosmosResultBlogWeblog()
    {
        // same as testSearchResultSiteWeblog
    }
}
