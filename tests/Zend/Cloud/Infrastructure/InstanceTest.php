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
 * @package    Zend\Cloud\Infrastructure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Cloud\Infrastructure;

use Zend\Cloud\Infrastructure\Instance,
    PHPUnit_Framework_TestCase as TestCase,
    ZendTest\Cloud\Infrastructure\Adapter\TestAssets\MockAdapter;

class InstanceTest extends TestCase
{
    /**
     * Mock class for the Adapter (dummy methods)
     * 
     * @var MockAdapter
     */
    protected static $adapter;

    /**
     * @var array
     */
    protected static $data;

    /**
     * @SetUpBeforeClass
     */
    public static function setUpBeforeClass()
    {
        self::$data = array(
            Instance::INSTANCE_ID         => 'foo',
            Instance::INSTANCE_IMAGEID    => 'foo',
            Instance::INSTANCE_NAME       => 'foo',
            Instance::INSTANCE_LAUNCHTIME => 'foo',
            Instance::INSTANCE_PUBLICDNS  => 'foo',
            Instance::INSTANCE_CPU        => 'foo',
            Instance::INSTANCE_RAM        => 'foo',
            Instance::INSTANCE_STORAGE    => 'foo',
            Instance::INSTANCE_STATUS     => 'foo',
            Instance::INSTANCE_ZONE       => 'foo',
        );
        self::$adapter = new MockAdapter();
    }

    /**
     * Test all the constants of the class
     */
    public function testConstants()
    {
        $this->assertEquals('running', Instance::STATUS_RUNNING);
        $this->assertEquals('stopped', Instance::STATUS_STOPPED);
        $this->assertEquals('shutting-down', Instance::STATUS_SHUTTING_DOWN);
        $this->assertEquals('rebooting', Instance::STATUS_REBOOTING);
        $this->assertEquals('terminated', Instance::STATUS_TERMINATED);
        $this->assertEquals('id',Instance::INSTANCE_ID);
        $this->assertEquals('imageId',Instance::INSTANCE_IMAGEID);
        $this->assertEquals('name',Instance::INSTANCE_NAME);
        $this->assertEquals('status',Instance::INSTANCE_STATUS);
        $this->assertEquals('publicDns',Instance::INSTANCE_PUBLICDNS);
        $this->assertEquals('cpu',Instance::INSTANCE_CPU);
        $this->assertEquals('ram',Instance::INSTANCE_RAM);
        $this->assertEquals('storageSize',Instance::INSTANCE_STORAGE);
        $this->assertEquals('zone',Instance::INSTANCE_ZONE);
        $this->assertEquals('launchTime',Instance::INSTANCE_LAUNCHTIME);
        $this->assertEquals('CpuUsage',Instance::MONITOR_CPU);
        $this->assertEquals('NetworkIn',Instance::MONITOR_NETWORK_IN);
        $this->assertEquals('NetworkOut',Instance::MONITOR_NETWORK_OUT);
        $this->assertEquals('DiskWrite',Instance::MONITOR_DISK_WRITE);
        $this->assertEquals('DiskRead',Instance::MONITOR_DISK_READ);
        $this->assertEquals('StartTime',Instance::MONITOR_START_TIME);
        $this->assertEquals('EndTime',Instance::MONITOR_END_TIME);
        $this->assertEquals('username',Instance::SSH_USERNAME);
        $this->assertEquals('password',Instance::SSH_PASSWORD);
        $this->assertEquals('privateKey',Instance::SSH_PRIVATE_KEY);
        $this->assertEquals('publicKey',Instance::SSH_PUBLIC_KEY);
        $this->assertEquals('passphrase',Instance::SSH_PASSPHRASE);
    }

    /**
     * Test construct with missing params
     */
    public function testConstructExceptionMissingParams()
    {
        $this->setExpectedException(
            'Zend\Cloud\Infrastructure\Exception\InvalidArgumentException',
            'You must pass an array of parameters'
        );
        $instance = new Instance(self::$adapter, array());
    }

    /**
     * Test construct with invalid keys in the params
     */
    public function testConstructExceptionInvalidKeys()
    {
        $this->setExpectedException(
            'Zend\Cloud\Infrastructure\Exception\InvalidArgumentException',
            'The param "'.Instance::INSTANCE_ID.'" is a required param for Zend\Cloud\Infrastructure\Instance'
        );
        $instance = new Instance(self::$adapter, array('foo'=>'bar'));
    }

    /**
     * Test get Id
     */
    public function testGetId()
    {
        $instance = new Instance(self::$adapter,self::$data);
        $this->assertEquals('foo', $instance->getId());
        $this->assertEquals('foo', $instance->getAttribute(Instance::INSTANCE_ID));
    }

