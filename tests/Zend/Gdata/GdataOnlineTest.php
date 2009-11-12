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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata.php';
require_once 'Zend/Gdata/App/MediaEntry.php';
require_once 'Zend/Gdata/App/MediaFileSource.php';
require_once 'Zend/Gdata/ClientLogin.php';
require_once 'Zend/Gdata/App/InvalidArgumentException.php';

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 */
class Zend_Gdata_GdataOnlineTest extends PHPUnit_Framework_TestCase
{
    private $blog = null; // blog ID from config

    public function setUp()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->blog = constant('TESTS_ZEND_GDATA_BLOG_ID');
        $service = 'blogger';
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Zend_Gdata($client);
        $this->gdata->setMajorProtocolVersion(2);
    }

    public function testPostAndDeleteByEntry()
    {
        $postUrl = 'http://www.blogger.com/feeds/' . $this->blog .
                '/posts/default';
        $entry = $this->gdata->newEntry();
        $entry->title = $this->gdata->newTitle('PHP test blog post');
        $entry->content = $this->gdata->newContent('Blog post content...');
        $insertedEntry = $this->gdata->insertEntry($entry, $postUrl);
        $this->assertEquals('PHP test blog post', $insertedEntry->title->text);
        $this->assertEquals('Blog post content...',
                $insertedEntry->content->text);
        $this->assertTrue(
                strpos($insertedEntry->getEditLink()->href, 'http') === 0);
        $this->gdata->delete($insertedEntry);
    }

    public function testPostAndDeleteByUrl()
    {
        $postUrl = 'http://www.blogger.com/feeds/' . $this->blog .
                '/posts/default';
        $entry = $this->gdata->newEntry();
        $entry->title = $this->gdata->newTitle('PHP test blog post');
        $entry->content = $this->gdata->newContent('Blog post content...');
        $insertedEntry = $this->gdata->insertEntry($entry, $postUrl);
        $this->assertTrue(
                strpos($insertedEntry->getEditLink()->href, 'http') === 0);
        $this->gdata->delete($insertedEntry->getEditLink()->href);
    }

    public function testPostRetrieveEntryAndDelete()
    {
        $postUrl = 'http://www.blogger.com/feeds/' . $this->blog .
                '/posts/default';
        $entry = $this->gdata->newEntry();
        $entry->title = $this->gdata->newTitle(' PHP test blog post ');
        $this->assertTrue(isset($entry->title));
        $entry->content = $this->gdata->newContent('Blog post content...');

        /* testing getText and __toString */
        $this->assertEquals("PHP test blog post",
                $entry->title->getText());
        $this->assertEquals(" PHP test blog post ",
                $entry->title->getText(false));
        $this->assertEquals($entry->title->getText(),
            $entry->title->__toString());

        $insertedEntry = $this->gdata->insertEntry($entry, $postUrl);
        $retrievedEntryQuery = $this->gdata->newQuery(
                $insertedEntry->getSelfLink()->href);
        $retrievedEntry = $this->gdata->getEntry($retrievedEntryQuery);
        $this->assertTrue(
                strpos($retrievedEntry->getEditLink()->href, 'http') === 0);
        $this->gdata->delete($retrievedEntry);
    }

    public function testPostUpdateAndDeleteEntry()
    {
        $postUrl = 'http://www.blogger.com/feeds/' . $this->blog .
                '/posts/default';
        $entry = $this->gdata->newEntry();
        $entry->title = $this->gdata->newTitle('PHP test blog post');
        $entry->content = $this->gdata->newContent('Blog post content...');
        $insertedEntry = $this->gdata->insertEntry($entry, $postUrl);
        $this->assertTrue(
                strpos($insertedEntry->getEditLink()->href, 'http') === 0);
        $insertedEntry->title->text = 'PHP test blog post modified';
        $updatedEntry = $this->gdata->updateEntry($insertedEntry);
        $this->assertEquals('PHP test blog post modified',
                $updatedEntry->title->text);
        $updatedEntry->title->text = 'PHP test blog post modified twice';
        // entry->saveXML() and entry->getXML() should be the same
        $this->assertEquals($updatedEntry->saveXML(),
                $updatedEntry->getXML());
        $newlyUpdatedEntry = $this->gdata->updateEntry($updatedEntry);
        $this->assertEquals('PHP test blog post modified twice',
                $updatedEntry->title->text);
        $updatedEntry->delete();
    }

    public function testFeedImplementation()
    {
        $blogsUrl = 'http://www.blogger.com/feeds/default/blogs';
        $blogsQuery = $this->gdata->newQuery($blogsUrl);
        $retrievedFeed = $this->gdata->getFeed($blogsQuery);
        // rewind the retrieved feed first
        $retrievedFeed->rewind();

        // Make sure the iterator and array impls match
        $entry1 = $retrievedFeed->current();
        $entry2 = $retrievedFeed[0];
        $this->assertEquals($entry1, $entry2);

        /*
        TODO: Fix these tests
        // Test ArrayAccess interface
        $firstBlogTitle = $retrievedFeed[0]->title->text;
        $entries = $retrievedFeed->entry;
        $entries[0]->title->text = $firstBlogTitle . "**";
        $retrievedFeed[0] = $entries[0];
        $this->assertEquals($retrievedFeed->entry[0]->title->text,
                $retrievedFeed[0]->title->text);
        $this->assertEquals($firstBlogTitle . "**",
                $retrievedFeed[0]->title->text);
        */
    }

    public function testBadFeedRetrieval()
    {
        $feed = $this->gdata->newFeed();
        try {
            $returnedFeed = $this->gdata->getFeed($feed);
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            // we're expecting to cause an exception here
        }
    }

    public function testBadEntryRetrieval()
    {
        $entry = $this->gdata->newEntry();
        try {
            $returnedEntry = $this->gdata->getEntry($entry);
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            // we're expecting to cause an exception here
        }
    }

    public function testMediaUpload()
    {
        // the standard sevice for Gdata testing is Blogger, due to the strong
        // match to the standard Gdata/APP protocol.  However, Blogger doesn't
        // currently support media uploads, so we're using Picasa Web Albums
        // for this test instead
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->blog = constant('TESTS_ZEND_GDATA_BLOG_ID');
        $service = 'lh2';
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $gd = new Zend_Gdata($client);

        // setup the photo content
        $fs = $gd->newMediaFileSource('Zend/Gdata/_files/testImage.jpg');
        $fs->setContentType('image/jpeg');


        // create a new picasa album
        $albumEntry = $gd->newEntry();
        $albumEntry->setTitle($gd->newTitle('My New Test Album'));
        $albumEntry->setCategory(array($gd->newCategory(
                'http://schemas.google.com/photos/2007#album',
                'http://schemas.google.com/g/2005#kind'
                )));
        $createdAlbumEntry = $gd->insertEntry($albumEntry,
                'http://picasaweb.google.com/data/feed/api/user/default');
        $this->assertEquals('My New Test Album',
                $createdAlbumEntry->title->text);
        $albumUrl = $createdAlbumEntry->getLink('http://schemas.google.com/g/2005#feed')->href;

        // post the photo to the new album, without any metadata
        // other than the slug
        // add a slug header to the media file source
        $fs->setSlug('Going to the park');
        $createdPhotoBinaryOnly = $gd->insertEntry($fs, $albumUrl);
        $this->assertEquals('Going to the park',
                $createdPhotoBinaryOnly->title->text);

        // post the photo to the new album along with the entry
        // remove slug header from the media file source
        $fs->setSlug(null);

        // setup an entry with metadata
        $mediaEntry = $gd->newMediaEntry();
        $mediaEntry->setMediaSource($fs);

        $mediaEntry->setTitle($gd->newTitle('My New Test Photo'));
        $mediaEntry->setSummary($gd->newSummary('My New Test Photo Summary'));
        $mediaEntry->setCategory(array($gd->newCategory(
                'http://schemas.google.com/photos/2007#photo ',
                'http://schemas.google.com/g/2005#kind'
                )));
        $createdPhotoMultipart = $gd->insertEntry($mediaEntry, $albumUrl);
        $this->assertEquals('My New Test Photo',
                $createdPhotoMultipart->title->text);

        // cleanup and remove the album
        // first we wait 5 seconds
        sleep(5);
        try {
            $albumEntry->delete();
        } catch (Zend_Gdata_App_Exception $e) {
            $this->fail('Tried to delete the test album, got exception: ' .
                $e->getMessage());
        }
    }

    function testIsAuthenticated()
    {
        $this->assertTrue($this->gdata->isAuthenticated());
    }

    function testRetrieveNextAndPreviousFeedsFromService()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->blog = constant('TESTS_ZEND_GDATA_BLOG_ID');
        $service = 'youtube';
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $gd = new Zend_Gdata($client);

        $feed = $gd->getFeed(
            'http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured',
            'Zend_Gdata_App_Feed');

        $this->assertNotNull($feed);
        $this->assertTrue($feed instanceof Zend_Gdata_App_Feed);
        $this->assertEquals($feed->count(), 25);

        $nextFeed = $gd->getNextFeed($feed);

        $this->assertNotNull($nextFeed);
        $this->assertTrue($nextFeed instanceof Zend_Gdata_App_Feed);
        $this->assertEquals($nextFeed->count(), 25);

        $previousFeed = $gd->getPreviousFeed($nextFeed);

        $this->assertNotNull($previousFeed);
        $this->assertTrue($previousFeed instanceof Zend_Gdata_App_Feed);
        $this->assertEquals($previousFeed->count(), 25);

    }

    function testRetrieveNextFeedAndPreviousFeedsFromFeed()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->blog = constant('TESTS_ZEND_GDATA_BLOG_ID');
        $service = 'youtube';
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $gd = new Zend_Gdata($client);

        $feed = $gd->getFeed(
            'http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured',
            'Zend_Gdata_App_Feed');

        $nextFeed = $feed->getNextFeed();

        $this->assertNotNull($nextFeed);
        $this->assertTrue($nextFeed instanceof Zend_Gdata_App_Feed);
        $this->assertEquals($nextFeed->count(), 25);

        $previousFeed = $nextFeed->getPreviousFeed();

        $this->assertNotNull($previousFeed);
        $this->assertTrue($previousFeed instanceof Zend_Gdata_App_Feed);
        $this->assertEquals($previousFeed->count(), 25);

    }

    public function testDisableXMLToObjectMappingReturnsStringForFeed()
    {
        $gdata = new Zend_Gdata();
        $gdata->useObjectMapping(false);
        $xmlString = $gdata->getFeed(
            'http://gdata.youtube.com/feeds/api/standardfeeds/top_rated');
        $this->assertEquals('string', gettype($xmlString));
    }

    public function testDisableXMLToObjectMappingReturnsStringForEntry()
    {
        $gdata = new Zend_Gdata();
        $gdata->useObjectMapping(false);
        $xmlString = $gdata->getFeed(
            'http://gdata.youtube.com/feeds/api/videos/O4SWAfisH-8');
        $this->assertEquals('string', gettype($xmlString));
    }

    public function testDisableAndReEnableXMLToObjectMappingReturnsObject()
    {
        $gdata = new Zend_Gdata();
        $gdata->useObjectMapping(false);
        $xmlString = $gdata->getEntry(
            'http://gdata.youtube.com/feeds/api/videos/O4SWAfisH-8');
        $this->assertEquals('string', gettype($xmlString));
        $gdata->useObjectMapping(true);
        $entry = $gdata->getEntry(
            'http://gdata.youtube.com/feeds/api/videos/O4SWAfisH-8');
        $this->assertTrue($entry instanceof Zend_Gdata_Entry);
    }

}
