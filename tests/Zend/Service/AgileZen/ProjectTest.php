<?php

namespace ZendTest\Service\AgileZen;

use Zend\Service\AgileZen\AgileZen as AgileZenService;
use Zend\Service\AgileZen\Resources\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    protected static $roleId;
    protected static $inviteId;
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
    public function testConstruct()
    {
        $data = array (
            'id'          => 1,
            'name'        => 'test',
            'description' => 'test',
            'details'     => 'test',
            'createTime'  => '2011-02-17T18:47:16', 
            'owner'       => array(
                'id' => 1,
                'name' => 'test',
                'userName' => 'test',
                'email' => 'test@test'
            )
        );
        $project = new Project($this->agileZen, $data);
        $this->assertTrue($project instanceof \Zend\Service\AgileZen\Resources\Project);
    }
    public function testMissDataArrayInConstruct()
    {
        $this->setExpectedException(
            'Zend\Service\AgileZen\Resources\Exception\InvalidArgumentException',
            'You must pass an array of data'
        );
        $project = new Project($this->agileZen, 1);
    }
    public function testValidKeys()
    {
        $keys = array (
            'id'          => 1,
            'name'        => 'test',
            'description' => 'test',
            'details'     => 'test',
            'createTime'  => '2011-02-17T18:47:16', 
            'owner'       => array(
                'id' => 1,
                'name' => 'test',
                'userName' => 'test',
                'email' => 'test@test'
            )
        );
        $this->assertTrue(Project::validKeys($keys));  
        
        $keys = array ('id' => null, 'test' => null);
        $this->assertFalse(Project::validKeys($keys));
        
        $keys = array (
            'id'          => 1, 
            'owner'       => array(
                'id' => 1,
                'test' => 'test'
            )
        );
        $this->assertFalse(Project::validKeys($keys));
        
    }
    public function testGetProjects()
    {
        $projects = $this->agileZen->getProjects();
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($projects instanceof \Zend\Service\AgileZen\Container);
        if (count($projects)==0) {
            self::markTestSkipped('No project to test, I cannot continue the test.');
        }
        $this->assertTrue($projects[0] instanceof \Zend\Service\AgileZen\Resources\Project);
    }
    public function testGetProject()
    {
        $project = $this->agileZen->getProject(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));

        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($project instanceof \Zend\Service\AgileZen\Resources\Project);
        $this->assertTrue($project->getOwner() instanceof \Zend\Service\AgileZen\Resources\User);
    }
    public function testUpdateProject()
    {
        $project = $this->agileZen->getProject(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $oldDescription = $project->getDescription();
        
        $data = array(
            'description' => 'description changed!'
        );
        
        $newProject = $this->agileZen->updateProject(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'), $data);
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($newProject instanceof \Zend\Service\AgileZen\Resources\Project);
        $this->assertEquals($data['description'], $newProject->getDescription());
        if ($this->agileZen->isSuccessful()) {
            $data = array(
                'description' => $oldDescription
            );
            $oldProject = $this->agileZen->updateProject(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'), $data);
            $this->assertTrue($this->agileZen->isSuccessful());
            $this->assertTrue($oldProject instanceof \Zend\Service\AgileZen\Resources\Project);
            $this->assertEquals($data['description'], $oldProject->getDescription());
        }
    }
    public function testGetMembers()
    {
        $members = $this->agileZen->getMembers(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($members instanceof \Zend\Service\AgileZen\Container);
        $this->assertTrue($members[0] instanceof \Zend\Service\AgileZen\Resources\User); 
    }
    public function testMembersByProject()
    {
        $project = $this->agileZen->getProject(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $members = $project->getMembers();
        $this->assertTrue($members instanceof \Zend\Service\AgileZen\Container);
        $this->assertTrue($members[0] instanceof \Zend\Service\AgileZen\Resources\User); 
    }
    public function testAddMemberProject()
    {
        if (!defined('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_MEMBER_NAME') ||
               constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_MEMBER_NAME')===null) {
            $this->markTestSkipped('No member defined to add to the project.');
        }
        $result = $this->agileZen->addProjectMember(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_MEMBER_NAME')
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($result);
    }
    public function testRemoveMemberProject()
    {
        if (!defined('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_MEMBER_NAME') ||
               constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_MEMBER_NAME')===null) {
            $this->markTestSkipped('No member defined to delete from the project.');
        }
        $result = $this->agileZen->removeProjectMember(
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
                constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_MEMBER_NAME')
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($result);
    }
    public function testProjectPhases()
    {
        $phases = $this->agileZen->getPhases(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($phases instanceof \Zend\Service\AgileZen\Container);
        $this->assertTrue($phases[0] instanceof \Zend\Service\AgileZen\Resources\Phase); 
    }
    public function testPhasesByProject()
    {
        $project = $this->agileZen->getProject(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $phases = $project->getPhases();
        $this->assertTrue($phases instanceof \Zend\Service\AgileZen\Container);
        $this->assertTrue($phases[0] instanceof \Zend\Service\AgileZen\Resources\Phase); 
    }   
    public function testProjectStories()
    {
        $stories = $this->agileZen->getStories(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        $this->assertTrue($this->agileZen->isSuccessful());
        if (!empty($stories)) {
            $this->assertTrue($stories instanceof \Zend\Service\AgileZen\Container);
            foreach ($stories as $story) {
                $this->assertTrue($story instanceof \Zend\Service\AgileZen\Resources\Story);
            }    
        } else {
            $this->markTestSkipped('No stories for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }    
    }
    public function testStoriesByProject()
    {
        $project = $this->agileZen->getProject(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        
        $this->assertTrue($this->agileZen->isSuccessful());
        $stories = $project->getStories();
        if (!empty($stories)) {
            $this->assertTrue($stories instanceof \Zend\Service\AgileZen\Container);
            foreach ($stories as $story) {
                $this->assertTrue($story instanceof \Zend\Service\AgileZen\Resources\Story);
            }
        } else {
            $this->markTestSkipped('No stories for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }     
    }   
    public function testProjectRoles()
    {
        $roles = $this->agileZen->getRoles(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($roles instanceof \Zend\Service\AgileZen\Container);
        $found = false;
        foreach ($roles as $role) {
            $this->assertTrue($role instanceof \Zend\Service\AgileZen\Resources\Role);
            if (strtolower($role->getName())=='administrators') {
                $found = true;
                self::$roleId = $role->getId();
            }
        }
        $this->assertTrue($found);
    }
    public function testProjectRole()
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
        $this->assertEquals(strtolower($role->getName()), 'administrators');
    }
    public function testProjectInvites()
    {
        $invites = $this->agileZen->getInvites(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        $this->assertTrue($this->agileZen->isSuccessful());
        if (empty($invites)) {
             $this->markTestSkipped('No invites founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        } else {
            $this->assertTrue($invites instanceof \Zend\Service\AgileZen\Container);
            $this->assertTrue($invites[0] instanceof \Zend\Service\AgileZen\Resources\Invite);
            self::$inviteId = $invites[0]->getId();
        }    
    }
    public function testProjectInvite()
    {
        if (empty(self::$inviteId)) {
            $this->markTestSkipped('No invites founded for the project Id ' .
                    constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        }
        $invite = $this->agileZen->getInvite(
            constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'),
            self::$inviteId
        );
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue($invite instanceof \Zend\Service\AgileZen\Resources\Invite);
    }
    public function testProjectMetrics()
    {
        $metrics = $this->agileZen->getProjectMetrics(constant('TESTS_ZEND_SERVICE_AGILEZEN_ONLINE_PROJECT_ID'));
        $this->assertTrue($this->agileZen->isSuccessful());
        $this->assertTrue(isset($metrics['throughput'])); 
        $this->assertTrue(isset($metrics['leadTime'])); 
        $this->assertTrue(isset($metrics['cycleTime'])); 
        $this->assertTrue(isset($metrics['workTime'])); 
        $this->assertTrue(isset($metrics['waitTime'])); 
        $this->assertTrue(isset($metrics['blockedTime'])); 
        $this->assertTrue(isset($metrics['efficiency']));  
    }
}
