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
require_once 'Zend/Gdata/Photos/UserQuery.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Photos_PhotosUserQueryTest extends PHPUnit_Framework_TestCase
{
    
    /**
      * Check the consistency of a user feed request
      */
    public function testSimpleUserQuery()
    {
        $queryString = "http://picasaweb.google.com/data/feed/api/user/sample.user";
        
        $query = new Zend_Gdata_Photos_UserQuery();
        $query->setUser("sample.user");
        
        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check the consistency of a user feed request
      * Projection is set to base
      */
    public function testBaseUserQuery()
    {
        $queryString = "http://picasaweb.google.com/data/feed/base/user/sample.user";
        
        $query = new Zend_Gdata_Photos_UserQuery();
        $query->setUser("sample.user");
        $query->setProjection("base");
        
        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check for thrown exceptions upon improper albumname/id setting
      */
    public function testUserQueryExceptions()
      {
        $query = new Zend_Gdata_Photos_UserQuery();
        $query->setUser("sample.user");
        $query->setProjection(null);
        
        try {
            $generatedString = $query->getQueryUrl();
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
        
        $query->setProjection("api");
        $query->setUser(null);
        
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
    public function testTagFilterUserQuery()
    {
        $queryString = "http://picasaweb.google.com/data/feed/api/user/sample.user?tag=test";
        
        $query = new Zend_Gdata_Photos_UserQuery();
        $query->setUser("sample.user");
        $query->setTag("test");
        
        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check the consistency of a user feed request for private data
      */
    public function testPrivateUserQuery()
    {
        $queryString = "http://picasaweb.google.com/data/feed/api/user/sample.user?access=private";
        
        $query = new Zend_Gdata_Photos_UserQuery();
        $query->setUser("sample.user");
        $query->setAccess("private");
        
        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }
    
}
