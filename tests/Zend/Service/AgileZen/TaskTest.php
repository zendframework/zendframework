<?php

namespace ZendTest\Service\AgileZen;

use Zend\Service\AgileZen\AgileZen as AgileZenService;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    protected static $taskId;
  
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
    public function testAddTask()
    {
        $data = array (
            'text'   => 'testing task',
            'status' => 'incomplete'
        );
        $task = $this->agileZen->addTask(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($task instanceof \Zend\Service\AgileZen\Resources\Task);
        self::$taskId = $task->getId();
    }
    public function testGetTasks()
    {
        $tasks = $this->agileZen->getTasks(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID')
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        if (empty($tasks)) {
            $this->markTestSkipped('No tasks founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $this->assertTrue($tasks instanceof \Zend\Service\AgileZen\Container);
        foreach ($tasks as $task) {
            $this->assertTrue($task instanceof \Zend\Service\AgileZen\Resources\Task);
        }
    }
    public function testGetTask()
    {
        if (empty(self::$taskId)) {
            $this->markTestSkipped('No tasks founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $task = $this->agileZen->getTask(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$taskId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($task instanceof \Zend\Service\AgileZen\Resources\Task);
        $this->assertEquals(self::$taskId, $task->getId());
    }
    
    public function testUpdateTask()
    {
        if (empty(self::$taskId)) {
            $this->markTestSkipped('No task to update for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $data = array (
            'text'   => 'updating task',
            'status' => 'complete'
        );
        $task = $this->agileZen->updateTask(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$taskId,
                $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($task instanceof \Zend\Service\AgileZen\Resources\Task);
        $this->assertEquals(self::$taskId, $task->getId());
        $this->assertEquals($data['text'], $task->getText());
        $this->assertEquals($data['status'], $task->getStatus());
    }
    public function testRemoveTask()
    {
        if (empty(self::$taskId)) {
            $this->markTestSkipped('No task to remove for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID') . ' and story Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'));
        }
        $result = $this->agileZen->removeTask(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_STORY_ID'),
                self::$taskId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($result);
    }
}
