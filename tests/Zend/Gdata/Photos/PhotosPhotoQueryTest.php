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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Photos.php';
require_once 'Zend/Gdata/Photos/PhotoQuery.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Photos_PhotosPhotoQueryTest extends PHPUnit_Framework_TestCase
{
    
    /**
      * Check the consistency of a user feed request
      */
    public function testSimplePhotoQuery()
    {
        $queryString = "http://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1/photoid/1";
        
        $query = new Zend_Gdata_Photos_PhotoQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setPhotoId("1");
        
        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check the consistency of a user feed request
      * Projection is set to base
      */
    public function testBasePhotoQuery()
    {
        $queryString = "http://picasaweb.google.com/data/feed/base/user/sample.user/albumid/1/photoid/1";
        
        $query = new Zend_Gdata_Photos_PhotoQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setPhotoId("1");
        $query->setProjection("base");
        
        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check for thrown exceptions upon improper photoid setting
      */
    public function testPhotoQueryExceptions()
      {
        $query = new Zend_Gdata_Photos_PhotoQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        
        try {
            $generatedString = $query->getQueryUrl();
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
      }

    /**
      * Check the consistency of a user feed request filtered
      * for a specific tag
      */
    public function testTagFilterPhotoQuery()
    {
        $queryString = "http://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1/photoid/1?tag=test";
        
        $query = new Zend_Gdata_Photos_PhotoQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setPhotoId("1");
        $query->setTag("test");
        
        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check the consistency of a user feed request for private data
      */
    public function testPrivatePhotoQuery()
    {
        $queryString = "http://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1/photoid/1?access=private";
        
        $query = new Zend_Gdata_Photos_PhotoQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setPhotoId("1");
        $query->setAccess("private");
        
        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }
    
}