    /**
     * Test get Image Id
     */
    public function testGetImageId()
    {
        $instance = new Instance(self::$adapter,self::$data);
        $this->assertEquals('foo', $instance->getImageId());
        $this->assertEquals('foo', $instance->getAttribute(Instance::INSTANCE_IMAGEID));
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $instance = new Instance(self::$adapter,self::$data);
        $this->assertEquals('foo', $instance->getName());
        $this->assertEquals('foo', $instance->getAttribute(Instance::INSTANCE_NAME));
    }

    /**
     * Test get status
     */
    public function testGetStatus()
    {
        $instance = new Instance(self::$adapter,self::$data);
        
        $this->assertEquals(
            self::$adapter->statusInstance($instance::INSTANCE_ID), 
            $instance->getStatus()
        );
        $this->assertEquals('foo', $instance->getAttribute(Instance::INSTANCE_STATUS));
    }

    /**
     * Test get public DNS
     */
    public function testGetPublicDns()
    {
        $instance = new Instance(self::$adapter, self::$data);
        $this->assertEquals('foo', $instance->getPublicDns());
        $this->assertEquals('foo', $instance->getAttribute(Instance::INSTANCE_PUBLICDNS));
    }

    /**
     * Test get CPU
     */
    public function testGetCpu()
    {
        $instance = new Instance(self::$adapter,self::$data);
        $this->assertEquals('foo', $instance->getCpu());
        $this->assertEquals('foo', $instance->getAttribute(Instance::INSTANCE_CPU));
    }

    /**
     * Test get RAM size
     */
    public function testGetRam()
    {
        $instance = new Instance(self::$adapter,self::$data);
        $this->assertEquals('foo',$instance->getRamSize());
        $this->assertEquals('foo',$instance->getAttribute(Instance::INSTANCE_RAM));
    }

    /**
     * Test get storage size (disk)
     */
    public function testGetStorageSize()
    {
        $instance = new Instance(self::$adapter,self::$data);
        $this->assertEquals('foo',$instance->getStorageSize());
        $this->assertEquals('foo',$instance->getAttribute(Instance::INSTANCE_STORAGE));
    }

    /**
     * Test get zone
     */
    public function testGetZone()
    {
        $instance = new Instance(self::$adapter,self::$data);
        $this->assertEquals('foo',$instance->getZone());
        $this->assertEquals('foo',$instance->getAttribute(Instance::INSTANCE_ZONE));
    }

    /**
     * Test get the launch time of the instance
     */
    public function testGetLaunchTime()
    {
        $instance = new Instance(self::$adapter,self::$data);
        $this->assertEquals('foo',$instance->getLaunchTime());
        $this->assertEquals('foo',$instance->getAttribute(Instance::INSTANCE_LAUNCHTIME));
    }

    /**
     * Test reboot
     */
    public function testReboot()
    {
        $instance = new Instance(self::$adapter,self::$data);
        
        $this->assertEquals(
            self::$adapter->rebootInstance($instance::INSTANCE_ID), 
            $instance->reboot()
        );
    }

    /**
     * Test stop
     */
    public function testStop()
    {
        $instance = new Instance(self::$adapter,self::$data);
        
        $this->assertEquals(
            self::$adapter->stopInstance($instance::INSTANCE_ID), 
            $instance->stop()
        );
    }

    /**
     * Test start
     */
    public function testStart()
    {
        $instance= new Instance(self::$adapter,self::$data);
        
        $this->assertEquals(
            self::$adapter->startInstance($instance::INSTANCE_ID), 
            $instance->start()
        );
    }

    /**
     * Test destroy
     */
    public function testDestroy()
    {
        $instance = new Instance(self::$adapter, self::$data);
        
        $this->assertEquals(
            self::$adapter->destroyInstance($instance::INSTANCE_ID),
            $instance->destroy()
        );
    }

    /**
     * Test monitor
     */
    public function testMonitor()
    {
        $instance = new Instance(self::$adapter,self::$data);
        
        $this->assertEquals(
            self::$adapter->monitorInstance($instance::INSTANCE_ID, 'foo'), 
            $instance->monitor('foo')
        );
    }

    /**
     * Test deploy
     */
    public function testDeploy()
    {
        $instance = new Instance(self::$adapter,self::$data);
        
        $this->assertEquals(
            self::$adapter->deployInstance($instance::INSTANCE_ID, 'foo', 'bar'), 
            $instance->deploy('foo','bar')
        );
    }
}
