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

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Gdata/Photos.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';
require_once 'Zend/Gdata/ClientLogin.php';
require_once 'Zend/Gdata/App/InvalidArgumentException.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_PhotosOnlineTest extends PHPUnit_Framework_TestCase
{
    
    protected $photos = null;
    
    public function setUp()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $service = 'lh2';
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $this->photos = new Zend_Gdata_Photos($client);
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

        $album = new Zend_Gdata_Photos_AlbumEntry();
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
        
        $fd = $client->newMediaFileSource('Zend/Gdata/_files/testImage.jpg');
        $fd->setContentType('image/jpeg');
        
        $photo = new Zend_Gdata_Photos_PhotoEntry();
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
        $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
        $keywords->setText("foo, bar, baz");
        $insertedEntry->mediaGroup->keywords = $keywords;
    
        $updatedEntry = $insertedEntry->save();
        return array($updatedEntry, $album);
    }
    
    public function createComment($photo)
    {
        $client = $this->photos;
        
        $comment = new Zend_Gdata_Photos_CommentEntry();
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
        
        $tag = new Zend_Gdata_Photos_TagEntry();
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
      
        $this->assertTrue($updatedPhoto instanceof Zend_Gdata_Photos_PhotoEntry);
      
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
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
        try {
            $userEntry = $client->getAlbumEntry(null);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
        try {
            $photoEntry = $client->getPhotoEntry(null);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
        try {
            $tagEntry = $client->getTagEntry(null);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
        try {
            $commentEntry = $client->getCommentEntry(null);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
        try {
            $photo = new Zend_Gdata_Photos_PhotoEntry();
            $result = $client->insertPhotoEntry($photo, null);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
        try {
            $comment = new Zend_Gdata_Photos_CommentEntry();
            $result = $client->insertCommentEntry($comment, null);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
        try {
            $tag = new Zend_Gdata_Photos_TagEntry();
            $result = $client->insertTagEntry($tag, null);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
    }
    
    public function testInvalidFeedFetching()
    {
        $client = $this->photos;
        
        try {
            $albumFeed = $client->getAlbumFeed(null);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Gdata_App_InvalidArgumentException);
        }
    }
    
}
