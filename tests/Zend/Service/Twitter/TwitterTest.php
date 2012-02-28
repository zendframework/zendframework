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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Twitter;

use Zend\Service\Twitter,
    Zend\Service,
    Zend\Http,
    Zend\Rest;

/**
 * @category   Zend
 * @package    Zend_Service_Twitter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Twitter
 */
class TwitterTest extends \PHPUnit_Framework_TestCase
{

    public function teardown()
    {
        Service\AbstractService::setDefaultHttpClient(new Http\Client);
    }

    /**
     * Quick reusable Twitter Service stub setup. Its purpose is to fake
     * interactions with Twitter so the component can focus on what matters:
     * 1. Makes correct requests (URI, parameters and HTTP method)
     * 2. Parses all responses and returns a Rest\Client\Result
     * 3. TODO: Correctly utilises all optional parameters
     *
     * If used correctly, tests will be fast, efficient, and focused on
     * Zend_Service_Twitter's behaviour only. No other dependencies need be
     * tested. The Twitter API Changelog should be regularly reviewed to
     * ensure the component is synchronised to the API.
     *
     * @param string $path Path appended to Twitter API endpoint
     * @param string $method Do we expect HTTP GET or POST?
     * @param string $responseFile File containing a valid XML response to the request
     * @param array $params Expected GET/POST parameters for the request
     * @return \Zend\Http\Client
     */
    protected function stubTwitter($path, $method, $responseFile = null, array $params = null)
    {
        $client = $this->getMock('Zend\OAuth\Client', array(), array(), '', false);
        $client->expects($this->any())->method('resetParameters')
            ->will($this->returnValue($client));
        $client->expects($this->once())->method('setUri')
            ->with('http://api.twitter.com/1/' . $path);
        $response = $this->getMock('Zend\Http\Response', array(), array(), '', false);
        if (!is_null($params)) {
            $setter = 'setParameter' . ucfirst(strtolower($method));
            $client->expects($this->once())->method($setter)->with($params);
        }
        $client->expects($this->once())->method('send')->with()
            ->will($this->returnValue($response));
        $response->expects($this->any())->method('getBody')
            ->will($this->returnValue(
                isset($responseFile) ? file_get_contents(__DIR__ . '/_files/' . $responseFile) : ''
            ));
        return $client;
    }

    /**
     * OAuth tests
     */

    public function testProvidingAccessTokenInOptionsSetsHttpClientFromAccessToken()
    {
        $token = $this->getMock('Zend\OAuth\Token\Access', array(), array(), '', false);
        $client = $this->getMock('Zend\OAuth\Client', array(), array(), '', false);
        $token->expects($this->once())->method('getHttpClient')
            ->with(array('accessToken'=>$token, 'opt1'=>'val1', 'siteUrl'=>'http://twitter.com/oauth'))
            ->will($this->returnValue($client));

        $twitter = new Twitter\Twitter(array('accessToken'=>$token, 'opt1'=>'val1'));
        $this->assertTrue($client === $twitter->getLocalHttpClient());
    }

    public function testNotAuthorisedWithoutToken()
    {
        $twitter = new Twitter\Twitter;
        $this->assertFalse($twitter->isAuthorised());
    }

    public function testChecksAuthenticatedStateBasedOnAvailabilityOfAccessTokenBasedClient()
    {
        $token = $this->getMock('Zend\OAuth\Token\Access', array(), array(), '', false);
        $client = $this->getMock('Zend\OAuth\Client', array(), array(), '', false);
        $token->expects($this->once())->method('getHttpClient')
            ->with(array('accessToken'=>$token, 'siteUrl'=>'http://twitter.com/oauth'))
            ->will($this->returnValue($client));

        $twitter = new Twitter\Twitter(array('accessToken'=>$token));
        $this->assertTrue($twitter->isAuthorised());
    }

