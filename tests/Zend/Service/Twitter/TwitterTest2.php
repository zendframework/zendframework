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
 * @package    Zend_Service_Twitter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: TwitterTest.php 22318 2010-05-29 18:24:27Z padraic $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Twitter_TwitterTest2::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Service_Twitter */
require_once 'Zend/Service/Twitter.php';

/** Zend_Http_Client */
require_once 'Zend/Http/Client.php';

/** Zend_Http_Client_Adapter_Test */
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @category   Zend
 * @package    Zend_Service_Twitter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Twitter
 */
class Zend_Service_Twitter_TwitterTest2 extends PHPUnit_Framework_TestCase
{

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    public function teardown()
    {
        Zend_Service_Abstract::setHttpClient(new Zend_Http_Client);
    }
    
    /**
     * @group ZF-8218
     */
    public function testUserNameNotRequired()
    {    
        $twitter = new Zend_Service_Twitter();
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'users/show/twitter.xml', Zend_Http_Client::GET, 'users.show.twitter.xml'
        ));
        $exists = $twitter->user->show('twitter')->id() !== null;
        $this->assertTrue($exists);
    }

    /**
     * @group ZF-7781
     */
    public function testRetrievingStatusesWithValidScreenNameThrowsNoInvalidScreenNameException()
    {
        $twitter = new Zend_Service_Twitter();
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'statuses/user_timeline.xml', Zend_Http_Client::GET, 'user_timeline.twitter.xml'
        ));
        $twitter->status->userTimeline(array('screen_name' => 'twitter'));
    }

    /**
     * @group ZF-7781
     * @expectedException Zend_Service_Twitter_Exception
     */
    public function testRetrievingStatusesWithInvalidScreenNameCharacterThrowsInvalidScreenNameException()
    {
        $twitter = new Zend_Service_Twitter();
        $twitter->status->userTimeline(array('screen_name' => 'abc.def'));
    }

    /**
     * @group ZF-7781
     */
    public function testRetrievingStatusesWithInvalidScreenNameLengthThrowsInvalidScreenNameException()
    {
        $this->setExpectedException('Zend_Service_Twitter_Exception');
        $twitter = new Zend_Service_Twitter();
        $twitter->status->userTimeline(array('screen_name' => 'abcdef_abc123_abc123x'));
    }

    /**
     * @group ZF-7781
     */
    public function testStatusUserTimelineConstructsExpectedGetUriAndOmitsInvalidParams()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'statuses/user_timeline/783214.xml', Zend_Http_Client::GET, 'user_timeline.twitter.xml', array(
                'page' => '1',
                'count' => '123',
                'user_id' => '783214',
                'since_id' => '10000',
                'max_id' => '20000',
                'screen_name' => 'twitter'
            )
        ));
        $twitter->status->userTimeline(array(
            'id' => '783214',
            'since' => '+2 days', /* invalid param since Apr 2009 */
            'page' => '1',
            'count' => '123',
            'user_id' => '783214',
            'since_id' => '10000',
            'max_id' => '20000',
            'screen_name' => 'twitter'
        ));
    }

    public function testOverloadingGetShouldReturnObjectInstanceWithValidMethodType()
    {
        $twitter = new Zend_Service_Twitter;
        $return = $twitter->status;
        $this->assertSame($twitter, $return);
    }

    /**
     * @expectedException Zend_Service_Twitter_Exception
     */
    public function testOverloadingGetShouldthrowExceptionWithInvalidMethodType()
    {
        $twitter = new Zend_Service_Twitter;
        $return = $twitter->foo;
    }

    /**
     * @expectedException Zend_Service_Twitter_Exception
     */
    public function testOverloadingGetShouldthrowExceptionWithInvalidFunction()
    {
        $twitter = new Zend_Service_Twitter;
        $return = $twitter->foo();
    }

    public function testMethodProxyingDoesNotThrowExceptionsWithValidMethods()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'statuses/public_timeline.xml', Zend_Http_Client::GET, 'public_timeline.xml'
        ));
        $twitter->status->publicTimeline();
    }

    /**
     * @expectedException Zend_Service_Twitter_Exception
     */
    public function testMethodProxyingThrowExceptionsWithInvalidMethods()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->status->foo();
    }
    
    public function testVerifiedCredentials()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'account/verify_credentials.xml', Zend_Http_Client::GET, 'account.verify_credentials.xml'
        ));
        $response = $twitter->account->verifyCredentials();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
    }

    public function testPublicTimelineStatusReturnsResults()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'statuses/public_timeline.xml', Zend_Http_Client::GET, 'public_timeline.xml'
        ));
        $response = $twitter->status->publicTimeline();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
    }

    public function testRateLimitStatusReturnsResults()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'account/rate_limit_status.xml', Zend_Http_Client::GET, 'rate_limit_status.xml'
        ));
        $response = $twitter->account->rateLimitStatus();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
    }

    public function testRateLimitStatusHasHitsLeft()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'account/rate_limit_status.xml', Zend_Http_Client::GET, 'rate_limit_status.xml'
        ));
        $response = $twitter->account->rateLimitStatus();
        $remaining_hits = $response->toValue($response->{'remaining-hits'});
        $this->assertEquals(150, $remaining_hits);
    }
    
    public function testAccountEndSession()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'account/end_session', Zend_Http_Client::GET
        ));
        $response = $twitter->account->endSession();
        $this->assertTrue($response);
    }

    /**
     * TODO: Check actual purpose. New friend returns XML response, existing
     * friend returns a 403 code.
     */
    public function testFriendshipCreate()
    {
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'friendships/create/twitter.xml', Zend_Http_Client::POST, 'friendships.create.twitter.xml'
        ));
        $response = $twitter->friendship->create('twitter');
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
    }

    /**
     * TODO: Mismatched behaviour from API. Per the API this can assert the
     * existence of a friendship between any two users (not just the current
     * user). We should expand the method or add a better fit method for
     * general use.
     */
    public function testFriendshipExists()
    {
        $twitter = new Zend_Service_Twitter('padraicb');
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'friendships/exists.xml', Zend_Http_Client::GET, 'friendships.exists.twitter.xml',
            array('user_a'=>'padraicb', 'user_b'=>'twitter')
        ));
        $response = $twitter->friendship->exists('twitter');
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
    }

    public function testFriendsTimelineWithPageReturnsResults()
    {   
        $twitter = new Zend_Service_Twitter;
        $twitter->setLocalHttpClient($this->_stubTwitter(
            'statuses/friends_timeline.xml', Zend_Http_Client::GET, 'statuses.friends_timeline.page.xml',
            array('page'=>3)
        ));
        $response = $twitter->status->friendsTimeline(array('page' => 3));
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
    }

    /**
     * @return void
     */
    public function testFriendsTimelineWithCountReturnsResults()
    {$this->markTestIncomplete();
        /* @var $response Zend_Rest_Client_Result */
        $response = $this->twitter->status->friendsTimeline(array('id' => 'zftestuser1', 'count' => '2'));
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());

        $this->assertTrue(isset($response->status));
        $this->assertEquals(2, count($response->status), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
    }

    /**
     * @return void
     */
    public function testUserTimelineStatusWithPageAndTwoTweetsReturnsResults()
    {$this->markTestIncomplete();
        /* @var $response Zend_Rest_Client_Result */
        $response = $this->twitter->status->userTimeline(array('id' => 'zftestuser1', 'count' => 2));
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $raw_response = $httpResponse->getHeadersAsString() . $httpResponse->getBody();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());

        $this->assertTrue(isset($response->status));
        $this->assertEquals(2, count($response->status), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
    }

    public function testUserTimelineStatusShouldReturnFortyResults()
    {$this->markTestIncomplete();
        /* @var $response Zend_Rest_Client_Result */
        $response = $this->twitter->status->userTimeline(array('id' => 'zftestuser1', 'count' => 40));
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());

        $this->assertTrue(isset($response->status));
        $this->assertEquals(40, count($response->status));
    }

    /**
     * @return void
     */
    public function testPostStatusUpdateReturnsResponse()
    {$this->markTestIncomplete();
        /* @var $response Zend_Rest_Client_Result */
        $response = $this->twitter->status->update('Test Message - ' . rand());
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status));
    }

    /**
     * $return void
     */
    public function testPostStatusUpdateToLongShouldThrowException()
    {$this->markTestIncomplete();
        try {
            $response = $this->twitter->status->update('Test Message - ' . str_repeat(' Hello ', 140));
            $this->fail('Trying to post a status with > 140 character should throw exception');
        } catch (Exception $e) {
        }
    }

    public function testPostStatusUpdateUTF8ShouldNotThrowException()
    {$this->markTestIncomplete();
        try {
            $response = $this->twitter->status->update(str_repeat('M�r', 46) . 'M�');
        } catch (Exception $e) {
            $this->fail('Trying to post a utf8 string of 140 chars should not throw exception');
        }
    }

    /**
     * $return void
     */
    public function testPostStatusUpdateEmptyShouldThrowException()
    {$this->markTestIncomplete();
        try {
            $response = $this->twitter->status->update('');
            $this->fail('Trying to post an empty status should throw exception');
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testShowStatusReturnsResponse()
    {$this->markTestIncomplete();
        $response = $this->twitter->status->publicTimeline();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $status_id = $response->toValue($response->status->id);
        $this->assertType('numeric', $status_id);

        $response2 = $this->twitter->status->show($status_id);
        $this->assertTrue($response2 instanceof Zend_Rest_Client_Result);

        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status));

    }

    /**
     * @return void
     */
    public function testCreateFavoriteStatusReturnsResponse()
    {$this->markTestIncomplete();
        /* @var $response Zend_Rest_Client_Result */
        $response = $this->twitter->status->userTimeline();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $update_id = $response->toValue($response->status->id);
        $this->assertType('numeric', $update_id);

        $response2 = $this->twitter->favorite->create($update_id);
        $this->assertTrue($response2 instanceof Zend_Rest_Client_Result);

        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status));

    }

    /**
     * @return void
     */
    public function testFavoriteFavoriesReturnsResponse()
    {$this->markTestIncomplete();
        $response = $this->twitter->favorite->favorites();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status));
    }

    public function testDestroyFavoriteReturnsResponse()
    {$this->markTestIncomplete();
        $response = $this->twitter->favorite->favorites();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $update_id = $response->toValue($response->status->id);
        $this->assertType('numeric', $update_id);

        $response2 = $this->twitter->favorite->destroy($update_id);
        $this->assertTrue($response2 instanceof Zend_Rest_Client_Result);

        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status));
    }

    public function testStatusDestroyReturnsResult()
    {$this->markTestIncomplete();
        /* @var $response Zend_Rest_Client_Result */
        $response = $this->twitter->status->userTimeline();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $update_id = $response->toValue($response->status->id);
        $this->assertType('numeric', $update_id);

        $response2 = $this->twitter->status->destroy($update_id);
        $this->assertTrue($response2 instanceof Zend_Rest_Client_Result);

        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status));
    }

    public function testUserFriendsReturnsResults()
    {$this->markTestIncomplete();
        $response = $this->twitter->user->friends();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status));
    }

    public function testUserFolloersReturnsResults()
    {$this->markTestIncomplete();
        $response = $this->twitter->user->followers(array('id' => 'zftestuser1'));
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status));
    }

    public function testUserFriendsSpecificUserReturnsResults()
    {$this->markTestIncomplete();
        $response = $this->twitter->user->friends(array('id' => 'ZendRssFeed'));
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();

        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
        $this->assertTrue(isset($response->status), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());

        return $response;
    }

    public function testUserShowByIdReturnsResults()
    {$this->markTestIncomplete();
        $userInfo = $this->testUserFriendsSpecificUserReturnsResults();
        $userId = $userInfo->toValue($userInfo->user->id);

        $response = $this->twitter->user->show($userId);
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $this->assertEquals($userInfo->toValue($userInfo->user->name), $response->toValue($response->name));
        $this->assertEquals($userId, $response->toValue($response->id));
    }

    public function testUserShowByNameReturnsResults()
    {$this->markTestIncomplete();
        $response = $this->twitter->user->show('zftestuser1');
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $this->assertEquals('zftestuser1', $response->toValue($response->screen_name));
    }

    public function testStatusRepliesReturnsResults()
    {$this->markTestIncomplete();
        $response = $this->twitter->status->replies(array('page' => 1, 'since_id' => 10000, 'invalid_option' => 'doh'));
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
    }

    /**
     * @return void
     */
    public function testFriendshipDestory()
    {$this->markTestIncomplete();
        $response = $this->twitter->friendship->destroy('zftestuser1');
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);

        $httpClient = $this->twitter->getLocalHttpClient();
        $httpRequest = $httpClient->getLastRequest();
        $httpResponse = $httpClient->getLastResponse();
        $this->assertTrue($httpResponse->isSuccessful(), $httpResponse->getStatus() . ': ' . var_export($httpRequest, 1) . '\n' . $httpResponse->getHeadersAsString());
    }

    /**
     * @return void
     */
    public function testBlockingCreate()
    {$this->markTestIncomplete();
        $response = $this->twitter->block->create('zftestuser1');
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $this->assertEquals('zftestuser1', (string) $response->screen_name);
    }

    /**
     * @return void
     */
    public function testBlockingExistsReturnsTrueWhenBlockExists()
    {$this->markTestIncomplete();
        $this->assertTrue($this->twitter->block->exists('zftestuser1'));
    }

    /**
     * @return void
     */
    public function testBlockingBlocked()
    {$this->markTestIncomplete();
        $response = $this->twitter->block->blocking();
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $this->assertEquals('zftestuser1', (string) $response->user->screen_name);
    }

    /**
     * @return void
     */
    public function testBlockingBlockedReturnsIds()
    {$this->markTestIncomplete();
        $response = $this->twitter->block->blocking(1, true);
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $this->assertEquals('16935247', (string) $response->id);
    }

    /**
     * @return void
     */
    public function testBlockingDestroy()
    {$this->markTestIncomplete();
        $response = $this->twitter->block->destroy('zftestuser1');
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
        $this->assertEquals('zftestuser1', (string) $response->screen_name);
    }

    /**
     * @return void
     */
    public function testBlockingExistsReturnsFalseWhenBlockDoesNotExists()
    {$this->markTestIncomplete();
        $this->assertFalse($this->twitter->block->exists('zftestuser1'));
    }

    /**
     * @return void
     */
    public function testBlockingExistsReturnsOjectWhenFlagPassed()
    {$this->markTestIncomplete();
        $response = $this->twitter->block->exists('zftestuser1', true);
        $this->assertTrue($response instanceof Zend_Rest_Client_Result);
    }

    /**
     * Insert Test Data
     *
     */
    protected function insertTestTwitterData()
    {$this->markTestIncomplete();
        $twitter = new Zend_Service_Twitter('zftestuser1', 'zftestuser1');
        // create 10 new entries
        for ($x = 0; $x < 10; $x++) {
            $twitter->status->update('Test Message - ' . $x);
        }
        $twitter->account->endSession();
    }

    /**
     * @group ZF-6284
     */
    public function testTwitterObjectsSoNotShareSameHttpClientToPreventConflictingAuthentication()
    {$this->markTestIncomplete();
        $twitter1 = new Zend_Service_Twitter('zftestuser1', 'zftestuser1');
        $twitter2 = new Zend_Service_Twitter('zftestuser2', 'zftestuser2');
        $this->assertFalse($twitter1->getLocalHttpClient() === $twitter2->getLocalHttpClient());
    }
    
    /**
     * Quick reusable Twitter Service stub setup.
     */
    protected function _stubTwitter($path, $method, $responseFile = null, array $params = null)
    {
        $client = $this->getMock('Zend_Http_Client');
        $client->expects($this->any())->method('resetParameters')
            ->will($this->returnValue($client));
        $client->expects($this->once())->method('setUri')
            ->with('http://api.twitter.com/1/' . $path);
        $response = $this->getMock('Zend_Http_Response', array(), array(), '', false);
        if (!is_null($params)) {
            $setter = 'setParameter' . ucfirst(strtolower($method));
            $client->expects($this->once())->method($setter)->with($params);
        }
        $client->expects($this->once())->method('request')->with($method)
            ->will($this->returnValue($response));
        $response->expects($this->any())->method('getBody')
            ->will($this->returnValue(
                isset($responseFile) ? file_get_contents(dirname(__FILE__) . '/_files/' . $responseFile) : ''
            ));
        return $client;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_TwitterTest2::main') {
    Zend_Service_TwitterTest2::main();
}
