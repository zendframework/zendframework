<?php

namespace ZendTest\Service\AgileZen;

use Zend\Service\AgileZen\AgileZen as AgileZenService;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    protected static $roleId;

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
    public function testGetRoles()
    {
        $roles = $this->agileZen->getRoles(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($roles instanceof \Zend\Service\AgileZen\Container);
        foreach ($roles as $role) {
            $this->assertTrue($role instanceof \Zend\Service\AgileZen\Resources\Role);
        }
    }
    public function testAddRole()
    {
        $data = array(
            'name'   => 'testZF',
            'access' => 'read'
        );
        $role = $this->agileZen->addRole(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'), $data);
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($role instanceof \Zend\Service\AgileZen\Resources\Role);
        $this->assertEquals($data['name'], $role->getName());
        $this->assertEquals($data['access'], $role->getAccess());
        if (!empty($role)) {
            self::$roleId = $role->getId();
        }    
    }
    public function testUpdateRole()
    {
        if (empty(self::$roleId)) {
            $this->markTestSkipped('No role added for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $data = array(
            'name'   => 'updated testZF',
            'access' => 'admin'
        );
        $role = $this->agileZen->updateRole(
            constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'), 
            self::$roleId,
            $data
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($role instanceof \Zend\Service\AgileZen\Resources\Role);
        $this->assertEquals(self::$roleId, $role->getId());
        $this->assertEquals($data['name'], $role->getName());
        $this->assertEquals($data['access'], $role->getAccess());
    }
    public function testGetRole()
    {
        if (empty(self::$roleId)) {
            $this->markTestSkipped('No role founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $role = $this->agileZen->getRole(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                self::$roleId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($role instanceof \Zend\Service\AgileZen\Resources\Role);
        $this->assertEquals(self::$roleId, $role->getId());
    }
    public function testRemoveRole()
    {
        if (empty(self::$roleId)) {
            $this->markTestSkipped('No role to delete for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $result = $this->agileZen->removeRole(
            constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
            self::$roleId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($result);
    }
}