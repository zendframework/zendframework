<?php
/**
 * @category   Zend
 * @package    Zend_Cloud_Infrastructure
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cloud\Infrastructure\Adapter;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Service\Rackspace\Servers as RackspaceServers,
    Zend\Cloud\Infrastructure\Instance,
    Zend\Cloud\Infrastructure\InstanceList,
    Zend\Cloud\Infrastructure\Image,
    Zend\Cloud\Infrastructure\ImageList;

/**
 * Rackspace servers adapter for infrastructure service
 *
 * @package    Zend_Cloud_Infrastructure
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Rackspace extends AbstractAdapter
{
    /**
     * RACKSPACE constants
     */
    const RACKSPACE_USER      = 'rackspace_user';
    const RACKSPACE_KEY       = 'rackspace_key';
    const RACKSPACE_REGION    = 'rackspace_region';
    const RACKSPACE_ZONE_USA  = 'USA';
    const RACKSPACE_ZONE_UK   = 'UK';
    const MONITOR_CPU_SAMPLES = 3;
    /**
     * Rackspace Servers Instance 
     * 
     * @var RackspaceServers
     */
    protected $rackspace;
    /**
     * Rackspace access user
     * 
     * @var string 
     */
    protected $accessUser;

    /**
     * Rackspace access key
     * 
     * @var string 
     */
    protected $accessKey;
    /**
     * Rackspace Region
     * 
     * @var string 
     */
    protected $region;
    /**
     * Flavors
     * 
     * @var array 
     */
    protected $flavors;
    /**
     * Map array between Rackspace and Infrastructure status
     * 
     * @var array 
     */
    protected $mapStatus = array (
        'ACTIVE'             => Instance::STATUS_RUNNING,
        'SUSPENDED'          => Instance::STATUS_STOPPED,
        'BUILD'              => Instance::STATUS_REBUILD,
        'REBUILD'            => Instance::STATUS_REBUILD,
        'QUEUE_RESIZE'       => Instance::STATUS_PENDING,
        'PREP_RESIZE'        => Instance::STATUS_PENDING,
        'RESIZE'             => Instance::STATUS_REBUILD,
        'VERIFY_RESIZE'      => Instance::STATUS_REBUILD,
        'PASSWORD'           => Instance::STATUS_PENDING,
        'RESCUE'             => Instance::STATUS_PENDING,
        'REBOOT'             => Instance::STATUS_REBOOTING,
        'HARD_REBOOT'        => Instance::STATUS_REBOOTING,
        'SHARE_IP'           => Instance::STATUS_PENDING,
        'SHARE_IP_NO_CONFIG' => Instance::STATUS_PENDING,
        'DELETE_IP'          => Instance::STATUS_PENDING,
        'UNKNOWN'            => Instance::STATUS_PENDING
    );
    /**
     * Constructor
     *
     * @param  array|Traversable $options
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (empty($options) || !is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options provided');
        }
        
        if (!isset($options[self::RACKSPACE_USER])) {
            throw new Exception\InvalidArgumentException('Rackspace access user not specified!');
        }

        if (!isset($options[self::RACKSPACE_KEY])) {
            throw new Exception\InvalidArgumentException('Rackspace access key not specified!');
        }
        
        $this->accessUser = $options[self::RACKSPACE_USER];
        $this->accessKey  = $options[self::RACKSPACE_KEY];
        
        if (isset($options[self::RACKSPACE_REGION])) {
            switch ($options[self::RACKSPACE_REGION]) {
                case self::RACKSPACE_ZONE_UK:
                    $this->region= RackspaceServers::UK_AUTH_URL;
                    break;
                case self::RACKSPACE_ZONE_USA:
                    $this->region = RackspaceServers::US_AUTH_URL;
                    break;
                default:
                    throw new Exception\InvalidArgumentException('The region is not valid');
            }
        } else {
            $this->region = RackspaceServers::US_AUTH_URL;
        }

        try {
            $this->rackspace = new RackspaceServers($this->accessUser,$this->accessKey, $this->region);
        } catch (\Zend\Service\Rackspace\Exception  $e) {
            throw new Exception\RuntimeException('Error on create: ' . $e->getMessage(), $e->getCode(), $e);
        }

        if (isset($options[self::HTTP_ADAPTER])) {
            $this->rackspace->getHttpClient()->setAdapter($options[self::HTTP_ADAPTER]);
        }
    }
    /**
     * Convert the attributes of Rackspace server into attributes of Infrastructure
     * 
     * @param  array $attr
     * @return array|boolean 
     */
    protected function convertAttributes($attr)
    {
        $result = array();       
        if (!empty($attr) && is_array($attr)) {
            $result[Instance::INSTANCE_ID]      = $attr['id'];
            unset($attr['id']);
            $result[Instance::INSTANCE_NAME]    = $attr['name'];
            unset($attr['name']);
            $result[Instance::INSTANCE_STATUS]  = $this->mapStatus[$attr['status']];
            unset($attr['status']);
            $result[Instance::INSTANCE_IMAGEID] = $attr['imageId'];
            unset($attr['imageId']);
            if ($this->region==RackspaceServers::US_AUTH_URL) {
                $result[Instance::INSTANCE_ZONE] = self::RACKSPACE_ZONE_USA;
            } else {
                $result[Instance::INSTANCE_ZONE] = self::RACKSPACE_ZONE_UK;
            }
            if (empty($this->flavors)) {
                $this->flavors = $this->rackspace->listFlavors(true);
            }
            if (!empty($this->flavors)) {
                $result[Instance::INSTANCE_RAM]     = $this->flavors[$attr['flavorId']]['ram'];
                $result[Instance::INSTANCE_STORAGE] = $this->flavors[$attr['flavorId']]['disk'];
            }    
            unset($attr['flavorId']);
            $result[Instance::INSTANCE_PUBLICDNS] = $attr['addresses']['public'][0];
            $result = array_merge($attr,$result);
        }
        return $result;
    }
    /**
     * Return a list of the available instances
     *
     * @return InstanceList|boolean
     */ 
    public function listInstances() 
    {
        $this->resetError();
        $this->adapterResult = $this->rackspace->listServers(true);
        if ($this->adapterResult===false) {
            $this->setError();
            return false;
        }
        $array= $this->adapterResult->toArray();
        $result = array();
        foreach ($array as $instance) {
            $result[]= $this->convertAttributes($instance);
        }
        return new InstanceList($this, $result);
    }
    /**
     * Return the status of an instance
     *
     * @param  string
     * @return string|boolean
     */ 
    public function statusInstance($id)
    {
        $this->resetError();
        $this->adapterResult = $this->rackspace->getServer($id);
        if ($this->adapterResult===false) {
            $this->setError();
            return false;
        }
        $array= $this->adapterResult->toArray();
        return $this->mapStatus[$array['status']];
    }
    /**
     * Return the public DNS name/Ip address of the instance
     * 
     * @param  string $id
     * @return string|boolean 
     */
    public function publicDnsInstance($id) 
    {
        $this->resetError();
        $this->adapterResult = $this->rackspace->getServerPublicIp($id);
        if (empty($this->adapterResult)) {
            $this->setError();
            return false;
        }  
        return $this->adapterResult[0];
    }
    /**
     * Reboot an instance
     *
     * @param string $id
     * @return boolean
     */ 
    public function rebootInstance($id)
    {
        $this->resetError();
        $result = $this->rackspace->rebootServer($id,true);
        if ($result===false) {
            $this->setError();
        }
        return $result;
    }
    /**
     * Create a new instance
     *
     * @param string $name
     * @param array $options
     * @return Instance|boolean
     */ 
    public function createInstance($name, $options)
    {
        if (empty($name)) {
            throw new Exception\InvalidArgumentException('You must specify the name of the instance');
        }
        if (empty($options) || !is_array($options)) {
            throw new Exception\InvalidArgumentException('The options must be an array');
        }
        $this->resetError();
        // @todo create an generic abstract definition for an instance?
        $metadata = array();
        if (isset($options['metadata'])) {
            $metadata = $options['metadata'];
            unset($options['metadata']);
        }
        $files = array();
        if (isset($options['files'])) {
            $files = $options['files'];
            unset($options['files']);
        }
        $options['name'] = $name;
        $this->adapterResult = $this->rackspace->createServer($options,$metadata,$files);
        if ($this->adapterResult===false) {
            $this->setError();
            return false;
        }
        return new Instance($this, $this->convertAttributes($this->adapterResult->toArray()));
    }
    /**
     * Stop an instance
     *
     * @param  string $id
     * @return boolean
     */ 
    public function stopInstance($id)
    {
        throw new Exception\RuntimeException('The stopInstance method is not implemented in the adapter');
    }
 
    /**
     * Start an instance
     *
     * @param  string $id
     * @return boolean
     */ 
    public function startInstance($id)
    {
        throw new Exception\RuntimeException('The startInstance method is not implemented in the adapter');
    }
 
    /**
     * Destroy an instance
     *
     * @param  string $id
     * @return boolean
     */ 
    public function destroyInstance($id)
    {
        $this->resetError();
        $this->adapterResult= $this->rackspace->deleteServer($id);
        if ($this->adapterResult===false) {
            $this->setError();
        }
        return $this->adapterResult;
    }
    /**
     * Return a list of all the available instance images
     *
     * @return ImageList|boolean
     */ 
    public function imagesInstance()
    {
        $this->resetError();
        $this->adapterResult = $this->rackspace->listImages(true);
        if ($this->adapterResult===false) {
            $this->setError();
            return false;
        }
        
        $images= $this->adapterResult->toArray();
        $result= array();
        $i=0;
        foreach ($images as $image) {
            if (strtolower($image['status'])==='active') {
                if (strpos($image['name'],'Windows')!==false) {
                    $platform = Image::IMAGE_WINDOWS;
                } else {
                    $platform = Image::IMAGE_LINUX;
                }
                if (strpos($image['name'],'x64')!==false) {
                    $arch = Image::ARCH_64BIT;
                } else {
                    $arch = Image::ARCH_32BIT;
                }
                $result[$i]= array (
                    Image::IMAGE_ID           => $image['id'],
                    Image::IMAGE_NAME         => $image['name'],
                    Image::IMAGE_DESCRIPTION  => $image['name'],
                    Image::IMAGE_ARCHITECTURE => $arch,
                    Image::IMAGE_PLATFORM     => $platform,
                );
                unset($image['id']);
                unset($image['name']);
                $result[$i] = array_merge($image, $result[$i]);
                $i++;
            }
        }
        return new ImageList($result,$this->adapterResult);
    }
    /**
     * Return all the available zones
     * 
     * @return array
     */
    public function zonesInstance()
    {
        return array(self::RACKSPACE_ZONE_USA,self::RACKSPACE_ZONE_UK);
    }
    /**
     * Return the system information about the $metric of an instance
     * NOTE: it works only for Linux servers
     * 
     * @param  string $id
     * @param  string $metric
     * @param  null|array $options
     * @return array|boolean
     */ 
    public function monitorInstance($id, $metric, $options = null)
    {
        if (!function_exists("ssh2_connect")) {
            throw new Exception\RuntimeException('Monitor requires the PHP "SSH" extension (ext/ssh2)');
        }
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('You must specify the id of the instance to monitor');
        }
        if (empty($metric)) {
            throw new Exception\InvalidArgumentException('You must specify the metric to monitor');
        }
        if (!in_array($metric,$this->validMetrics)) {
            throw new Exception\InvalidArgumentException(sprintf('The metric "%s" is not valid', $metric));
        }
        if (!empty($options) && !is_array($options)) {
            throw new Exception\InvalidArgumentException('The options must be an array');
        }
        
        switch ($metric) {
            case Instance::MONITOR_CPU:
                $cmd= 'top -b -n '.self::MONITOR_CPU_SAMPLES.' | grep \'Cpu\'';
                break;
            case Instance::MONITOR_RAM:
                $cmd= 'top -b -n 1 | grep \'Mem\'';
                break;
            case Instance::MONITOR_DISK:
                $cmd= 'df --total | grep total';
                break;
        }
        if (empty($cmd)) {
            throw new Exception\InvalidArgumentException('The metric specified is not supported by the adapter');
        }
        
        $params= array(
            Instance::SSH_USERNAME => $options['username'],
            Instance::SSH_PASSWORD => $options['password']
        );
        $exec_time= time();
        try {
            $result= $this->deployInstance($id,$params,$cmd);
        } catch (Exception\RuntimeException $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }

        $monitor = array();
        $num     = 0;
        $average = 0;

        $outputs= explode("\n",$result);
        foreach ($outputs as $output) {
            if (!empty($output)) {
                switch ($metric) {
                    case Instance::MONITOR_CPU:
                        if (preg_match('/(\d+\.\d)%us/', $output,$match)) {
                            $usage = (float) $match[1];
                        }
                        break;
                    case Instance::MONITOR_RAM:
                        if (preg_match('/(\d+)k total/', $output,$match)) {
                            $total = (integer) $match[1];
                        }
                        if (preg_match('/(\d+)k used/', $output,$match)) {
                            $used = (integer) $match[1];
                        }
                        if ($total>0) {
                            $usage= (float) $used/$total;
                        }    
                        break;
                    case Instance::MONITOR_DISK:
                        if (preg_match('/(\d+)%/', $output,$match)) {
                            $usage = (float) $match[1];
                        }
                        break;
                }
                
                $monitor['series'][] = array (
                    'timestamp' => $exec_time,
                    'value'     => number_format($usage,2)
                );
                
                $average += $usage;
                $exec_time+= 60; // seconds
                $num++;
            }
        }
        
        if ($num>0) {
            $monitor['average'] = number_format($average/$num,2);
        }
        return $monitor;
    }
    /**
     * Get the adapter 
     * 
     * @return \Zend\Service\Rackspace\Servers
     */
    public function getAdapter()
    {
        return $this->rackspace;
    }
    /**
     * Get last HTTP request
     * 
     * @return string 
     */
    public function getLastHttpRequest()
    {
        return $this->rackspace->getHttpClient()->getLastRawRequest();
    }
    /**
     * Get the last HTTP response
     * 
     * @return \Zend\Http\Response
     */
    public function getLastHttpResponse()
    {
        return $this->rackspace->getHttpClient()->getResponse()->toString();
    }
    /**
     * Set the error message and code
     * 
     * @return void
     */
    protected function setError()
    {
        $this->errorMsg  = $this->rackspace->getErrorMsg();
        $this->errorCode = $this->rackspace->getErrorCode();
    }
}
