<?php

namespace ZendTest\Service\AgileZen;

use Zend\Service\AgileZen\AgileZen as AgileZenService;

class PhaseTest extends \PHPUnit_Framework_TestCase
{
    protected static $phaseId;
    
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_ENABLED')) {
            self::markTestSkipped('Zend\Service\AgileZen tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_APIKEY')) {
            self::markTestSkipped('The ApiKey constant must be setted.');
        }
        if(!defined('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID')) {
            self::markTestSkipped('The project Id constant must be setted.');
        }
        $this->agileZen = new AgileZenService(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_APIKEY'));                                               
    }
    public function testAddPhase()
    {
        $data = array (
            'name'        => 'testing phase',
            'description' => 'description phase'
        );
        $phase = $this->agileZen->addPhase(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($phase instanceof \Zend\Service\AgileZen\Resources\Phase);
        $this->assertEquals($data['name'], $phase->getName());
        $this->assertEquals($data['description'], $phase->getDescription());
        self::$phaseId = $phase->getId();
    }
    public function testGetPhase()
    {
        if (empty(self::$phaseId)) {
            $this->markTestSkipped('No phase to get for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $phase = $this->agileZen->getPhase(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                self::$phaseId
        );
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($phase instanceof \Zend\Service\AgileZen\Resources\Phase);
        $this->assertEquals(self::$phaseId, $phase->getId());
    }
    public function testUpdatePhase()
    {
        if (empty(self::$phaseId)) {
            $this->markTestSkipped('No phase to update for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $data = array (
            'name'        => 'updated phase',
            'description' => 'description updated'
        );
        $phase = $this->agileZen->updatePhase(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                self::$phaseId,
                $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($phase instanceof \Zend\Service\AgileZen\Resources\Phase);
        $this->assertEquals(self::$phaseId, $phase->getId());
        $this->assertEquals($data['name'], $phase->getName());
        $this->assertEquals($data['description'], $phase->getDescription());
    }
    public function testRemovePhase()
    {
        if (empty(self::$phaseId)) {
            $this->markTestSkipped('No phase to delete for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $result = $this->agileZen->removePhase(
            constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
            self::$phaseId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($result);
    }
}
