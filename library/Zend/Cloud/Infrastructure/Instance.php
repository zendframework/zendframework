<?php
/**
 * Instance of an infrastructure service
 *
 * @category   Zend
 * @package    Zend\Cloud
 * @subpackage Infrastructure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * namespace
 */
namespace Zend\Cloud\Infrastructure;

use Zend\Cloud\Infrastructure\Exception,
    Zend\Cloud\Infrastructure\Adapter;

class Instance 
{
    const STATUS_RUNNING = 'running'; 
    const STATUS_STOPPED = 'stopped'; 
    const STATUS_SHUTTING_DOWN= 'shutting-down'; 
    const STATUS_REBOOTING= 'rebooting';
    const STATUS_TERMINATED= 'terminated';
    const INSTANCE_ID= 'id';
    const INSTANCE_IMAGEID= 'imageId';
    const INSTANCE_NAME= 'name';
    const INSTANCE_STATUS= 'status';
    const INSTANCE_PUBLICDNS= 'publicDns';
    const INSTANCE_CPU= 'cpu';
    const INSTANCE_RAM= 'ram';
    const INSTANCE_STORAGE= 'storageSize';
    const INSTANCE_ZONE= 'zone';
    const INSTANCE_LAUNCHTIME= 'launchTime';
    const ZONE_NAME= 'zone';
    const MONITOR_CPU= 'CPU';
    const MONITOR_NETWORK_IN= 'NetworkIn';
    const MONITOR_NETWORK_OUT= 'NetworkOut';
    const MONITOR_DISK_WRITE= 'DiskWrite';
    const MONITOR_DISK_READ= 'DiskRead';
    const MONITOR_START_TIME= 'StartTime';
    const MONITOR_END_TIME= 'EndTime';
    const SSH_USERNAME= 'user';
    const SSH_PASSWORD= 'password';
    const SSH_KEY= 'pritaveKey';
    /**
     * @var Zend\Cloud\Infrastructure\Adapter
     */
    protected $adapter;
    /**
     * Instance's attribute
     * 
     * @var array 
     */
    protected $attributes;
    /**
     * 
     * @var type 
     */
    protected $attributeRequired= array ( self::INSTANCE_ID, self::INSTANCE_STATUS );
    /**
     * __construct
     * 
     * @param array $data 
     */
    public function __construct(Adapter $adapter,$data)
    {
        if (!($adapter instanceof Adapter)) {
            throw new Exception\InvalidArgumentException("You must pass a Zend\Cloud\Infrastructure\Adapter instance");
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException ("You must pass a array of params");
        } else {
            foreach ($this->attributeRequired as $key) {
                if (empty($data[$key])) {
                    throw new Exception\InvalidArgumentException ("The param $key is a required param for Zend\Cloud\Infrastructure\Instance");
                }
            }
        } 
        $this->adapter= $adapter;
        $this->attributes= $data;
    }
    /**
     * Get Attribute with a specific key
     *
     * @param array $data
     * @return misc|boolean
     */
    public function getAttribute($key) {
        if (!empty($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        return false;
    }
    /**
     * Get all the attributes
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    /**
     * Get the instance's id
     * 
     * @return string 
     */
    public function getId()
    {
        return $this->attributes[self::INSTANCE_ID];
    }
    /**
     * Get the instances' image id
     * 
     * @return string 
     */
    public function getImageId()
    {
        return $this->attributes[self::INSTANCE_IMAGEID];
    }
    /**
     * Get the instance's name
     * 
     * @return string 
     */
    public function getName()
    {
        return $this->attributes[self::INSTANCE_NAME];
    }
    /**
     * Get the status of the infrastructure
     * 
     * @return string|boolean 
     */
    public function getStatus()
    {
        if (!empty($this->attributes[self::INSTANCE_STATUS])) {
            return $this->attributes[self::INSTANCE_STATUS];
        }
        return false;
    }
    /**
     * Get the instance's CPU
     * 
     * @return string
     */
    public function getCpu()
    {
        return $this->attributes[self::INSTANCE_CPU];
    }
    /**
     * Get the instance's RAM size
     * 
     * @return string
     */
    public function getRam()
    {
        return $this->attributes[self::INSTANCE_RAM];
    }
    /**
     * Get the instance's storage size
     * 
     * @return string
     */
    public function getStorageSize()
    {
        return $this->attributes[self::INSTANCE_STORAGE];
    }
    /**
     * Get the instance's zone
     * 
     * @return string 
     */
    public function getZone()
    {
        return $this->attributes[self::INSTANCE_ZONE];
    }
    /**
     * Get the instance's launch time
     * 
     * @return string
     */
    public function getLaunchTime()
    {
        return $this->attributes[self::INSTANCE_LAUNCHTIME];
    }
    /**
     * Reboot the instance
     * 
     * @return boolean 
     */
    public function reboot()
    {
        return $this->adapter->rebootInstance($this->attributes[self::INSTANCE_ID]);
    }
    /**
     * Stop the instance
     * 
     * @return boolean 
     */
    public function stop()
    {
        return $this->adapter->stopInstance($this->attributes[self::INSTANCE_ID]);
    }
    /**
     * Start the instance
     * 
     * @return boolean 
     */
    public function start()
    {
        return $this->adapter->startInstance($this->attributes[self::INSTANCE_ID]);
    }
    /**
     * Destroy the instance
     * 
     * @return boolean 
     */
    public function destroy()
    {
        return $this->adapter->destroyInstance($this->attributes[self::INSTANCE_ID]);
    }
}