    public function testRelaysMethodsToInternalOAuthInstance()
    {
        $oauth = $this->getMock('Zend\OAuth\Consumer', array(), array(), '', false);
        $oauth->expects($this->once())->method('getRequestToken')->will($this->returnValue('foo'));
        $oauth->expects($this->once())->method('getRedirectUrl')->will($this->returnValue('foo'));
        $oauth->expects($this->once())->method('redirect')->will($this->returnValue('foo'));
        $oauth->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));
        $oauth->expects($this->once())->method('getToken')->will($this->returnValue('foo'));

        $twitter = new Twitter\Twitter(array('opt1'=>'val1'), $oauth);
        $this->assertEquals('foo', $twitter->getRequestToken());
        $this->assertEquals('foo', $twitter->getRedirectUrl());
        $this->assertEquals('foo', $twitter->redirect());
        $this->assertEquals('foo', $twitter->getAccessToken(array(), $this->getMock('Zend\OAuth\Token\Request')));
        $this->assertEquals('foo', $twitter->getToken());
    }

    public function testResetsHttpClientOnReceiptOfAccessTokenToOauthClient()
    {
        $this->markTestIncomplete('Problem with resolving classes for mocking');
        $oauth = $this->getMock('Zend\OAuth\Consumer', array(), array(), '', false);
        $client = $this->getMock('Zend\OAuth\Client', array(), array(), '', false);
        $token = $this->getMock('Zend\OAuth\Token\Access', array(), array(), '', false);
        $token->expects($this->once())->method('getHttpClient')->will($this->returnValue($client));
        $oauth->expects($this->once())->method('getAccessToken')->will($this->returnValue($token));
        $client->expects($this->once())->method('setHeaders')->with('Accept-Charset', 'ISO-8859-1,utf-8');

        $twitter = new Twitter\Twitter(array(), $oauth);
        $twitter->getAccessToken(array(), $this->getMock('Zend\OAuth\Token\Request'));
        $this->assertTrue($client === $twitter->getLocalHttpClient());
    }

    public function testAuthorisationFailureWithUsernameAndNoAccessToken()
    {
        $this->setExpectedException('Zend\Service\Twitter\Exception');
        $twitter = new Twitter\Twitter(array('username'=>'me'));
        $twitter->statusPublicTimeline();
    }

    /**
     * @group ZF-8218
     */
    public function testUserNameNotRequired()
    {
        $twitter = new Twitter\Twitter();
        $twitter->setLocalHttpClient($this->stubTwitter(
            'users/show.xml', Http\Request::METHOD_GET, 'users.show.twitter.xml',
            array('id'=>'twitter')
        ));
        $exists = $twitter->user->show('twitter')->id() !== null;
        $this->assertTrue($exists);
    }

    /**
     * @group ZF-7781
     */
    public function testRetrievingStatusesWithValidScreenNameThrowsNoInvalidScreenNameException()
    {
        $twitter = new Twitter\Twitter();
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/user_timeline.xml', Http\Request::METHOD_GET, 'user_timeline.twitter.xml'
        ));
        $twitter->status->userTimeline(array('screen_name' => 'twitter'));
    }

    /**
     * @group ZF-7781
     */
    public function testRetrievingStatusesWithInvalidScreenNameCharacterThrowsInvalidScreenNameException()
    {
        $this->setExpectedException('Zend\Service\Twitter\Exception');
        $twitter = new Twitter\Twitter();
        $twitter->status->userTimeline(array('screen_name' => 'abc.def'));
    }

    /**
     * @group ZF-7781
     */
    public function testRetrievingStatusesWithInvalidScreenNameLengthThrowsInvalidScreenNameException()
    {
        $this->setExpectedException('\Zend\Service\Twitter\Exception');
        $twitter = new Twitter\Twitter();
        $twitter->status->userTimeline(array('screen_name' => 'abcdef_abc123_abc123x'));
    }

    /**
     * @group ZF-7781
     */
    public function testStatusUserTimelineConstructsExpectedGetUriAndOmitsInvalidParams()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/user_timeline/783214.xml', Http\Request::METHOD_GET, 'user_timeline.twitter.xml', array(
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
        $twitter = new Twitter\Twitter;
        $return = $twitter->status;
        $this->assertSame($twitter, $return);
    }

    public function testOverloadingGetShouldthrowExceptionWithInvalidMethodType()
    {
        $this->setExpectedException('Zend\Service\Twitter\Exception');
        $twitter = new Twitter\Twitter;
        $return = $twitter->foo;
    }

    public function testOverloadingGetShouldthrowExceptionWithInvalidFunction()
    {
        $this->setExpectedException('Zend\Service\Twitter\Exception');
        $twitter = new Twitter\Twitter;
        $return = $twitter->foo();
    }

    public function testMethodProxyingDoesNotThrowExceptionsWithValidMethods()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/public_timeline.xml', Http\Request::METHOD_GET, 'public_timeline.xml'
        ));
        $twitter->status->publicTimeline();
    }

    public function testMethodProxyingThrowExceptionsWithInvalidMethods()
    {
        $this->setExpectedException('Zend\Service\Twitter\Exception');
        $twitter = new Twitter\Twitter;
        $twitter->status->foo();
    }

    public function testVerifiedCredentials()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'account/verify_credentials.xml', Http\Request::METHOD_GET, 'account.verify_credentials.xml'
        ));
        $response = $twitter->account->verifyCredentials();
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testPublicTimelineStatusReturnsResults()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/public_timeline.xml', Http\Request::METHOD_GET, 'public_timeline.xml'
        ));
        $response = $twitter->status->publicTimeline();
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testRateLimitStatusReturnsResults()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'account/rate_limit_status.xml', Http\Request::METHOD_GET, 'rate_limit_status.xml'
        ));
        $response = $twitter->account->rateLimitStatus();
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testRateLimitStatusHasHitsLeft()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'account/rate_limit_status.xml', Http\Request::METHOD_GET, 'rate_limit_status.xml'
        ));
        $response = $twitter->account->rateLimitStatus();
        $remaining_hits = $response->toValue($response->{'remaining-hits'});
        $this->assertEquals(150, $remaining_hits);
    }

    public function testAccountEndSession()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'account/end_session', Http\Request::METHOD_GET
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
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'friendships/create/twitter.xml', Http\Request::METHOD_POST, 'friendships.create.twitter.xml'
        ));
        $response = $twitter->friendship->create('twitter');
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Mismatched behaviour from API. Per the API this can assert the
     * existence of a friendship between any two users (not just the current
     * user). We should expand the method or add a better fit method for
     * general use.
     */
    public function testFriendshipExists()
    {
        $twitter = new Twitter\Twitter(array('username'=>'padraicb'));
        $twitter->setLocalHttpClient($this->stubTwitter(
            'friendships/exists.xml', Http\Request::METHOD_GET, 'friendships.exists.twitter.xml',
            array('user_a'=>'padraicb', 'user_b'=>'twitter')
        ));
        $response = $twitter->friendship->exists('twitter');
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Add verification for ALL optional parameters
     */
    public function testFriendsTimelineWithPageReturnsResults()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/friends_timeline.xml', Http\Request::METHOD_GET, 'statuses.friends_timeline.page.xml',
            array('page'=>3)
        ));
        $response = $twitter->status->friendsTimeline(array('page' => 3));
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Add verification for ALL optional parameters
     */
    public function testUserTimelineReturnsResults()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/user_timeline/twitter.xml', Http\Request::METHOD_GET, 'user_timeline.twitter.xml'
        ));
        $response = $twitter->status->userTimeline(array('id' => 'twitter'));
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Add verification for ALL optional parameters
     */
    public function testPostStatusUpdateReturnsResponse()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/update.xml', Http\Request::METHOD_POST, 'statuses.update.xml',
            array('status'=>'Test Message 1')
        ));
        $response = $twitter->status->update('Test Message 1');
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testPostStatusUpdateToLongShouldThrowException()
    {
        $this->setExpectedException('Zend\Service\Twitter\Exception');
        $twitter = new Twitter\Twitter;
        $twitter->status->update('Test Message - ' . str_repeat(' Hello ', 140));
    }

    public function testPostStatusUpdateEmptyShouldThrowException()
    {
        $this->setExpectedException('Zend\Service\Twitter\Exception');
        $twitter = new Twitter\Twitter;
        $twitter->status->update('');
    }

    public function testShowStatusReturnsResponse()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/show/15042159587.xml', Http\Request::METHOD_GET, 'statuses.show.xml'
        ));
        $response = $twitter->status->show(15042159587);
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testCreateFavoriteStatusReturnsResponse()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'favorites/create/15042159587.xml', Http\Request::METHOD_POST, 'favorites.create.xml'
        ));
        $response = $twitter->favorite->create(15042159587);
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testFavoriteFavoriesReturnsResponse()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'favorites.xml', Http\Request::METHOD_GET, 'favorites.xml'
        ));
        $response = $twitter->favorite->favorites();
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Can we use a HTTP DELETE?
     */
    public function testDestroyFavoriteReturnsResponse()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'favorites/destroy/15042159587.xml', Http\Request::METHOD_POST, 'favorites.destroy.xml'
        ));
        $response = $twitter->favorite->destroy(15042159587);
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testStatusDestroyReturnsResult()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/destroy/15042159587.xml', Http\Request::METHOD_POST, 'statuses.destroy.xml'
        ));
        $response = $twitter->status->destroy(15042159587);
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Add verification for ALL optional parameters
     */
    public function testUserFriendsReturnsResults()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/friends.xml', Http\Request::METHOD_GET, 'statuses.friends.xml'
        ));
        $response = $twitter->user->friends();
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Add verification for ALL optional parameters
     * Note: Implementation does not currently accept ANY optional parameters
     */
    public function testUserFollowersReturnsResults()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/followers.xml', Http\Request::METHOD_GET, 'statuses.followers.xml'
        ));
        $response = $twitter->user->followers();
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testUserShowByIdReturnsResults()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'users/show.xml', Http\Request::METHOD_GET, 'users.show.twitter.xml',
            array('id'=>'twitter')
        ));
        $response = $twitter->user->show('twitter');
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Add verification for ALL optional parameters
     */
    public function testStatusRepliesReturnsResults()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'statuses/mentions.xml', Http\Request::METHOD_GET, 'statuses.mentions.xml'
        ));
        $response = $twitter->status->replies();
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * TODO: Add verification for ALL optional parameters
     */
    public function testFriendshipDestroy()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'friendships/destroy/twitter.xml', Http\Request::METHOD_POST, 'friendships.destroy.twitter.xml'
        ));
        $response = $twitter->friendship->destroy('twitter');
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testBlockingCreate()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'blocks/create/twitter.xml', Http\Request::METHOD_POST, 'blocks.create.twitter.xml'
        ));
        $response = $twitter->block->create('twitter');
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testBlockingExistsReturnsTrueWhenBlockExists()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'blocks/exists/twitter.xml', Http\Request::METHOD_GET, 'blocks.exists.twitter.xml'
        ));
        $this->assertTrue($twitter->block->exists('twitter'));
    }

    public function testBlockingBlocked()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'blocks/blocking.xml', Http\Request::METHOD_GET, 'blocks.blocking.xml'
        ));
        $response = $twitter->block->blocking();
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testBlockingBlockedReturnsIds()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'blocks/blocking/ids.xml', Http\Request::METHOD_GET, 'blocks.blocking.ids.xml',
            array('page'=>1)
        ));
        $response = $twitter->block->blocking(1, true);
        $this->assertTrue($response instanceof Rest\Client\Result);
        $this->assertEquals('23836616', (string) $response->id);
    }

    public function testBlockingDestroy()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'blocks/destroy/twitter.xml', Http\Request::METHOD_POST, 'blocks.destroy.twitter.xml'
        ));
        $response = $twitter->block->destroy('twitter');
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    public function testBlockingExistsReturnsFalseWhenBlockDoesNotExists()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'blocks/exists/padraicb.xml', Http\Request::METHOD_GET, 'blocks.exists.padraicb.xml'
        ));
        $this->assertFalse($twitter->block->exists('padraicb'));
    }

    public function testBlockingExistsReturnsObjectWhenFlagPassed()
    {
        $twitter = new Twitter\Twitter;
        $twitter->setLocalHttpClient($this->stubTwitter(
            'blocks/exists/padraicb.xml', Http\Request::METHOD_GET, 'blocks.exists.padraicb.xml'
        ));
        $response = $twitter->block->exists('padraicb', true);
        $this->assertTrue($response instanceof Rest\Client\Result);
    }

    /**
     * @group ZF-6284
     */
    public function testTwitterObjectsSoNotShareSameHttpClientToPreventConflictingAuthentication()
    {
        $twitter1 = new Twitter\Twitter(array('username'=>'zftestuser1'));
        $twitter2 = new Twitter\Twitter(array('username'=>'zftestuser2'));
        $this->assertFalse($twitter1->getLocalHttpClient() === $twitter2->getLocalHttpClient());
    }

    public function testYouCanRetrieveTheUsersWhoRetweetedATweet()
    {
        $twitter = new Twitter\Twitter();
        $response = $twitter->statusRetweetedBy('85607267692584960');

        $this->assertTrue($response instanceof Rest\Client\Result);
        $this->assertTrue(is_array($response->name), var_export($response, 1));
        $this->assertTrue(in_array('Alessandro Nadalin', $response->name));
    }

}
