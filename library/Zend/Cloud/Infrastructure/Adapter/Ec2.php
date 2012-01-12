<?php
/**
 * @category   Zend
 * @package    Zend\Cloud\Infrastructure
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cloud\Infrastructure\Adapter;

use Zend\Service\Amazon\Ec2\Instance as Ec2Instance,
    Zend\Service\Amazon\Ec2\Image as Ec2Image,
    Zend\Service\Amazon\Ec2\AvailabilityZones as Ec2Zone,
    Zend\Service\Amazon\Ec2\CloudWatch as Ec2Monitor,
    Zend\Service\Amazon\Ec2\Exception as Ec2Exception,
    Zend\Cloud\Infrastructure\Instance,    
    Zend\Cloud\Infrastructure\InstanceList,
    Zend\Cloud\Infrastructure\Image,
    Zend\Cloud\Infrastructure\ImageList;

/**
 * Amazon EC2 adapter for infrastructure service
 *
 * @package    Zend\Cloud\Infrastructure
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ec2 extends AbstractAdapter
{
    /**
     * AWS constants
     */
    const AWS_ACCESS_KEY     = 'aws_accesskey';
    const AWS_SECRET_KEY     = 'aws_secretkey';
    const AWS_REGION         = 'aws_region';
    const AWS_SECURITY_GROUP = 'securityGroup';

    /**
     * Ec2 Instance 
     * 
     * @var Ec2Instance
     */
    protected $ec2;

    /**
     * Ec2 Image
     * 
     * @var Ec2Image
     */
    protected $ec2Image;

    /**
     * Ec2 Zone
     * 
     * @var Ec2Zone
     */
    protected $ec2Zone;

    /**
     * Ec2 Monitor 
     * 
     * @var Ec2Monitor
     */
    protected $ec2Monitor;

    /**
     * AWS Access Key
     * 
     * @var string 
     */
    protected $accessKey;

    /**
     * AWS Access Secret
     * 
     * @var string 
     */
    protected $accessSecret;

    /**
     * Region zone 
     * 
     * @var string 
     */
    protected $region;

    /**
     * Map array between EC2 and Infrastructure status
     * 
     * @var array 
     */
    protected $mapStatus = array (
        'running'       => Instance::STATUS_RUNNING,
        'terminated'    => Instance::STATUS_TERMINATED,
        'pending'       => Instance::STATUS_PENDING,
        'shutting-down' => Instance::STATUS_SHUTTING_DOWN,
        'stopping'      => Instance::STATUS_PENDING,
        'stopped'       => Instance::STATUS_STOPPED,
        'rebooting'     => Instance::STATUS_REBOOTING,
    );

    /**
     * Map monitor metrics between Infrastructure and EC2
     * 
     * @var array 
     */
    protected $mapMetrics= array (
        Instance::MONITOR_CPU         => 'CPUUtilization',
        Instance::MONITOR_DISK_READ   => 'DiskReadBytes',
        Instance::MONITOR_DISK_WRITE  => 'DiskWriteBytes',
        Instance::MONITOR_NETWORK_IN  => 'NetworkIn',
        Instance::MONITOR_NETWORK_OUT => 'NetworkOut',
    );
    
    /**
     * Constructor
     *
     * @param  array|Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        if (is_object($options)) {
            if (method_exists($options, 'toArray')) {
                $options= $options->toArray();
            } elseif ($options instanceof \Traversable) {
                $options = iterator_to_array($options);
            }
        }
        
        if (empty($options) || !is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options provided');
        }
        
        if (!isset($options[self::AWS_ACCESS_KEY]) 
            || !isset($options[self::AWS_SECRET_KEY])
        ) {
            throw new Exception\InvalidArgumentException('AWS keys not specified!');
        }

        $this->accessKey    = $options[self::AWS_ACCESS_KEY];
        $this->accessSecret = $options[self::AWS_SECRET_KEY];
        $this->region       = '';

        if (isset($options[self::AWS_REGION])) {
            $this->region= $options[self::AWS_REGION];
        }

        try {
            $this->ec2 = new Ec2Instance($options[self::AWS_ACCESS_KEY], $options[self::AWS_SECRET_KEY], $this->region);
        } catch (Ec2Exception $e) {
            throw new Exception\RuntimeException('Error on create: ' . $e->getMessage(), $e->getCode(), $e);
        }

        if (isset($options[self::HTTP_ADAPTER])) {
            $this->ec2->getHttpClient()->setAdapter($options[self::HTTP_ADAPTER]);
        }
    }

    /**
     * Convert the attributes of EC2 into attributes of Infrastructure
     * 
     * @param  array $attr
     * @return array|boolean 
     */
    private function convertAttributes($attr)
    {
        $result = array();       
        if (!empty($attr) && is_array($attr)) {
            $result[Instance::INSTANCE_ID]         = $attr['instanceId'];
            unset ($attr['instanceId']);
            $result[Instance::INSTANCE_STATUS]     = $this->mapStatus[$attr['instanceState']['name']];
            unset ($attr['instanceState']['name']);
            $result[Instance::INSTANCE_IMAGEID]    = $attr['imageId'];
            unset ($attr['imageId']);
            $result[Instance::INSTANCE_ZONE]       = $attr['availabilityZone'];
            unset ($attr['availabilityZone']);
            $result[Instance::INSTANCE_LAUNCHTIME] = $attr['launchTime'];
            unset ($attr['launchTime']);
            
            switch ($attr['instanceType']) {
                case Ec2Instance::MICRO:
                    $result[Instance::INSTANCE_CPU]     = '1 virtual core';
                    $result[Instance::INSTANCE_RAM]     = '613MB';
                    $result[Instance::INSTANCE_STORAGE] = '0GB';
                    break;
                case Ec2Instance::SMALL:
                    $result[Instance::INSTANCE_CPU]     = '1 virtual core';
                    $result[Instance::INSTANCE_RAM]     = '1.7GB';
                    $result[Instance::INSTANCE_STORAGE] = '160GB';
                    break;
                case Ec2Instance::LARGE:
                    $result[Instance::INSTANCE_CPU]     = '2 virtual core';
                    $result[Instance::INSTANCE_RAM]     = '7.5GB';
                    $result[Instance::INSTANCE_STORAGE] = '850GB';
                    break;
                case Ec2Instance::XLARGE:
                    $result[Instance::INSTANCE_CPU]     = '4 virtual core';
                    $result[Instance::INSTANCE_RAM]     = '15GB';
                    $result[Instance::INSTANCE_STORAGE] = '1690GB';
                    break;
                case Ec2Instance::HCPU_MEDIUM:
                    $result[Instance::INSTANCE_CPU]     = '2 virtual core';
                    $result[Instance::INSTANCE_RAM]     = '1.7GB';
                    $result[Instance::INSTANCE_STORAGE] = '350GB';
                    break;
                case Ec2Instance::HCPU_XLARGE:
                    $result[Instance::INSTANCE_CPU]     = '8 virtual core';
                    $result[Instance::INSTANCE_RAM]     = '7GB';
                    $result[Instance::INSTANCE_STORAGE] = '1690GB';
                    break;
            }
            unset ($attr['instanceType']);
            $result = array_merge($attr, $result);
        }
        return $result;
    }

    /**
     * Return the list of available instances
     *
     * @return boolean|InstanceList
     */ 
    public function listInstances() 
    {
        $this->resetError();
        try {
            $this->adapterResult = $this->ec2->describe();
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }    
        $result = array();
        foreach ($this->adapterResult['instances'] as $instance) {
            $result[]= $this->convertAttributes($instance);
        }
        if (empty($result)) {
            return null;
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
        try {
            $this->adapterResult = $this->ec2->describe($id);
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }       
        $result = $this->adapterResult['instances'][0];
        return $this->mapStatus[$result['instanceState']['name']];
    }

    /**
     * Return the public DNS name of the instance
     * 
     * @param  string $id
     * @return string|boolean 
     */
    public function publicDnsInstance($id) 
    {
        $this->resetError();
        try {
            $this->adapterResult = $this->ec2->describe($id);
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }
        $result = $this->adapterResult['instances'][0];
        return $result['dnsName'];
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
        try {
            $this->adapterResult = $this->ec2->reboot($id);
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }
        return $this->adapterResult;
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
        $this->resetError();
        try {
            $this->adapterResult = $this->ec2->run($options);
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }
        return new Instance($this, $this->convertAttributes($this->adapterResult['instances'][0]));
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
        try {
            $this->adapterResult = $this->ec2->terminate($id);
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }
        return true;
    }
 
    /**
     * Return a list of all the available instance images
     *
     * @return boolean|ImageList
     */ 
    public function imagesInstance()
    {
        if (!isset($this->ec2Image)) {
            $this->ec2Image = new Ec2Image($this->accessKey, $this->accessSecret, $this->region);
            $this->ec2Image->setHttpClient($this->ec2->getHttpClient());
        }
        
        $this->resetError();
        try {
            $this->adapterResult = $this->ec2Image->describe();
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }
        
        $images = array();
        $i = 0;
        foreach ($this->adapterResult as $result) {
            switch (strtolower($result['platform'])) {
                case 'windows' :
                    $platform = Image::IMAGE_WINDOWS;
                    break;
                default:
                    $platform = Image::IMAGE_LINUX;
                    break;
            }

            $images[$i] = array (
                Image::IMAGE_ID           => $result['imageId'],
                Image::IMAGE_NAME         => '',
                Image::IMAGE_DESCRIPTION  => $result['imageLocation'],
                Image::IMAGE_OWNERID      => $result['imageOwnerId'],
                Image::IMAGE_ARCHITECTURE => $result['architecture'],
                Image::IMAGE_PLATFORM     => $platform,
            );
            unset($result['imageId']);
            unset($result['imageLocation']);
            unset($result['imageOwnerId']);
            unset($result['architecture']);
            unset($result['platform']);
            $images[$i] = array_merge($result, $images[$i]);
            $i++;
        }
        if (empty($images)) {
            return null;
        }
        return new ImageList($images,$this->ec2Image);
    }

    /**
     * Return all the available zones
     * 
     * @return boolean|array
     */
    public function zonesInstance()
    {
        if (!isset($this->ec2Zone)) {
            $this->ec2Zone = new Ec2Zone($this->accessKey,$this->accessSecret,$this->region);
            $this->ec2Zone->setHttpClient($this->ec2->getHttpClient());
        }
        
        $this->resetError();
        try {
            $this->adapterResult = $this->ec2Zone->describe();
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }

        $zones = array();
        foreach ($this->adapterResult as $zone) {
            if (strtolower($zone['zoneState']) === 'available') {
                $zones[] = array (
                    Instance::INSTANCE_ZONE => $zone['zoneName'],
                );
            }
        }
        return $zones;
    }

    /**
     * Return the system information about the $metric of an instance
     * 
     * @param  string $id
     * @param  string $metric
     * @param  null|array $options
     * @return boolean|array
     */ 
    public function monitorInstance($id, $metric, $options = null)
    {
        if (empty($id) || empty($metric)) {
            return false;
        }

        if (!in_array($metric,$this->validMetrics)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The metric "%s" is not valid', 
                $metric
            ));
        }

        if (!empty($options) && !is_array($options)) {
            throw new Exception\InvalidArgumentException('The options must be an array');
        }

        if (!empty($options) 
            && (empty($options[Instance::MONITOR_START_TIME]) 
                || empty($options[Instance::MONITOR_END_TIME]))
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The options array must contain: "%s" and "%s"',
                $options[Instance::MONITOR_START_TIME],
                $options[Instance::MONITOR_END_TIME]
            ));
        }      

        if (!isset($this->ec2Monitor)) {
            $this->ec2Monitor = new Ec2Monitor($this->accessKey, $this->accessSecret, $this->region);
            $this->ec2Monitor->setHttpClient($this->ec2->getHttpClient());
        }

        $param = array(
            'MeasureName' => $this->mapMetrics[$metric],
            'Statistics'  => array('Average'),
            'Dimensions'  => array('InstanceId' => $id),
        );

        if (!empty($options)) {
            $param['StartTime'] = $options[Instance::MONITOR_START_TIME];
            $param['EndTime']   = $options[Instance::MONITOR_END_TIME];
        }

        $this->resetError();
        try {
            $this->adapterResult = $this->ec2Monitor->getMetricStatistics($param);
        } catch (Ec2Exception\RunTimeException $e) {
            $this->setError($e);
            return false;
        }

        $monitor             = array();
        $num                 = 0;
        $average             = 0;

        if (!empty($this->adapterResult['datapoints'])) {
            foreach ($this->adapterResult['datapoints'] as $result) {
                $monitor['series'][] = array (
                    'timestamp' => $result['Timestamp'],
                    'value'     => $result['Average'],
                );
                $average += $result['Average'];
                $num++;
            }
        }

        if ($num > 0) {
            $monitor['average'] = $average / $num;
        }

        return $monitor;
    }

    /**
     * Get the adapter 
     * 
     * @return Ec2Instance 
     */
    public function getAdapter()
    {
        return $this->ec2;
    }

    /**
     * Get last HTTP request
     * 
     * @return string 
     */
    public function getLastHttpRequest()
    {
        return $this->ec2->getHttpClient()->getLastRawRequest();
    }

    /**
     * Get the last HTTP response
     * 
     * @return Zend\Http\Response 
     */
    public function getLastHttpResponse()
    {
        return $this->ec2->getHttpClient()->getResponse()->toString();
    }
    
    /**
     * Set the error message and error code from Ec2Exception\RunTimeException
     * 
     * @return void
     */
    protected function setError(Ec2Exception\RunTimeException $e)
    {
        $msg = explode(' ',$e->getMessage(),2);
        $this->errorCode = $msg[0];
        $this->errorMsg = $msg[1];
    }
}
