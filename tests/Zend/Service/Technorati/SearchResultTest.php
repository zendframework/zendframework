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
 * @see Zend_Service_Technorati_SearchResult
 */
require_once 'Zend/Service/Technorati/SearchResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_SearchResultTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->domElements = self::getTestFileElementsAsDom('TestSearchResultSet.xml');
    }
    
    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_SearchResult', array($this->domElements->item(0)));
    }
    
    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_SearchResult', 'DOMElement');
    }

    public function testSearchResult()
    {
        $object = new Zend_Service_Technorati_SearchResult($this->domElements->item(0));
        
        // check properties
        $this->assertType('string', $object->getTitle());
        $this->assertContains('El SDK de Android', $object->getTitle());
        $this->assertType('string', $object->getExcerpt());
        $this->assertContains('[ Android]', $object->getExcerpt());
        $this->assertType('Zend_Uri_Http', $object->getPermalink());
        $this->assertEquals(Zend_Uri_Http::factory('http://blogs.eurielec.etsit.upm.es/miotroblog/?p=271'), $object->getPermalink());
        $this->assertType('Zend_Date', $object->getCreated());
        $this->assertEquals(new Zend_Date('2007-11-14 22:18:04 GMT'), $object->getCreated());
        
        // check weblog
        $this->assertType('Zend_Service_Technorati_Weblog', $object->getWeblog());
        $this->assertContains('Mi otro blog', $object->getWeblog()->getName());
    }

    public function testSearchResultSerialization()
    {
        $this->_testResultSerialization(new Zend_Service_Technorati_SearchResult($this->domElements->item(0)));
    }

    public function testSearchResultSpecialEncoding()
    {
        $object = new Zend_Service_Technorati_SearchResult($this->domElements->item(1));
        
        $this->assertContains('質の超濃い読者をどかんと5000件集めます', $object->getTitle());
    }
}
