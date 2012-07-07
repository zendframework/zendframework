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
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Delicious;

use Zend\Service\Delicious\Delicious as DeliciousClient;
use Zend\Http;
use Zend\Rest\Client as RestClient;

/**
 * @category   Zend_Service
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Delicious
 */
class PublicDataTest extends \PHPUnit_Framework_TestCase
{
    const TEST_UNAME = 'zfTestUser';
    const TEST_PASS  = 'zfuser';
    const TEST_URL  = 'http://framework.zend.com/';

    /**
     * @var Zend_Service_Delicious
     */
    protected $_delicious;

    /**
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_DELICIOUS_ENABLED')) {
            $this->markTestSkipped('\Zend\Service\Delicious online tests are not enabled');
        }
        $httpClient = new Http\Client();
        $httpClient->setOptions(array(
                'useragent' => '\Zend\Service\Delicious - Unit tests/0.1',
                'keepalive' => true
        ));
        RestClient\RestClient::setDefaultHttpClient($httpClient);

        $this->_delicious = new DeliciousClient();
    }

    /**
     * Try to get tags of some user
     *
     * @return void
     */
    public function testGetTags()
    {
        $tags = $this->_delicious->getUserTags(self::TEST_UNAME);

        $this->assertInternalType('array', $tags);
    }

    /**
     * @return void
     */
    public function testGetTagsWithCount()
    {
        $tags = $this->_delicious->getUserTags(self::TEST_UNAME, null, 20);

        $this->assertInternalType('array', $tags);
        $this->assertTrue(count($tags) <= 20);
    }

    /**
     * @return void
     */
    public function testGetTagsWithAtLeast()
    {
        $tags = $this->_delicious->getUserTags(self::TEST_UNAME, 5);

        $this->assertInternalType('array', $tags);
        foreach ($tags as $count) {
            $this->assertTrue($count >= 5);
        }
    }

    /**
     * @return void
     */
    public function testGetNetwork()
    {
        $network = $this->_delicious->getUserNetwork(self::TEST_UNAME);

        $this->assertInternalType('array', $network);
    }

    /**
     * @return void
     */
    public function testGetFans()
    {
        $fans = $this->_delicious->getUserFans(self::TEST_UNAME);

        $this->assertInternalType('array', $fans);
    }

    /**
     * @return void
     */
    public function testGetUserPosts()
    {
        $posts = $this->_delicious->getUserPosts(self::TEST_UNAME, 10);

        $this->assertInstanceOf('Zend\Service\Delicious\PostList', $posts);

        // check if all objects in returned \Zend\Service\Delicious\PostList
        // are instances of \Zend\Service\Delicious\SimplePost
        foreach ($posts as $post) {
            $this->assertInstanceOf('Zend\Service\Delicious\SimplePost', $post);
        }

        // test filtering of Zend_Service_Delicious_PostList by tag name
        $filterPostList = $posts->withTag('zfSite');

        foreach ($filterPostList as $post) {
            $this->assertInternalType('array', $post->getTags());
            $this->assertContains('zfSite', $post->getTags());
        }
    }

    /**
     * Try to get details of some URL
     *
     * @return void
     */
    public function testGetUrlDetails() {
        $details = $this->_delicious->getUrlDetails(self::TEST_URL);

        $this->assertInternalType('array', $details);
        $this->assertArrayHasKey('hash', $details);
        $this->assertArrayHasKey('top_tags', $details);
        $this->assertArrayHasKey('url', $details);
        $this->assertArrayHasKey('total_posts', $details);

        $this->assertEquals(self::TEST_URL, $details['url']);
        $this->assertInternalType('array', $details['top_tags']);
    }
}
