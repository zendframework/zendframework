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
 * @package    Zend_GData_Photos
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\Photos;
use Zend\GData\Photos;
use Zend\GData\App;

/**
 * @category   Zend
 * @package    Zend_GData_Photos
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Photos
 */
class PhotosAlbumQueryTest extends \PHPUnit_Framework_TestCase
{

    /**
      * Check the consistency of an album feed request
      */
    public function testSimpleAlbumQuery()
    {
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1";

        $query = new Photos\AlbumQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");

        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);

        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/album/test";

        $query->setAlbumId(null);
        $query->setAlbumName("test");

        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check for thrown exceptions upon improper albumname/id setting
      */
    public function testAlbumQueryExceptions()
      {
        $query = new Photos\AlbumQuery();
        $query->setUser("sample.user");

        try {
            $generatedString = $query->getQueryUrl();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }

        $query->setAlbumId("1");
        $query->setAlbumName("test");

        try {
            $generatedString = $query->getQueryUrl();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
      }

    /**
      * Check the consistency of an album feed request
      * Projection is set to base
      */
    public function testBaseAlbumQuery()
    {
        $queryString = "https://picasaweb.google.com/data/feed/base/user/sample.user/albumid/1";

        $query = new Photos\AlbumQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setProjection("base");

        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check the consistency of an album feed request filtered
      * for a specific tag
      */
    public function testTagFilterAlbumQuery()
    {
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1?tag=test";

        $query = new Photos\AlbumQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setTag("test");

        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check the consistency of an album feed request for private data
      */
    public function testPrivateAlbumQuery()
    {
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1?access=private";

        $query = new Photos\AlbumQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setAccess("private");

        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check the consistency of an album feed request for specifically-sized thumbnails
      */
    public function testThumbnailAlbumQuery()
    {
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1?thumbsize=72";

        $query = new Photos\AlbumQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setThumbsize("72");

        $generatedString = $query->getQueryUrl();

        // Assert that the set thumbsize is correct
        $this->assertEquals("72", $query->getThumbsize());

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

    /**
      * Check the consistency of an album feed request for specifically-sized images
      */
    public function testImgAlbumQuery()
    {
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1?imgmax=800";

        $query = new Photos\AlbumQuery();
        $query->setUser("sample.user");
        $query->setAlbumId("1");
        $query->setImgMax("800");

        // Assert that the set ImgMax is correct
        $this->assertEquals("800", $query->getImgMax());

        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);

        // Check that ImgMax is set back to null
        $queryString = "https://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1";
        $query->setImgMax(null);

        $generatedString = $query->getQueryUrl();

        // Assert that the generated query matches the correct one
        $this->assertEquals($queryString, $generatedString);
    }

}
