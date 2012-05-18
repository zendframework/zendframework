<?php

namespace ZendTest\Service\AgileZen;

use Zend\Service\AgileZen\AgileZen as AgileZenService;

class StoryTest extends \PHPUnit_Framework_TestCase
{
    protected static $storyId;
     
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_ENABLED')) {
            self::markTestSkipped('Zend\Service\AgileZen tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_APIKEY')) {
            self::markTestSkipped('The ApiKey costant has to be set.');
        }
        if(!defined('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID')) {
            self::markTestSkipped('The project ID costant has to be set.');
        }
        $this->agileZen = new AgileZenService(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_APIKEY'));                                               
    }
    public function testAddStory()
    {
        $data = array(
            'text'    => 'test story',
            'details' => 'details of the test story',
            'tags'    => array('foo', 'bar')
        );
        $params = array (
            'with' => 'tags'
        );
        $story = $this->agileZen->addStory(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'), $data, $params);
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($story instanceof \Zend\Service\AgileZen\Resources\Story);
        $this->assertEquals($data['text'], $story->getText());
        self::$storyId = $story->getId();
        $tags = $story->getTags();
        $this->assertTrue($tags instanceof \Zend\Service\AgileZen\Container);
        foreach ($tags as $tag) {
            $this->assertTrue($tag instanceof \Zend\Service\AgileZen\Resources\Tag);
            $this->assertTrue(($tag->getName()=='foo') || ($tag->getName()=='bar'));
        }
    }
    public function testUpdateStory()
    {
        if (empty(self::$storyId)) {
            $this->markTestSkipped('No stories added for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $data = array(
            'text'    => 'test story updated',
            'details' => 'updated details of the test story'
        );
        $story = $this->agileZen->updateStory(
            constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'), 
            self::$storyId,
            $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($story instanceof \Zend\Service\AgileZen\Resources\Story);
        $this->assertEquals($data['text'], $story->getText());
    }
    public function testGetStory()
    {
        if (empty(self::$storyId)) {
            $this->markTestSkipped('No story founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $story = $this->agileZen->getStory(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                self::$storyId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($story instanceof \Zend\Service\AgileZen\Resources\Story);
        $this->assertEquals(self::$storyId, $story->getId());
    }
    public function testGetStoryWithDetails()
    {
         if (empty(self::$storyId)) {
            $this->markTestSkipped('No story founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $params = array (
            'with' => 'details'
        );
        $story = $this->agileZen->getStory(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                self::$storyId,
                $params
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($story instanceof \Zend\Service\AgileZen\Resources\Story);
        $this->assertEquals(self::$storyId, $story->getId());
        $this->assertEquals('updated details of the test story', $story->getDetails());
    }
    public function testGetStoryWithTags()
    {
         if (empty(self::$storyId)) {
            $this->markTestSkipped('No story founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $params = array (
            'with' => 'tags'
        );
        $story = $this->agileZen->getStory(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                self::$storyId,
                $params
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($story instanceof \Zend\Service\AgileZen\Resources\Story);
        $this->assertEquals(self::$storyId, $story->getId());
        $tags = $story->getTags();
        $this->assertTrue($tags instanceof \Zend\Service\AgileZen\Container);
        foreach ($tags as $tag) {
            $this->assertTrue($tag instanceof \Zend\Service\AgileZen\Resources\Tag);
            $this->assertTrue(($tag->getName()==='foo') || ($tag->getName()==='bar'));
        }
    }
    public function testRemoveStory()
    {
        if (empty(self::$storyId)) {
            $this->markTestSkipped('No story to delete for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $result = $this->agileZen->removeStory(
            constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
            self::$storyId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($result);
    }
}
