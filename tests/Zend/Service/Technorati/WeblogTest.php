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
 * @see Zend_Service_Technorati_Weblog
 */
require_once 'Zend/Service/Technorati/Weblog.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_WeblogTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->domElement = self::getTestFileElementAsDom('TestWeblog.xml', '//weblog');
    }
    
    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_Weblog', array($this->domElement));
    }
    
    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_Weblog', 'DOMElement');
    }
    
    public function testWeblog()
    {
        $weblog = new Zend_Service_Technorati_Weblog($this->domElement);
                
        // check name
        $this->assertEquals('Roby Web World Italia', $weblog->getName());
        // check URL
        $this->assertEquals(Zend_Uri::factory('http://robyww.blogspot.com'), $weblog->getUrl());
        // check Atom Url
        $this->assertEquals(Zend_Uri::factory('http://robyww.blogspot.com/feeds/posts/atom'), $weblog->getAtomUrl());
        // check RSS Url
        $this->assertEquals(Zend_Uri::factory('http://robyww.blogspot.com/feeds/posts/rss'), $weblog->getRssUrl());
        // check inbound blogs
        $this->assertEquals(71, $weblog->getInboundBlogs());
        // check inbound links
        $this->assertEquals(103, $weblog->getInboundLinks());
        // check last update
        $this->assertEquals(new Zend_Date('2007-11-11 08:47:26 GMT'), $weblog->getLastUpdate());
        // check rank
        $this->assertEquals(93473, $weblog->getRank());
        // check authors
        $var = $weblog->getAuthors();
        $this->assertType('array', $var);
        $this->assertEquals(1, sizeof($var));
        // check photo
        $this->assertEquals(false, $weblog->hasPhoto());
        // check lat and lon
        $this->assertNull($weblog->getLat());
        $this->assertNull($weblog->getLon());
    }

    public function testWeblogWithTwoAuthors() 
    {
        $domElement = self::getTestFileElementAsDom('TestWeblogTwoAuthors.xml', '//weblog');
        $weblog = new Zend_Service_Technorati_Weblog($domElement);
        
        $authors = $weblog->getAuthors();
        
        // check whether $authors is an array with valid length
        $this->assertType('array', $authors); 
        $this->assertEquals(2, sizeof($authors));
        
        // check first author
        $author = $authors[0];
        $this->assertType('Zend_Service_Technorati_Author', $author);
        $this->assertEquals('rfilippini', $author->getUsername());
        
        // check second author, be sure it's not the first one
        $author = $authors[1];
        $this->assertType('Zend_Service_Technorati_Author', $author);
        $this->assertEquals('Rinzi', $author->getUsername());
    }
    
    public function testSetGet()
    {
        $weblog = new Zend_Service_Technorati_Weblog($this->domElement);
        
        // check name
        $set = 'foo';
        $get = $weblog->setName($set)->getName();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);
        
        // check URL
        
        $set = Zend_Uri::factory('http://www.simonecarletti.com/');
        $get = $weblog->setUrl($set)->getUrl();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals($set, $get);
        
        $set = 'http://www.simonecarletti.com/';
        $get = $weblog->setUrl($set)->getUrl();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals(Zend_Uri::factory($set), $get);
        
        $set = 'http:::/foo';
        try {
            $weblog->setUrl($set);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch(Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid URI", $e->getMessage());
        }
        
        // check Atom URL
        
        $set = Zend_Uri::factory('http://www.simonecarletti.com/');
        $get = $weblog->setAtomUrl($set)->getAtomUrl();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals($set, $get);
        
        $set = 'http://www.simonecarletti.com/';
        $get = $weblog->setAtomUrl($set)->getAtomUrl();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals(Zend_Uri::factory($set), $get);
        
        $set = 'http:::/foo';
        try {
            $weblog->setAtomUrl($set);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch(Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid URI", $e->getMessage());
        }
        
        // check RSS Url
        
        $set = Zend_Uri::factory('http://www.simonecarletti.com/');
        $get = $weblog->setRssUrl($set)->getRssUrl();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals($set, $get);
        
        $set = 'http://www.simonecarletti.com/';
        $get = $weblog->setRssUrl($set)->getRssUrl();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals(Zend_Uri::factory($set), $get);
        
        $set = 'http:::/foo';
        try {
            $weblog->setRssUrl($set);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch(Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid URI", $e->getMessage());
        }
        
        // check inbound blogs
        
        $set = rand();
        $get = $weblog->setInboundBlogs($set)->getInboundBlogs();
        $this->assertType('integer', $get);
        $this->assertEquals($set, $get);
        
        $set = (string) rand();
        $get = $weblog->setInboundBlogs($set)->getInboundBlogs();
        $this->assertType('integer', $get);
        $this->assertEquals((int) $set, $get);
        
        // check inbound links
        
        $set = rand();
        $get = $weblog->setInboundLinks($set)->getInboundLinks();
        $this->assertType('integer', $get);
        $this->assertEquals((int) $set, $get);
        
        $set = (string) rand();
        $get = $weblog->setInboundLinks($set)->getInboundLinks();
        $this->assertType('integer', $get);
        $this->assertEquals((int) $set, $get);
        
        // last update
        
        $set = '2007-11-11 08:47:26 GMT';
        $get = $weblog->setLastUpdate($set)->getLastUpdate();
        $this->assertType('Zend_Date', $get);
        $this->assertEquals(new Zend_Date($set), $get);
        
        /* not supported
        $set = time();
        $get = $weblog->setLastUpdate($set)->getLastUpdate();
        $this->assertType('integer', $get);
        $this->assertEquals($set, $get); */
        
        $set = '200ty';
        try {
            $weblog->setLastUpdate($set);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch(Zend_Service_Technorati_Exception $e) {
            $this->assertContains("valid Date/Time", $e->getMessage());
        }
        
        // check rank
        
        $set = rand();
        $get = $weblog->setRank($set)->getRank();
        $this->assertType('integer', $get);
        $this->assertEquals((int) $set, $get);
        
        $set = (string) rand();
        $get = $weblog->setRank($set)->getRank();
        $this->assertType('integer', $get);
        $this->assertEquals((int) $set, $get);
        
        // check hasPhoto
        
        $set = false;
        $get = $weblog->setHasPhoto($set)->hasPhoto();
        $this->assertType('boolean', $get);
        $this->assertEquals($set, $get);
        
        $set = 0;
        $get = $weblog->setHasPhoto($set)->hasPhoto();
        $this->assertType('boolean', $get);
        $this->assertEquals((bool) $set, $get);
        
        // check lat
        
        $set = 1.3;
        $get = $weblog->setLat($set)->getLat();
        $this->assertType('float', $get);
        $this->assertEquals($set, $get);
        
        $set = '1.3';
        $get = $weblog->setLat($set)->getLat();
        $this->assertType('float', $get);
        $this->assertEquals((float) $set, $get);
        
        // check lon
        
        $set = 1.3;
        $get = $weblog->setLon($set)->getLon();
        $this->assertType('float', $get);
        $this->assertEquals($set, $get);
        
        $set = '1.3';
        $get = $weblog->setLon($set)->getLon();
        $this->assertType('float', $get);
        $this->assertEquals((float) $set, $get);
    }
}
