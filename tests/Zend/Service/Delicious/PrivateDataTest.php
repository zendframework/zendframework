<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Delicious\PrivateData;

use DateTime;
use Zend\Service\Delicious;
use Zend\Http;
use Zend\Rest\Client as RestClient;

/**
 * @category   Zend_Service
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Delicious
 */
class PrivateDataTest extends \PHPUnit_Framework_TestCase
{
    const TEST_UNAME = 'zfTestUser';
    const TEST_PASS  = 'zfuser';

    // artificial wait interval between successive calls
    const API_CALL_INTERVAL = 3;

    private static $TEST_POST_TITLE  = 'test - title';
    private static $TEST_POST_URL    = 'http://zfdev.com/unittests/delicious/test_url_1';
    private static $TEST_POST_NOTES  = 'test - note';
    private static $TEST_POST_TAGS   = array('testTag1','testTag2');
    private static $TEST_POST_SHARED = false;

    /**
     * @var \Zend\Service\Delicious
     */
    protected $delicious;

    /**
     *
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_DELICIOUS_ENABLED')) {
            $this->markTestSkipped('\Zend\Service\Delicious online tests are not enabled');
        }
        $httpClient = new Http\Client();
        $httpClient->setOptions(array(
                'useragent' => 'Zend\Service\Delicious - Unit tests/0.1',
                'keepalive' => true
        ));

        $this->delicious = new Delicious\Delicious(self::TEST_UNAME, self::TEST_PASS, $httpClient);
    }

    /**
     *
     * @return void
     */
    public function testLastUpdate()
    {
        $this->assertInstanceOf('DateTime', $this->delicious->getLastUpdate());
    }

    /**
     *
     * @return void
     */
    public function testTagsAndBundles()
    {
        // get tags
        $tags = $this->delicious->getTags();
        $this->assertInternalType('array', $tags);
        $tags = array_keys($tags);

        if (count($tags) < 1) {
            $this->fail('Test account corrupted - no tags');
        }

        $oldTagName = $tags[0];
        $newTagName = uniqid('tag');

        // rename tag
        $this->delicious->renameTag($oldTagName, $newTagName);

        sleep(self::API_CALL_INTERVAL);

        // get renamed tags
        $tags = $this->delicious->getTags();

        $this->assertArrayHasKey($newTagName, $tags);
        $this->assertArrayNotHasKey($oldTagName, $tags);

        $tags = array_keys($tags);

        // add new bundle
        $newBundleName = uniqid('bundle');
        $this->delicious->addBundle($newBundleName, $tags);

        sleep(self::API_CALL_INTERVAL);

        // check if bundle was added
        $bundles = $this->delicious->getBundles();
        $this->assertInternalType('array', $bundles);
        $this->assertArrayHasKey($newBundleName, $bundles);
        $this->assertEquals($tags, $bundles[$newBundleName]);

        // delete bundle
        $this->delicious->deleteBundle($newBundleName);

        sleep(self::API_CALL_INTERVAL);

        // check if bundle was deleted
        $bundles = $this->delicious->getBundles();
        $this->assertArrayNotHasKey($newBundleName, $bundles);
    }

    /**
     *
     * @return void
     */
    public function _testAddDeletePost()
    {
        $newPost = $this->delicious->createNewPost(self::$TEST_POST_TITLE, self::$TEST_POST_URL)
                                    ->setNotes(self::$TEST_POST_NOTES)
                                    ->setTags(self::$TEST_POST_TAGS)
                                    ->setShared(self::$TEST_POST_SHARED);

        // check if post was created correctly
        $this->assertEquals(self::$TEST_POST_TITLE, $newPost->getTitle());
        $this->assertEquals(self::$TEST_POST_URL, $newPost->getUrl());
        $this->assertEquals(self::$TEST_POST_NOTES, $newPost->getNotes());
        $this->assertEquals(self::$TEST_POST_TAGS, $newPost->getTags());
        $this->assertEquals(self::$TEST_POST_SHARED, $newPost->getShared());

        // test tag adding to tag
        $newTagName = uniqid('tag');
        $newPost->addTag($newTagName);
        $this->assertContains($newTagName, $newPost->getTags());

        // test tag removeing
        $newPost->removeTag($newTagName);
        $this->assertNotContains($newTagName, $newPost->getTags());

        // send post to del.icio.us
        $newPost->save();

        sleep(self::API_CALL_INTERVAL);

        // get the post back
        $returnedPosts = $this->delicious->getPosts(null, null, self::$TEST_POST_URL);

        $this->assertEquals(1, count($returnedPosts));

        $savedPost = $returnedPosts[0];

        // check if post was saved correctly
        $this->assertEquals(self::$TEST_POST_TITLE, $savedPost->getTitle());
        $this->assertEquals(self::$TEST_POST_URL, $savedPost->getUrl());
        $this->assertEquals(self::$TEST_POST_NOTES, $savedPost->getNotes());
        $this->assertEquals(self::$TEST_POST_TAGS, $savedPost->getTags());
        $this->assertEquals(self::$TEST_POST_SHARED, $savedPost->getShared());
        $this->assertInstanceOf('DateTime', $savedPost->getDate());
        $this->assertInternalType('string', $savedPost->getHash());
        $this->assertInternalType('integer', $savedPost->getOthers());

        // delete post
        $savedPost->delete();

        sleep(self::API_CALL_INTERVAL);

        // check if post was realy deleted
        $returnedPosts = $this->delicious->getPosts(null, null, self::$TEST_POST_URL);
        $this->assertEquals(0, count($returnedPosts));
    }

    /**
     * Ensures that getAllPosts() provides expected behavior
     *
     * @return void
     */
    public function testGetAllPosts()
    {
        $posts = $this->delicious->getAllPosts('zfSite');
        $this->assertInstanceOf('Zend\Service\Delicious\PostList', $posts);

        foreach ($posts as $post) {
            $this->assertContains('zfSite', $post->getTags());
        }
    }

    /**
     * Ensures that getRecentPosts() provides expected behavior
     *
     * @return void
     */
    public function testGetRecentPosts()
    {
        $posts = $this->delicious->getRecentPosts('zfSite', 10);
        $this->assertInstanceOf('Zend\Service\Delicious\PostList', $posts);
        $this->assertTrue(count($posts) <= 10);

        foreach ($posts as $post) {
            $this->assertContains('zfSite', $post->getTags());
        }
    }

    /**
     * Ensures that getPosts() provides expected behavior
     *
     * @return void
     */
    public function testGetPosts()
    {
        $posts = $this->delicious->getPosts('zfSite', new DateTime(), 'help');
        $this->assertInstanceOf('Zend\Service\Delicious\PostList', $posts);
        $this->assertTrue(count($posts) <= 10);

        foreach ($posts as $post) {
            $this->assertContains('zfSite', $post->getTags());
        }
    }

    /**
     *
     * @return void
     */
    public function testDates()
    {
        $this->assertInternalType('array', $this->delicious->getDates());
    }
}
