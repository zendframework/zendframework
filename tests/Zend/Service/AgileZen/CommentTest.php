<?php

namespace ZendTest\Service\AgileZen;

use Zend\Service\AgileZen\AgileZen as AgileZenService;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    const TEXT        = 'This is the test comment';
    const TEXT_UPDATE = 'This is the updated comment';
    
    protected static $commentId;
   
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
        if(!defined('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID')) {
            self::markTestSkipped('The story ID costant has to be set.');
        }
        $this->agileZen = new AgileZenService(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_APIKEY'));                                               
    }
    public function testAddComment()
    {
        $data = array(
            'text' => self::TEXT
        );
        $comment = $this->agileZen->addComment(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($comment instanceof \Zend\Service\AgileZen\Resources\Comment);
        self::$commentId = $comment->getId();
    }
    
    public function testGetComments()
    {
        $comments = $this->agileZen->getComments(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID')
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        if (empty($comments)) {
            $this->markTestSkipped('No comments founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $this->assertTrue($comments instanceof \Zend\Service\AgileZen\Container);
        foreach ($comments as $comment) {
            $this->assertTrue($comment instanceof \Zend\Service\AgileZen\Resources\Comment);
        }
    }
    public function testGetComment()
    {
        if (empty(self::$commentId)) {
            $this->markTestSkipped('No comments founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $comment = $this->agileZen->getComment(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$commentId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($comment instanceof \Zend\Service\AgileZen\Resources\Comment);
        $this->assertEquals(self::$commentId, $comment->getId());
        $this->assertEquals(self::TEXT, $comment->getText());
    }
    
    public function testUpdateAttachment()
    {
        if (empty(self::$commentId)) {
            $this->markTestSkipped('No comment to update for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $data = array (
            'text' => self::TEXT_UPDATE
        );
        $comment = $this->agileZen->updateComment(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$commentId,
                $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($comment instanceof \Zend\Service\AgileZen\Resources\Comment);
        $this->assertEquals(self::$commentId, $comment->getId());
        $this->assertEquals($data['text'], $comment->getText());
    }
    public function testRemoveComment()
    {
        if (empty(self::$commentId)) {
            $this->markTestSkipped('No comment to remove for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $result = $this->agileZen->removeComment(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$commentId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($result);
    }
}
