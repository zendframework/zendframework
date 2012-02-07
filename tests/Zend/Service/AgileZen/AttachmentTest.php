<?php

namespace ZendTest\Service\AgileZen;

use Zend\Service\AgileZen\AgileZen as AgileZenService;

class AttachmentTest extends \PHPUnit_Framework_TestCase
{
    protected static $attachId;
    
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
    public function testAddAttachment()
    {
        $data = array(__DIR__ . '/_files/zf.gif');
        $attachment = $this->agileZen->addAttachment(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                $data
        );
        if (!$this->agileZen->isSuccessful()) {
            $this->markTestSkipped('Your API key cannot add attachments to the project id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($attachment instanceof \Zend\Service\AgileZen\Resources\Task);
        self::$attachId = $attachment->getId();
    }
    
    public function testGetAttachments()
    {
        $attachments = $this->agileZen->getAttachments(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID')
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        if (empty($attachments)) {
            $this->markTestSkipped('No attachments founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $this->assertTrue($attachments instanceof \Zend\Service\AgileZen\Container);
        foreach ($attachments as $attach) {
            $this->assertTrue($attach instanceof \Zend\Service\AgileZen\Resources\Attachment);
        }
    }
    public function testGetAttachment()
    {
        if (empty(self::$attachId)) {
            $this->markTestSkipped('No attachments founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $attachment = $this->agileZen->getAttachment(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$taskId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($attachment instanceof \Zend\Service\AgileZen\Resources\Attachment);
        $this->assertEquals(self::$attachId, $attachment->getId());
    }
    
    public function testUpdateAttachment()
    {
        if (empty(self::$attachId)) {
            $this->markTestSkipped('No attachment to update for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $data = array (
            'filename' => 'zf_update.gif'
        );
        $attachment = $this->agileZen->updateAttachment(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$attachId,
                $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($attachment instanceof \Zend\Service\AgileZen\Resources\Attachment);
        $this->assertEquals(self::$attachId, $attachment->getId());
        $this->assertEquals($data['filename'], $attachment->getFileName());
    }
    public function testRemoveAttachment()
    {
        if (empty(self::$attachId)) {
            $this->markTestSkipped('No attachment to remove for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $result = $this->agileZen->removeAttachment(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$attachId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($result);
    }
}