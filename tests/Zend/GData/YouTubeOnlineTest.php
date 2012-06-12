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
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData;
use Zend\GData\YouTube;
use Zend\GData;
use Zend\GData\App;

/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class YouTubeOnlineTest extends \PHPUnit_Framework_TestCase
{

    /** @var YouTube */
    public $gdata;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_GData online tests are not enabled');
        }
        $this->ytAccount = constant('TESTS_ZEND_GDATA_YOUTUBE_ACCOUNT');
        $this->user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $this->pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->subscriptionTypeSchema = 'http://gdata.youtube.com/schemas/' .
            '2007/subscriptiontypes.cat';
        $this->gdata = new YouTube();
    }

    public function tearDown()
    {
    }

    public function testRetrieveSubScriptionFeed()
    {
        $feed = $this->gdata->getSubscriptionFeed($this->ytAccount);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('Subscriptions of ' . $this->ytAccount,
            $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
    }

    public function testRetrieveContactFeed()
    {
        $feed = $this->gdata->getContactFeed($this->ytAccount);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('Contacts of ' . $this->ytAccount,
            $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
        $this->assertEquals('ytgdatatest1', $feed->entry[0]->username->text);
    }

    public function testRetrieveUserVideos()
    {
        $feed = $this->gdata->getUserUploads($this->ytAccount);
        $this->assertEquals('Uploads by ' . $this->ytAccount,
            $feed->title->text);
        $this->assertTrue(count($feed->entry) === 1);
    }

    public function testRetrieveVideoFeed()
    {
        $feed = $this->gdata->getVideoFeed();

        $query = new YouTube\VideoQuery();
        $query->setVideoQuery('puppy');
        $feed = $this->gdata->getVideoFeed($query);
        foreach ($feed as $videoEntry) {
            $videoResponsesLink = $videoEntry->getVideoResponsesLink();
            $videoRatingsLink = $videoEntry->getVideoRatingsLink();
            $videoComplaintsLink = $videoEntry->getVideoComplaintsLink();
        }

        $feed = $this->gdata->getVideoFeed($query->getQueryUrl());
    }

    public function testRetrieveVideoEntry()
    {
        $entry = $this->gdata->getVideoEntry('66wj2g5yz0M');
        $this->assertEquals('TestMovie', $entry->title->text);

        $entry = $this->gdata->getVideoEntry(null, 'https://gdata.youtube.com/feeds/api/videos/66wj2g5yz0M');
        $this->assertEquals('TestMovie', $entry->title->text);
    }

    public function testRetrieveOtherFeeds()
    {
        $feed = $this->gdata->getRelatedVideoFeed('66wj2g5yz0M');
        $feed = $this->gdata->getVideoResponseFeed('66wj2g5yz0M');
        $feed = $this->gdata->getVideoCommentFeed('66wj2g5yz0M');
        $feed = $this->gdata->getWatchOnMobileVideoFeed();
        $feed = $this->gdata->getUserFavorites($this->ytAccount);
    }

    public function testRetrieveUserProfile()
    {
        $entry = $this->gdata->getUserProfile($this->ytAccount);
        $this->assertEquals($this->ytAccount . ' Channel', $entry->title->text);
        $this->assertEquals($this->ytAccount, $entry->username->text);
        $this->assertEquals('I\'m a lonely test account, with little to do but sit around and wait for people to use me.  I get bored in between releases and often sleep to pass the time.  Please use me more often, as I love to show off my talent in breaking your code.',
                $entry->description->text);
        $this->assertEquals(32, $entry->age->text);
        $this->assertEquals('crime and punishment, ps i love you, the stand', $entry->books->text);
        $this->assertEquals('Google', $entry->company->text);
        $this->assertEquals('software engineering, information architecture, photography, travel', $entry->hobbies->text);
        $this->assertEquals('Mountain View, CA', $entry->hometown->text);
        $this->assertEquals('San Francisco, CA 94114, US', $entry->location->text);
        $this->assertEquals('monk, heroes, law and order, top gun', $entry->movies->text);
        $this->assertEquals('imogen heap, frou frou, thievory corp, morcheeba, barenaked ladies', $entry->music->text);
        $this->assertEquals('Developer Programs', $entry->occupation->text);
        $this->assertEquals('University of the World', $entry->school->text);
        $this->assertEquals('f', $entry->gender->text);
        $this->assertEquals('taken', $entry->relationship->text);
    }

    public function testRetrieveAndUpdatePlaylistList()
    {

        $service = YouTube::AUTH_SERVICE_NAME;
        $authenticationURL= 'https://www.google.com/youtube/accounts/ClientLogin';
        $httpClient = GData\ClientLogin::getHttpClient(
            $this->user,
            $this->pass,
            $service,
            null, // client
            'Google-UnitTests-1.0', // source
            null, // $loginToken
            null, // loginCaptcha
            $authenticationURL);

        $this->gdata = new YouTube($httpClient,
            'Google-UnitTests-1.0', 'ytapi-gdataops-12345-u78960r7-0',
            'AI39si6c-ZMGFZ5fkDAEJoCNHP9LOM2LSO1XuycZF7Eyu1IuvkioESq' .
            'zRcf3voDLymIUGIrxdMx2aTufdbf5D7E51NyLYyfeaw');

        $this->gdata->setMajorProtocolVersion(2);
        $feed = $this->gdata->getPlaylistListFeed($this->ytAccount);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('Playlists of ' . $this->ytAccount,
            $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        $i = 0;
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
            if ($i == 0) {
                $entry->title->setText('new playlist title');
                $entry->save();
            }
            $i++;
        }
    }

    public function testRetrievePlaylistV2()
    {
      $this->gdata->setMajorProtocolVersion(2);
      $feed = $this->gdata->getPlaylistListFeed($this->ytAccount);
      $firstEntry = $feed->entries[0];
      $this->assertTrue($firstEntry instanceof YouTube\PlaylistListEntry);
      $this->assertTrue($firstEntry->getSummary()->text != null);
    }

    public function testRetrievePlaylistVideoFeed()
    {
        $listFeed = $this->gdata->getPlaylistListFeed($this->ytAccount);

        $feed = $this->gdata->getPlaylistVideoFeed($listFeed->entry[0]->feedLink[0]->href);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
    }

    public function testRetrieveTopRatedVideos()
    {
        $feed = $this->gdata->getTopRatedVideoFeed();
        $this->assertTrue($feed->totalResults->text > 10);
        $this->assertEquals('Top Rated', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->rating->average > 3);
            $this->assertEquals(1, $entry->rating->min);
            $this->assertEquals(5, $entry->rating->max);
            $this->assertTrue($entry->rating->numRaters > 2);
        }
    }

    public function testRetrieveTopRatedVideosV2()
    {
        $this->gdata->setMajorProtocolVersion(2);
        $feed = $this->gdata->getTopRatedVideoFeed();
        $client = $this->gdata->getHttpClient();
        $positionOfAPIProjection = strpos(
            $client->getLastRawRequest(), "/feeds/api/");
        $this->assertTrue(is_numeric($positionOfAPIProjection));
    }

    public function testRetrieveMostViewedVideosV2()
    {
        $this->gdata->setMajorProtocolVersion(2);
        $feed = $this->gdata->getMostViewedVideoFeed();
        $client = $this->gdata->getHttpClient();
        $positionOfAPIProjection = strpos(
            $client->getLastRawRequest(), "/feeds/api/");
        $this->assertTrue(is_numeric($positionOfAPIProjection));
    }

    public function testRetrieveRecentlyFeaturedVideosV2()
    {
        $this->gdata->setMajorProtocolVersion(2);
        $feed = $this->gdata->getRecentlyFeaturedVideoFeed();
        $client = $this->gdata->getHttpClient();
        $positionOfAPIProjection = strpos(
            $client->getLastRawRequest(), "/feeds/api/");
        $this->assertTrue(is_numeric($positionOfAPIProjection));
    }

    public function testWatchOnMobileVideosV2()
    {
        $this->gdata->setMajorProtocolVersion(2);
        $feed = $this->gdata->getWatchOnMobileVideoFeed();
        $client = $this->gdata->getHttpClient();
        $positionOfAPIProjection = strpos(
            $client->getLastRawRequest(), "/feeds/api/");
        $this->assertTrue(is_numeric($positionOfAPIProjection));
    }

    public function testRetrieveMostViewedVideos()
    {
        $feed = $this->gdata->getMostViewedVideoFeed();
        $this->assertTrue($feed->totalResults->text > 10);
        $this->assertEquals('Most Viewed', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            if ($entry->rating) {
                $this->assertEquals(1, $entry->rating->min);
                $this->assertEquals(5, $entry->rating->max);
            }
        }
    }

    /**
     * @group ZF-9479
     */
    public function testPerformV2Query()
    {
        $this->gdata->setMajorProtocolVersion(2);
        $query = $this->gdata->newVideoQuery();
        $query->videoQuery = 'zend framework';
        $query->startIndex = 0;
        $query->maxResults = 10;
        $query->orderBy = 'viewCount';
        $query->safeSearch = 'strict';
        $videoFeed = $this->gdata->getVideoFeed($query);
        $this->assertTrue(count($videoFeed->entry) > 0,
            'Could not retrieve a single entry for location search:' .
                $query->getQueryUrl(2));
    }

    public function testPerformV2Query_Location()
    {
        $this->gdata->setMajorProtocolVersion(2);
        $query = $this->gdata->newVideoQuery();
        // Setting location to New York City
        $query->setLocation('-37.0625,-95.677068');
        $query->setLocationRadius('1000km');
        $videoFeed = $this->gdata->getVideoFeed($query);
        $this->assertTrue(count($videoFeed->entry) > 0,
            'Could not retrieve a single entry for location search:' .
            $query->getQueryUrl(2));
    }

    public function testPerformV2Query_SafeSearch()
    {
        $this->gdata->setMajorProtocolVersion(2);
        $query = $this->gdata->newVideoQuery();
        $query->setSafeSearch('strict');
        $videoFeed = $this->gdata->getVideoFeed($query);
        $this->assertTrue(count($videoFeed->entry) > 0,
            'Could not retrieve a single entry for safeSearch=strict search:' .
            $query->getQueryUrl(2));
    }

    public function testPeformV2Query_Uploader()
    {
        $this->gdata->setMajorProtocolVersion(2);
        $query = $this->gdata->newVideoQuery();
        $query->setUploader('partner');
        $videoFeed = $this->gdata->getVideoFeed($query);
        $this->assertTrue(count($videoFeed->entry) > 0,
            'Could not retrieve a single entry for uploader=partner search:' .
            $query->getQueryUrl(2));

        foreach($videoFeed as $videoEntry) {
            $mg = $videoEntry->getMediaGroup();
            $this->assertEquals('partner',
                $mg->getMediaCredit()->getYTtype());
        }
    }

    public function testAddUpdateAndDeletePlaylistV2()
    {
        $service = YouTube::AUTH_SERVICE_NAME;
        $authenticationURL =
            'https://www.google.com/youtube/accounts/ClientLogin';
        $httpClient = GData\ClientLogin::getHttpClient(
            $this->user,
            $this->pass,
            $service,
            null, // client
            'Google-UnitTests-1.0', // source
            null, // $loginToken
            null, // loginCaptcha
            $authenticationURL);

        $yt = new YouTube(
            $httpClient, 'Google-UnitTests-1.0',
            'ytapi-gdataops-12345-u78960r7-0',
            'AI39si6c-ZMGFZ5fkDAEJoCNHP9LOM2LSO1XuycZF7E' .
            'yu1IuvkioESqzRcf3voDLymIUGIrxdMx2aTufdbf5D7E51NyLYyfeaw');

        $yt->setMajorProtocolVersion(2);
        $feed = $yt->getPlaylistListFeed($this->ytAccount);

        // Add new
        $newPlaylist = $yt->newPlaylistListEntry();
        $newPlaylist->setMajorProtocolVersion(2);
        $titleString = $this->generateRandomString(10);
        $newPlaylist->title = $yt->newTitle()->setText($titleString);
        $newPlaylist->summary = $yt->newSummary()->setText('testing');
        $postUrl = 'https://gdata.youtube.com/feeds/api/users/default/playlists';
        $successfulInsertion = true;

        try {
            $yt->insertEntry($newPlaylist, $postUrl);
        } catch (App\Exception $e) {
            $successfulInsertion = false;
        }

        $this->assertTrue($successfulInsertion, 'Failed to insert a new ' .
            'playlist.');

        $playlistListFeed = $yt->getPlaylistListFeed('default');

        $playlistFound = false;
        $newPlaylistEntry = null;

        foreach ($playlistListFeed as $playlistListEntry) {
            if ($playlistListEntry->title->text == $titleString) {
                $playlistFound = true;
                $newPlaylistEntry = $playlistListEntry;
                break;
            }
        }

        $this->assertTrue($playlistFound, 'Could not find the newly inserted ' .
            'playlist.');

        // Update it
        $newTitle = $this->generateRandomString(10);
        $newPlaylistEntry->title->setText($newTitle);
        $updatedSuccesfully = true;
        try {
            $newPlaylistEntry->save();
        } catch (App\Exception $e) {
            $updatedSuccesfully = false;
        }

        $this->assertTrue($updatedSuccesfully, 'Could not succesfully update ' .
            'a new playlist.');

        // Delete it
        $deletedSuccesfully = true;
        try {
            $newPlaylistEntry->delete();
        } catch (App\Exception $e) {
            $deletedSuccesfully = false;
        }

        $this->assertTrue($deletedSuccesfully, 'Could not succesfully delete ' .
            'a new playlist.');
    }

    public function testAddAndDeleteSubscriptionToChannelV2()
    {
        $service = YouTube::AUTH_SERVICE_NAME;
        $authenticationURL =
            'https://www.google.com/youtube/accounts/ClientLogin';
        $httpClient = GData\ClientLogin::getHttpClient(
            $this->user,
            $this->pass,
            $service,
            null, // client
            'Google-UnitTests-1.0', // source
            null, // $loginToken
            null, // loginCaptcha
            $authenticationURL);

        $yt = new YouTube(
            $httpClient, 'Google-UnitTests-1.0',
            'ytapi-gdataops-12345-u78960r7-0',
            'AI39si6c-ZMGFZ5fkDAEJoCNHP9LOM2LSO1XuycZF7E' .
            'yu1IuvkioESqzRcf3voDLymIUGIrxdMx2aTufdbf5D7E51NyLYyfeaw');

        $yt->setMajorProtocolVersion(2);

        $channelToSubscribeTo = 'AssociatedPress';

        // Test for deletion first in case something went wrong
        // last time the test was run (network, etc...)
        $subscriptionFeed = $yt->getSubscriptionFeed($this->ytAccount);
        $successDeletionUpFront = true;
        $message = null;
        foreach($subscriptionFeed as $subscriptionEntry) {
            $subscriptionType = null;
            $categories = $subscriptionEntry->getCategory();
            // examine the correct category element since there are multiple
            foreach($categories as $category) {
                if ($category->getScheme() ==
                    'http://gdata.youtube.com/schemas/2007/' .
                    'subscriptiontypes.cat') {
                        $subscriptionType = $category->getTerm();
                    }
            }
            if ($subscriptionType == 'channel') {
                if ($subscriptionEntry->getUsername()->text ==
                    $channelToSubscribeTo) {
                    try {
                        $subscriptionEntry->delete();
                    } catch (App\Exception $e) {
                        $message = $e->getMessage();
                        $successDeletionUpFront = false;
                    }
                }
            }
        }
        $this->assertTrue($successDeletionUpFront, 'Found existing ' .
            'subscription in unit test, could not delete prior to running ' .
            'test -- ' . $message);

        // Channel
        $newSubscription = $yt->newSubscriptionEntry();
        $newSubscription->category = array(
            $yt->newCategory('channel',
            $this->subscriptionTypeSchema));
        $newSubscription->setUsername($yt->newUsername(
            $channelToSubscribeTo));

        $postUrl =
            'https://gdata.youtube.com/feeds/api/users/default/subscriptions';

        $successPosting = true;
        $message = null;
        $insertedSubscription = null;
        try {
            $insertedSubscription = $yt->insertEntry(
                $newSubscription, $postUrl,
                '\Zend\GData\YouTube\SubscriptionEntry');
        } catch (App\Exception $e) {
            $message = $e->getMessage();
            $successPosting = false;
        }

        $this->assertTrue($successPosting, $message);

        // Delete it
        $successDeletion = true;
        $message = null;
        try {
            $insertedSubscription->delete();
        } catch (App\Exception $e) {
            $message = $e->getMessage();
            $successDeletion = false;
        }

        $this->assertTrue($successDeletion, $message);
    }

    public function testAddAndDeleteSubscriptionToFavoritesV2()
    {
        $service = YouTube::AUTH_SERVICE_NAME;
        $authenticationURL =
            'https://www.google.com/youtube/accounts/ClientLogin';
        $httpClient = GData\ClientLogin::getHttpClient(
            $this->user,
            $this->pass,
            $service,
            null, // client
            'Google-UnitTests-1.0', // source
            null, // $loginToken
            null, // loginCaptcha
            $authenticationURL);

        $yt = new YouTube(
            $httpClient, 'Google-UnitTests-1.0',
            'ytapi-gdataops-12345-u78960r7-0',
            'AI39si6c-ZMGFZ5fkDAEJoCNHP9LOM2LSO1XuycZF7E' .
            'yu1IuvkioESqzRcf3voDLymIUGIrxdMx2aTufdbf5D7E51NyLYyfeaw');

        $yt->setMajorProtocolVersion(2);

        $usernameOfFavoritesToSubscribeTo = 'CBS';

        // Test for deletion first in case something went wrong
        // last time the test was run (network, etc...)
        $subscriptionFeed = $yt->getSubscriptionFeed($this->ytAccount);
        $successDeletionUpFront = true;
        $message = null;
        foreach($subscriptionFeed as $subscriptionEntry) {
            $subscriptionType = null;
            $categories = $subscriptionEntry->getCategory();
            // examine the correct category element since there are multiple
            foreach($categories as $category) {
                if ($category->getScheme() ==
                    'http://gdata.youtube.com/schemas/2007/' .
                    'subscriptiontypes.cat') {
                        $subscriptionType = $category->getTerm();
                    }
            }
            if ($subscriptionType == 'favorites') {
                if ($subscriptionEntry->getUsername()->text ==
                    $usernameOfFavoritesToSubscribeTo) {
                    try {
                        $subscriptionEntry->delete();
                    } catch (App\Exception $e) {
                        $message = $e->getMessage();
                        $successDeletionUpFront = false;
                    }
                }
            }
        }
        $this->assertTrue($successDeletionUpFront, 'Found existing ' .
            'subscription in unit test, could not delete prior to running ' .
            'test -- ' . $message);

        // CBS's favorites
        $newSubscription = $yt->newSubscriptionEntry();
        $newSubscription->category = array(
            $yt->newCategory('favorites',
            $this->subscriptionTypeSchema));
        $newSubscription->setUsername($yt->newUsername(
            $usernameOfFavoritesToSubscribeTo));

        $postUrl =
            'https://gdata.youtube.com/feeds/api/users/default/subscriptions';

        $successPosting = true;
        $message = null;
        $insertedSubscription = null;
        try {
            $insertedSubscription = $yt->insertEntry(
                $newSubscription, $postUrl,
                '\Zend\GData\YouTube\SubscriptionEntry');
        } catch (App\Exception $e) {
            $message = $e->getMessage();
            $successPosting = false;
        }

        $this->assertTrue($successPosting, $message);

        // Delete it
        $successDeletion = true;
        $message = null;
        try {
            $insertedSubscription->delete();
        } catch (App\Exception $e) {
            $message = $e->getMessage();
            $successDeletion = false;
        }

        $this->assertTrue($successDeletion, $message);
    }

    public function testAddAndDeleteSubscriptionToPlaylistV2()
    {
        $service = YouTube::AUTH_SERVICE_NAME;
        $authenticationURL =
            'https://www.google.com/youtube/accounts/ClientLogin';
        $httpClient = GData\ClientLogin::getHttpClient(
            $this->user,
            $this->pass,
            $service,
            null, // client
            'Google-UnitTests-1.0', // source
            null, // $loginToken
            null, // loginCaptcha
            $authenticationURL);

        $yt = new YouTube(
            $httpClient, 'Google-UnitTests-1.0',
            'ytapi-gdataops-12345-u78960r7-0',
            'AI39si6c-ZMGFZ5fkDAEJoCNHP9LOM2LSO1XuycZF7E' .
            'yu1IuvkioESqzRcf3voDLymIUGIrxdMx2aTufdbf5D7E51NyLYyfeaw');

        $yt->setMajorProtocolVersion(2);
        $playlistIdToSubscribeTo = '7A2BB4AFFEBED2A4';

        // Test for deletion first in case something went wrong
        // last time the test was run (network, etc...)
        $subscriptionFeed = $yt->getSubscriptionFeed($this->ytAccount);
        $successDeletionUpFront = true;
        $message = null;
        foreach($subscriptionFeed as $subscriptionEntry) {
            $subscriptionType = null;
            $categories = $subscriptionEntry->getCategory();
            // examine the correct category element since there are multiple
            foreach($categories as $category) {
                if ($category->getScheme() ==
                    'http://gdata.youtube.com/schemas/2007/' .
                    'subscriptiontypes.cat') {
                        $subscriptionType = $category->getTerm();
                    }
            }
            if ($subscriptionType == 'playlist') {
                if ($subscriptionEntry->getPlaylistId()->text ==
                    $playlistIdToSubscribeTo) {
                    try {
                        $subscriptionEntry->delete();
                    } catch (App\Exception $e) {
                        $message = $e->getMessage();
                        $successDeletionUpFront = false;
                    }
                }
            }
        }
        $this->assertTrue($successDeletionUpFront, 'Found existing ' .
            'subscription in unit test, could not delete prior to running ' .
            'test -- ' . $message);

        // Playlist of McGyver videos
        $newSubscription = $yt->newSubscriptionEntry();
        $newSubscription->setMajorProtocolVersion(2);
        $newSubscription->category = array(
            $yt->newCategory('playlist',
            $this->subscriptionTypeSchema));
        $newSubscription->setPlaylistId($yt->newPlaylistId(
            $playlistIdToSubscribeTo));

        $postUrl =
            'https://gdata.youtube.com/feeds/api/users/default/subscriptions';

        $successPosting = true;
        $message = null;
        $insertedSubscription = null;
        try {
            $insertedSubscription = $yt->insertEntry(
                $newSubscription, $postUrl,
                '\Zend\GData\YouTube\SubscriptionEntry');
        } catch (App\Exception $e) {
            $message = $e->getMessage();
            $successPosting = false;
        }

        $this->assertTrue($successPosting, $message);

        // Delete it
        $successDeletion = true;
        $message = null;
        try {
            $insertedSubscription->delete();
        } catch (App\Exception $e) {
            $message = $e->getMessage();
            $successDeletion = false;
        }

        $this->assertTrue($successDeletion, $message);
    }

    public function testAddAndDeleteSubscriptionToQueryV2()
    {
        $developerKey = constant('TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant('TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');

        $service = YouTube::AUTH_SERVICE_NAME;
        $authenticationURL =
            'https://www.google.com/youtube/accounts/ClientLogin';
        $httpClient = GData\ClientLogin::getHttpClient(
            $this->user,
            $this->pass,
            $service,
            null, // client
            'Google-UnitTests-1.0', // source
            null, // $loginToken
            null, // loginCaptcha
            $authenticationURL
        );

        $yt = new YouTube($httpClient, 'Google-UnitTests-1.0', $clientId, $developerKey);

        $yt->setMajorProtocolVersion(2);
        $queryStringToSubscribeTo = 'zend';

        // Test for deletion first in case something went wrong
        // last time the test was run (network, etc...)
        $subscriptionFeed = $yt->getSubscriptionFeed($this->ytAccount);
        $successDeletionUpFront = true;
        $message = null;
        foreach($subscriptionFeed as $subscriptionEntry) {
            $subscriptionType = null;
            $categories = $subscriptionEntry->getCategory();
            // examine the correct category element since there are multiple
            foreach($categories as $category) {
                if ($category->getScheme() ==
                    'http://gdata.youtube.com/schemas/2007/' .
                    'subscriptiontypes.cat') {
                        $subscriptionType = $category->getTerm();
                    }
            }
            if ($subscriptionType == 'query') {
                if ($subscriptionEntry->getQueryString() ==
                    $queryStringToSubscribeTo) {
                    try {
                        $subscriptionEntry->delete();
                    } catch (App\Exception $e) {
                        $message = $e->getMessage();
                        $successDeletionUpFront = false;
                    }
                }
            }
        }
        $this->assertTrue($successDeletionUpFront, 'Found existing ' .
            'subscription in unit test, could not delete prior to running ' .
            'test -- ' . $message);

        // Query
        $newSubscription = $yt->newSubscriptionEntry();
        $newSubscription->category = array(
            $yt->newCategory('query',
            $this->subscriptionTypeSchema));
        $newSubscription->setQueryString($yt->newQueryString(
            $queryStringToSubscribeTo));

        $postUrl =
            'https://gdata.youtube.com/feeds/api/users/default/subscriptions';

        $successPosting = true;
        $message = null;
        $insertedSubscription = null;
        try {
            $insertedSubscription = $yt->insertEntry(
                $newSubscription, $postUrl,
                '\Zend\GData\YouTube\SubscriptionEntry');
        } catch (App\Exception $e) {
            $message = $e->getMessage();
            $successPosting = false;
        }

        $this->assertTrue($successPosting, $message);

        // Delete it
        $successDeletion = true;
        $message = null;
        try {
            $insertedSubscription->delete();
        } catch (App\Exception $e) {
            $message = $e->getMessage();
            $successDeletion = false;
        }

        $this->assertTrue($successDeletion, $message);
    }

    public function generateRandomString($length)
    {
        $outputString = null;
        for($i = 0; $i < $length; $i++) {
            $outputString .= chr(rand(65,90));
        }
        return $outputString;
    }

    public function testRetrieveActivityFeed()
    {
        $developerKey = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $client = GData\ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $youtube = new YouTube($client, 'ZF_UnitTest',
            $clientId, $developerKey);
        $youtube->setMajorProtocolVersion(2);

        $feed = $youtube->getActivityForUser($this->ytAccount);
        $this->assertTrue($feed instanceof YouTube\ActivityFeed);
        $this->assertTrue((count($feed->entries) > 0));
        $this->assertEquals('Activity of ' . $this->ytAccount,
            $feed->title->text);
    }

    public function testExceptionIfNotUsingDeveloperKey()
    {
        $exceptionThrown = false;
        $youtube = new YouTube();
        $youtube->setMajorProtocolVersion(2);
        try {
            $youtube->getActivityForUser($this->ytAccount);
        } catch (App\HttpException $e) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown, 'Was expecting an exception when ' .
            'making a request to the YouTube Activity feed without a ' .
            'developer key.');
    }

    public function testRetrieveActivityFeedForMultipleUsers()
    {
        $developerKey = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $client = GData\ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $youtube = new YouTube($client, 'ZF_UnitTest',
            $clientId, $developerKey);
        $youtube->setMajorProtocolVersion(2);

        $feed = $youtube->getActivityForUser($this->ytAccount .
            ',associatedpress');
        $this->assertTrue($feed instanceof YouTube\ActivityFeed);
        $this->assertTrue((count($feed->entries) > 0));
        $this->assertEquals('Activity of ' . $this->ytAccount .
            ',associatedpress', $feed->title->text);
    }

    public function testRetrieveFriendFeed()
    {
        $developerKey = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $client = GData\ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $youtube = new YouTube($client, 'ZF_UnitTest',
            $clientId, $developerKey);
        $youtube->setMajorProtocolVersion(2);

        $feed = $youtube->getFriendActivityForCurrentUser();
        $this->assertTrue($feed instanceof YouTube\ActivityFeed);
        $this->assertTrue((count($feed->entries) > 0));
        $this->assertEquals('Activity of the friends of ' . $this->ytAccount,
            $feed->title->text);
    }

   public function testThrowExceptionOnActivityFeedRequestForMoreThan20Users()
   {
        $exceptionThrown = false;
        $listOfMoreThan20Users = null;
        $youtube = new YouTube();
        $youtube->setMajorProtocolVersion(2);

        for ($x = 0;  $x < 30; $x++) {
            $listOfMoreThan20Users .= "user$x";
            if ($x != 29) {
                $listOfMoreThan20Users .= ",";
            }
        }

        try {
            $youtube->getActivityForUser($listOfMoreThan20Users);
        } catch (App\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown, 'Was expecting an exception on ' .
            'a request to ->getActivityForUser when more than 20 users were ' .
            'specified in YouTube.php');
    }

    public function testGetInboxFeedForCurrentUserV1()
    {
        $developerKey = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $client = GData\ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $youtube = new YouTube($client, 'ZF_UnitTest',
            $clientId, $developerKey);

        $inboxFeed = $youtube->getInboxFeedForCurrentUser();
        $this->assertTrue($inboxFeed instanceof YouTube\InboxFeed);
        $this->assertTrue(count($inboxFeed->entries) > 0, 'Test account ' .
            $this->ytAccount . ' had no messages in their inbox.');

        // get the first entry
        $inboxFeed->rewind();
        $inboxEntry = $inboxFeed->current();
        $this->assertTrue(
            $inboxEntry instanceof YouTube\InboxEntry);
        $this->assertTrue($inboxEntry->getTitle()->text != '');
    }

    public function testGetInboxFeedForCurrentUserV2()
    {
        $developerKey = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $client = GData\ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $youtube = new YouTube($client, 'ZF_UnitTest',
            $clientId, $developerKey);
        $youtube->setMajorProtocolVersion(2);

        $inboxFeed = $youtube->getInboxFeedForCurrentUser();
        $this->assertTrue($inboxFeed instanceof YouTube\InboxFeed);
        $this->assertTrue(count($inboxFeed->entries) > 0, 'Test account ' .
            $this->ytAccount . ' had no messages in their inbox.');

        // get the first entry
        $inboxFeed->rewind();
        $inboxEntry = $inboxFeed->current();
        $this->assertTrue(
            $inboxEntry instanceof YouTube\InboxEntry);
        $this->assertTrue($inboxEntry->getTitle()->text != '');
    }


    public function testSendAMessageV2()
    {
        $developerKey = constant('TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant('TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $client = GData\ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $youtube = new YouTube($client, 'ZF_UnitTest',
            $clientId, $developerKey);
        $youtube->setMajorProtocolVersion(2);

        // get a video from the recently featured video feed
        $videoFeed = $youtube->getRecentlyFeaturedVideoFeed();
        $videoEntry = $videoFeed->entry[0];
        $this->assertTrue($videoEntry instanceof YouTube\VideoEntry);

        // sending message to gdpython (python client library unit test user)
        $sentMessage = $youtube->sendVideoMessage(
            'Sending a v2 test message from Zend_GData_YouTubeOnlineTest.',
            $videoEntry, null, 'gdpython');

        $this->assertTrue(
            $sentMessage instanceof YouTube\InboxEntry);
    }

    public function testSendAMessageV1()
    {
        $developerKey = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $client = GData\ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $youtube = new YouTube($client, 'ZF_UnitTest',
            $clientId, $developerKey);
        $youtube->setMajorProtocolVersion(1);

        // get a video from the recently featured video feed
        $videoFeed = $youtube->getRecentlyFeaturedVideoFeed();
        $videoEntry = $videoFeed->entry[0];
        $this->assertTrue($videoEntry instanceof YouTube\VideoEntry);

        // sending message to gdpython (python client library unit test user)
        $sentMessage = $youtube->sendVideoMessage(
            'Sending a v1 test message from Zend_GData_YouTubeOnlineTest.',
            $videoEntry, null, 'gdpython');
        $this->assertTrue(
            $sentMessage instanceof YouTube\InboxEntry);
    }

    public function testThrowExceptionOnSendingMessageWithoutVideo()
    {
        $exceptionCaught = false;
        $this->gdata = new YouTube();
        try {
            $this->gdata->sendVideoMessage('Should fail', null, null, 'foo');
        } catch (App\InvalidArgumentException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Was expecting an exception if ' .
            'sending a message without a video');
    }

    public function testCommentOnAComment()
    {
        $developerKey = constant('TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $clientId = constant('TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $client = GData\ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');
        $youtube = new YouTube($client, 'ZF_UnitTest',
            $clientId, $developerKey);
        $youtube->setMajorProtocolVersion(2);

        $mostDiscussedFeed = $youtube->getVideoFeed(
            'https://gdata.youtube.com/feeds/api/standardfeeds/most_discussed');

        // get first entry
        $mostDiscussedFeed->rewind();
        $firstEntry = $mostDiscussedFeed->current();

        $this->assertTrue($firstEntry instanceof YouTube\VideoEntry);

        $commentFeed = $youtube->getVideoCommentFeed($firstEntry->getVideoId());

        // get first comment
        $commentFeed->rewind();
        $firstCommentEntry = $commentFeed->current();

        $commentedComment = $youtube->replyToCommentEntry($firstCommentEntry,
            'awesome ! (ZFUnitTest-test)');
        $this->assertTrue(
            $commentedComment instanceof YouTube\CommentEntry);
    }

}
