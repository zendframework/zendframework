<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Photos;

use Zend\GData\Photos;

/**
 * @category   Zend
 * @package    Zend_GData_Photos
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Photos
 */
class PhotosPhotoQueryTest extends \PHPUnit_Framework_TestCase
{

    /**
      * Check the consistency of a user feed request
      */
    public function testSimplePhotoQuery()
    {
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1/photoid/1";

        $query = new Photos\PhotoQuery();
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
        $queryString = "https://picasaweb.google.com/data/feed/base/user/sample.user/albumid/1/photoid/1";

        $query = new Photos\PhotoQuery();
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
        $query = new Photos\PhotoQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");

        try {
            $generatedString = $query->getQueryUrl();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \Zend\GData\App\InvalidArgumentException);
        }
      }

    /**
      * Check the consistency of a user feed request filtered
      * for a specific tag
      */
    public function testTagFilterPhotoQuery()
    {
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1/photoid/1?tag=test";

        $query = new Photos\PhotoQuery();
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
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1/photoid/1?access=private";

        $query = new Photos\PhotoQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setPhotoId("1");
        $query->setAccess("private");

        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

}
