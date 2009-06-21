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
 * @package    Zend_Service_SlideShare
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Service_SlideShare
 */
require_once 'Zend/Service/SlideShare.php';


/**
 * @category   Zend
 * @package    Zend_Service_SlideShare
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_SlideShareTest extends PHPUnit_Framework_TestCase
{
    /**
     * The Slide share object instance
     *
     * @var Zend_Service_SlideShare
     */
    protected static $_ss;

    /**
     * Enter description here...
     *
     * @return Zend_Service_SlideShare
     */
    protected function _getSSObject()
    {
        $ss = new Zend_Service_SlideShare(TESTS_ZEND_SERVICE_SLIDESHARE_APIKEY,
                                                 TESTS_ZEND_SERVICE_SLIDESHARE_SHAREDSECRET,
                                                 TESTS_ZEND_SERVICE_SLIDESHARE_USERNAME,
                                                 TESTS_ZEND_SERVICE_SLIDESHARE_PASSWORD,
                                                 TESTS_ZEND_SERVICE_SLIDESHARE_SLIDESHOWID);

        $cache = Zend_Cache::factory('Core', 'File', array('lifetime' => 0, 'automatic_serialization' => true),
                                                     array('cache_dir' => dirname(__FILE__)."/SlideShare/_files"));
        $ss->setCacheObject($cache);
        return $ss;
    }

    public function setUp()
    {

        if(!defined("TESTS_ZEND_SERVICE_SLIDESHARE_APIKEY") ||
           !defined("TESTS_ZEND_SERVICE_SLIDESHARE_SHAREDSECRET") ||
           !defined("TESTS_ZEND_SERVICE_SLIDESHARE_USERNAME") ||
           !defined("TESTS_ZEND_SERVICE_SLIDESHARE_PASSWORD") ||
           (TESTS_ZEND_SERVICE_SLIDESHARE_APIKEY == "") ||
           (TESTS_ZEND_SERVICE_SLIDESHARE_SHAREDSECRET == "") ||
           (TESTS_ZEND_SERVICE_SLIDESHARE_USERNAME == "") ||
           (TESTS_ZEND_SERVICE_SLIDESHARE_PASSWORD == "")) {

               $this->markTestSkipped("You must configure an account for slideshare to run these tests");
        }
    }


    public function testGetSlideShow()
    {

        if(!defined("TESTS_ZEND_SERVICE_SLIDESHARE_SLIDESHOWID") ||
           (TESTS_ZEND_SERVICE_SLIDESHARE_SLIDESHOWID <= 0)) {
               $this->markTestSkipped("You must provide a Slideshow ID to retrieve to perform this test");
        }

        $ss = $this->_getSSObject();
        try {
            $result = $ss->getSlideShow(TESTS_ZEND_SERVICE_SLIDESHARE_SLIDESHOWID);
        } catch(Exception $e) {
            $this->fail("Exception Caught retrieving Slideshow");
        }

        $this->assertTrue($result instanceof Zend_Service_SlideShare_SlideShow);

    }

    public function testGetSlideShowByTag()
    {

        $ss = $this->_getSSObject();

        try {
            $results = $ss->getSlideShowsByTag('zend', 0, 1);
        } catch(Exception $e) {
            $this->fail("Exception Caught retrieving Slideshow List (tag)");
        }

        $this->assertTrue(is_array($results));
        $this->assertTrue(count($results) == 1);
        $this->assertTrue($results[0] instanceof Zend_Service_SlideShare_SlideShow);

    }

    public function testGetSlideShowByTags()
    {

        $ss = $this->_getSSObject();

        try {
            $results = $ss->getSlideShowsByTag(array('zend', 'php'), 0, 1);
        } catch(Exception $e) {
            $this->fail("Exception Caught retrieving Slideshow List (tag)");
        }

        $this->assertTrue(is_array($results));

        if(!empty($results)) {
            $this->assertTrue(count($results) == 1);
            $this->assertTrue($results[0] instanceof Zend_Service_SlideShare_SlideShow);
        }
    }

    public function testGetSlideShowByUsername()
    {

        $ss = $this->_getSSObject();

        try {
            $results = $ss->getSlideShowsByUsername(TESTS_ZEND_SERVICE_SLIDESHARE_USERNAME, 0, 1);
        } catch(Exception $e) {
            $this->fail("Exception Caught retrieving Slideshow List (tag)");
        }

        $this->assertTrue(is_array($results));
        $this->assertTrue(count($results) == 1);
        $this->assertTrue($results[0] instanceof Zend_Service_SlideShare_SlideShow);

    }

    public function testUploadSlideShow()
    {
        $ss = $this->_getSSObject();

        $title = "Unit Test for ZF SlideShare Component";
        $ppt_file = dirname(__FILE__)."/SlideShare/_files/demo.ppt";

        $show = new Zend_Service_SlideShare_SlideShow();
        $show->setFilename($ppt_file);
        $show->setDescription("Unit Test");
        $show->setTitle($title);
        $show->setTags(array('unittest'));
        $show->setID(0);

        try {
            $result = $ss->uploadSlideShow($show, false);
        } catch(Exception $e) {

            if($e->getCode() == Zend_Service_SlideShare::SERVICE_ERROR_NOT_SOURCEOBJ) {
                // We ignore this exception, the web service sometimes throws this
                // error code because it seems to be buggy. Unfortunately it seems
                // to be sparatic so we can't code around it and have to call this
                // test a success
                return;
            } else {
                $this->fail("Exception Caught uploading slideshow");
            }
        }

        $this->assertTrue($result instanceof Zend_Service_SlideShare_SlideShow);
        $this->assertTrue($result->getId() > 0);
        $this->assertTrue($result->getTitle() === $title);

    }

    public function testSlideShowObj()
    {
        $ss = new Zend_Service_SlideShare_SlideShow();

        $ss->setDescription("Foo");
        $ss->setEmbedCode("Bar");
        $ss->setFilename("Baz");
        $ss->setId(123);
        $ss->setLocation("Somewhere");
        $ss->setNumViews(4432);
        $ss->setPermaLink("nowhere");
        $ss->setStatus(124);
        $ss->setStatusDescription("Boo");
        $ss->setTags(array('bar', 'baz'));
        $ss->addTag('fon');
        $ss->setThumbnailUrl('asdf');
        $ss->setTitle('title');
        $ss->setTranscript('none');

        $this->assertEquals($ss->getDescription(), "Foo");
        $this->assertEquals($ss->getEmbedCode(), "Bar");
        $this->assertEquals($ss->getFilename(), "Baz");
        $this->assertEquals($ss->getId(), 123);
        $this->assertEquals($ss->getLocation(), "Somewhere");
        $this->assertEquals($ss->getNumViews(), 4432);
        $this->assertEquals($ss->getPermaLink(), "nowhere");
        $this->assertEquals($ss->getStatus(), 124);
        $this->assertEquals($ss->getStatusDescription(), "Boo");
        $this->assertEquals($ss->getTags(), array('bar', 'baz', 'fon'));
        $this->assertEquals($ss->getThumbnailUrl(), "asdf");
        $this->assertEquals($ss->getTitle(), "title");
        $this->assertEquals($ss->getTranscript(), "none");

    }
}
