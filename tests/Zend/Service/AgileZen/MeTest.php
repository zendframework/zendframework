<?php

namespace ZendTest\Service\AgileZen;

use Zend\Service\AgileZen\AgileZen as AgileZenService;

class MeTest extends \PHPUnit_Framework_TestCase
{  
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_ENABLED')) {
            self::markTestSkipped('Zend\Service\AgileZen tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_APIKEY')) {
            self::markTestSkipped('The ApiKey costant has to be set.');
        }
        $this->agileZen = new AgileZenService(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_APIKEY'));                                               
    }
    public function testGetMe()
    {
        $me = $this->agileZen->getMe();
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($me instanceof \Zend\Service\AgileZen\Resources\User);
    }
    public function testUpdateMe()
    {
        $me = $this->agileZen->getMe();
        $this->assertTrue($this->agileZen->isSuccessful());
        $name = $me->getName();
        
        $data = array(
            'name' => $name . " updated"
        );
        $updatedMe = $this->agileZen->updateMe($data);
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertEquals($data['name'], $updatedMe->getName());
        
        if ($this->agileZen->isSuccessful()) {
            $data = array(
                'name' => $name
            );
            $me = $this->agileZen->updateMe($data);
            $this->assertTrue($this->agileZen->isSuccessful());
            $this->assertEquals($data['name'], $me->getName());
        }
    }
    public function testGetStories()
    {
        $stories = $this->agileZen->getMyStories();
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($stories instanceof \Zend\Service\AgileZen\Container);
        foreach ($stories as $story) {
            $this->assertTrue($story instanceof \Zend\Service\AgileZen\Resources\Story);
        }
    }
}