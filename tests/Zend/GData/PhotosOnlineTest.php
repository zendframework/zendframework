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

namespace ZendTest\GData;
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
class PhotosOnlineTest extends \PHPUnit_Framework_TestCase
{

    protected $photos = null;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_GData online tests are not enabled');
        }
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $service = 'lh2';
        $client = \Zend\GData\ClientLogin::getHttpClient($user, $pass, $service);
        $this->photos = new Photos($client);
    }

    /**
      * Verify that a given property is set to a specific value
      * and that the getter and magic variable return the same value.
      *
      * @param object $obj The object to be interrogated.
      * @param string $name The name of the property to be verified.
      * @param string $secondName 2nd level accessor function name
      * @param object $value The expected value of the property.
      */
    protected function verifyProperty($obj, $name, $secondName, $value)
    {
        $propName = $name;
        $propGetter = "get" . ucfirst($name);
        $secondGetter = "get" . ucfirst($secondName);

        $this->assertEquals($obj->$propGetter(), $obj->$propName);
        $this->assertEquals($value, $obj->$propGetter()->$secondGetter());
    }

    public function createAlbum()
    {
        $client = $this->photos;

        $album = new Photos\AlbumEntry();
        $album->setTitle($client->newTitle("testAlbum"));
        $album->setCategory(array($client->newCategory(
            'http://schemas.google.com/photos/2007#album',
            'http://schemas.google.com/g/2005#kind')));

        $newAlbum = $client->insertAlbumEntry($album);
        $this->assertEquals($album->getTitle(), $newAlbum->getTitle());
        $this->assertEquals($newAlbum->getTitle(), $client->getAlbumEntry($newAlbum->getLink('self')->href)->getTitle());

        $albumFeedUri = $newAlbum->getLink('http://schemas.google.com/g/2005#feed')->href;
        $albumFeed = $client->getAlbumFeed($albumFeedUri);
        $this->verifyProperty($albumFeed, "title", "text", "testAlbum");

        return $newAlbum;
    }

    public function createPhoto($album)
    {
        $client = $this->photos;

        $fd = $client->newMediaFileSource('Zend/GData/_files/testImage.jpg');
        $fd->setContentType('image/jpeg');

        $photo = new Photos\PhotoEntry();
        $photo->setMediaSource($fd);
        $photo->setTitle($client->newTitle("test photo"));
        $photo->setCategory(array($client->newCategory(
            'http://schemas.google.com/photos/2007#photo',
            'http://schemas.google.com/g/2005#kind')));

        $newPhoto = $client->insertPhotoEntry($photo, $album);
        $this->assertEquals($photo->getTitle(), $newPhoto->getTitle());
        $this->assertEquals($newPhoto->getTitle(), $client->getPhotoEntry($newPhoto->getLink('self')->href)->getTitle());

        $photoFeedUri = $newPhoto->getLink('http://schemas.google.com/g/2005#feed')->href;
        $photoFeed = $client->getPhotoFeed($photoFeedUri);
        $this->verifyProperty($photoFeed, "title", "text", "test photo");

        return $newPhoto;
    }

    public function updatePhotoMetaData()
    {
        $client = $this->photos;
        $album = $this->createAlbum();
        $insertedEntry = $this->createPhoto($album);

        $insertedEntry->title->text = "New Photo";
        $insertedEntry->summary->text = "Photo caption";
        $keywords = new \Zend\GData\Media\Extension\MediaKeywords();
        $keywords->setText("foo, bar, baz");
        $insertedEntry->mediaGroup->keywords = $keywords;

        $updatedEntry = $insertedEntry->save();
        return array($updatedEntry, $album);
    }

    public function createComment($photo)
    {
        $client = $this->photos;

        $comment = new Photos\CommentEntry();
        $comment->setTitle($client->newTitle("test comment"));
        $comment->setContent($client->newContent("test comment"));
        $comment->setCategory(array($client->newCategory(
            'http://schemas.google.com/photos/2007#comment',
            'http://schemas.google.com/g/2005#kind')));

        $newComment = $client->insertCommentEntry($comment, $photo);
        $this->assertEquals($comment->getContent(), $newComment->getContent());
        $this->assertEquals($newComment->getContent(), $client->getCommentEntry($newComment->getLink('self')->href)->getContent());

        return $newComment;
    }

    public function createTag($photo)
    {
        $client = $this->photos;

        $tag = new Photos\TagEntry();
        $tag->setTitle($client->newTitle("test tag"));
        $tag->setContent($client->newContent("test tag"));
        $tag->setCategory(array($client->newCategory(
            'http://schemas.google.com/photos/2007#tag',
            'http://schemas.google.com/g/2005#kind')));

        $newTag = $client->insertTagEntry($tag, $photo);
        $this->assertEquals($tag->getTitle(), $newTag->getTitle());
        $this->assertEquals($newTag->getTitle(), $client->getTagEntry($newTag->getLink('self')->href)->getTitle());

        return $newTag;
    }

    public function testCreateAlbumAndUploadPhoto()
    {
        $client = $this->photos;
        $album = $this->createAlbum();
        $photo = $this->createPhoto($album);

        // Clean up the mess
        $client->deletePhotoEntry($photo, true);
        $client->deleteAlbumEntry($album, true);
    }

    public function testUpdatePhotoMetadata()
    {
        $client = $this->photos;
        $dataArray = $this->updatePhotoMetaData();
        $updatedPhoto = $dataArray[0];
        $album = $dataArray[1];

        $this->assertTrue($updatedPhoto instanceof Photos\PhotoEntry);

        // Clean up the mess
        $client->deletePhotoEntry($updatedPhoto, true);
        $client->deleteAlbumEntry($album, true);
    }

    public function testUserFeedAndEntry()
    {
        $client = $this->photos;

        $userEntryUri = "http://picasaweb.google.com/data/entry/api/user/" .
            constant('TESTS_ZEND_GDATA_PHOTOS_USERNAME');

        $userEntry = $client->getUserEntry($userEntryUri);
        $this->verifyProperty($userEntry, "id", "text",
                "http://picasaweb.google.com/data/entry/api/user/" .
                constant('TESTS_ZEND_GDATA_PHOTOS_USERNAME'));


        $userFeed = $client->getUserFeed(constant('TESTS_ZEND_GDATA_PHOTOS_USERNAME'));
        $this->verifyProperty($userFeed, "id", "text",
                "http://picasaweb.google.com/data/feed/api/user/" .
                constant('TESTS_ZEND_GDATA_PHOTOS_USERNAME'));
    }

    public function testCreatePhotoCommentAndTag()
    {
        $client = $this->photos;
        $album = $this->createAlbum();
        $photo = $this->createPhoto($album);
        $comment = $this->createComment($photo);
        $tag = $this->createTag($photo);

        // Clean up the mess
        $client->deleteTagEntry($tag, true);
        $client->deleteCommentEntry($comment, true);
        $client->deletePhotoEntry($photo, true);
        $client->deleteAlbumEntry($album, true);
    }

    public function testInvalidEntryFetchingAndInserting()
    {
        $client = $this->photos;

        try {
            $userEntry = $client->getUserEntry(null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
        try {
            $userEntry = $client->getAlbumEntry(null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
        try {
            $photoEntry = $client->getPhotoEntry(null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
        try {
            $tagEntry = $client->getTagEntry(null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
        try {
            $commentEntry = $client->getCommentEntry(null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
        try {
            $photo = new Photos\PhotoEntry();
            $result = $client->insertPhotoEntry($photo, null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
        try {
            $comment = new Photos\CommentEntry();
            $result = $client->insertCommentEntry($comment, null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
        try {
            $tag = new Photos\TagEntry();
            $result = $client->insertTagEntry($tag, null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
    }

    public function testInvalidFeedFetching()
    {
        $client = $this->photos;

        try {
            $albumFeed = $client->getAlbumFeed(null);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof App\InvalidArgumentException);
        }
    }

}
