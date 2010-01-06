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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Service_Delicious
 */
require_once 'Zend/Service/Delicious.php';


/**
 * @category   Zend_Service
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Delicious
 */
class Zend_Service_Delicious_PublicDataTest extends PHPUnit_Framework_TestCase
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
        $httpClient = new Zend_Http_Client();
        $httpClient->setConfig(array(
                'useragent' => 'Zend_Service_Delicious - Unit tests/0.1',
                'keepalive' => true
        ));
        Zend_Rest_Client::setHttpClient($httpClient);

        $this->_delicious = new Zend_Service_Delicious();
    }

    /**
     * Try to get tags of some user
     *
     * @return void
     */
    public function testGetTags()
    {
        $tags = $this->_delicious->getUserTags(self::TEST_UNAME);

        $this->assertType('array', $tags);
    }

    /**
     * @return void
     */
    public function testGetTagsWithCount()
    {
        $tags = $this->_delicious->getUserTags(self::TEST_UNAME, null, 20);

        $this->assertType('array', $tags);
        $this->assertTrue(count($tags) <= 20);
    }

    /**
     * @return void
     */
    public function testGetTagsWithAtLeast()
    {
        $tags = $this->_delicious->getUserTags(self::TEST_UNAME, 5);

        $this->assertType('array', $tags);
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

        $this->assertType('array', $network);
    }

    /**
     * @return void
     */
    public function testGetFans()
    {
        $fans = $this->_delicious->getUserFans(self::TEST_UNAME);

        $this->assertType('array', $fans);
    }

    /**
     * @return void
     */
    public function testGetUserPosts()
    {
        $posts = $this->_delicious->getUserPosts(self::TEST_UNAME, 10);

        $this->assertType('Zend_Service_Delicious_PostList', $posts);

        // check if all objects in returned Zend_Service_Delicious_PostList
        // are instances of Zend_Service_Delicious_SimplePost
        foreach ($posts as $post) {
            $this->assertType('Zend_Service_Delicious_SimplePost', $post);
        }

        // test filtering of Zend_Service_Delicious_PostList by tag name
        $filterPostList = $posts->withTag('zfSite');

        foreach ($filterPostList as $post) {
            $this->assertType('array', $post->getTags());
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

        $this->assertType('array', $details);
        $this->assertArrayHasKey('hash', $details);
        $this->assertArrayHasKey('top_tags', $details);
        $this->assertArrayHasKey('url', $details);
        $this->assertArrayHasKey('total_posts', $details);

        $this->assertEquals(self::TEST_URL, $details['url']);
        $this->assertType('array', $details['top_tags']);
    }
}
