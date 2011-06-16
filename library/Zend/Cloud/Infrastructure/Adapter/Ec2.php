<?php
/**
 * @category   Zend
 * @package    Zend\Cloud\Infrastructure
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * namespace
 */
namespace Zend\Cloud\Infrastructure\Adapter;

use Zend\Service\Amazon\Ec2\Instance as Ec2Instance,
    Zend\Service\Amazon\Ec2\Image as Ec2Image,
    Zend\Service\Amazon\Ec2\AvailabilityZones as Ec2Zone,
    Zend\Service\Amazon\Ec2\CloudWatch as Ec2Monitor,
    Zend\Cloud\Infrastructure\Instance,    
    Zend\Cloud\Infrastructure\InstanceList,
    Zend\Cloud\Infrastructure\Image,
    Zend\Cloud\Infrastructure\ImageList;

/**
 * Amazon EC2 adapter for infrastructure service
 *
 * @package    Zend\Cloud\Infrastructure
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ec2 extends AbstractAdapter
{
    /**
     * AWS constants
     */
    const AWS_ACCESS_KEY = 'aws_accesskey';
    const AWS_SECRET_KEY = 'aws_secretkey';
    const AWS_REGION     = 'aws_region';

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
        'running'    => Instance::STATUS_RUNNING,
        'terminated' => Instance::STATUS_TERMINATED,
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
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options provided');
        }
        if (!isset($options[self::AWS_ACCESS_KEY]) || !isset($options[self::AWS_SECRET_KEY])) {
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
        } catch (\Zend\Service\Amazon\Ec2\Exception  $e) {
            throw new Exception\RuntimeException('Error on create: ' . $e->getMessage(), $e->getCode(), $e);
        }

        if (isset($options[self::HTTP_ADAPTER])) {
            $this->ec2->getHttpClient()->setAdapter($options[self::HTTP_ADAPTER]);
        }
        $this->options = $options;
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
            $result[Instance::INSTANCE_STATUS]     = $attr['instanceState']['name'];
            $result[Instance::INSTANCE_PUBLICDNS]  = $attr['dnsName'];
            $result[Instance::INSTANCE_IMAGEID]    = $attr['imageId'];
            $result[Instance::INSTANCE_ZONE]       = $attr['availabilityZone'];
            $result[Instance::INSTANCE_LAUNCHTIME] = $attr['launchTime'];

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
        }
        return $result;
    }

    /**
     * Return a list of the available instancies
     *
     * @return InstanceList|boolean
     */ 
    public function listInstances() 
    {
        $this->adapterResult = $this->ec2->describe();
        if (empty($this->adapterResult)) {
            return false;
        }

        $result = array();
        foreach ($this->adapterResult['instances'] as $instance) {
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
        $this->adapterResult = $this->ec2->describe($id);
        if (empty($this->adapterResult)) {
            return false;
        }    
        $result = $this->adapterResult['instances'][0];
        return $this->mapStatus[$result['instanteState']['name']];
    }

    /**
     * Return the public DNS name of the instance
     * 
     * @param  string $id
     * @return string|boolean 
     */
    public function publicDnsInstance($id) 
    {
        $this->adapterResult = $this->ec2->describe($id);
        if (empty($this->adapterResult)) {
            return false;
        }    
        $result = $this->adapterResult['instances'][0];
        return $result['dnsName'];
    }

    /**
     * Reboot one or more instances
     *
     * @param string $id
     * @return boolean
     */ 
    public function rebootInstance($id)
    {
        return $this->ec2->reboot($id);
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
        $this->adapterResult = $this->ec2->run($options);
        if (empty($this->adapterResult)) {
            return false;
        }
        return new Instance($this, $this->convertAttributes($this->adapterResult['instances'][0]));
    }

    /**
     * Stop one or more instances
     *
     * @param  string $id
     * @return boolean
     */ 
    public function stopInstance($id)
    {
        return $this->ec2->stop($id);
    }
 
    /**
     * Start one or more instances
     *
     * @param  string $id
     * @return boolean
     */ 
    public function startInstance($id)
    {
        return $this->ec2->start($id);
    }
 
    /**
     * Destroy an instance
     *
     * @param  string $id
     * @return boolean
     */ 
    public function destroyInstance($id)
    {
        return $this->ec2->terminate($id);
    }
 
    /**
     * Return a list of all the available instance images
     *
     * @return ImageList
     */ 
    public function imagesInstance()
    {
        if (!isset($this->ec2Image)) {
            $this->ec2Image = new Ec2Image($this->accessKey, $this->accessSecret, $this->region);
        }

        $this->adapterResult = $this->ec2Image->describe();
        $images              = array();

        foreach ($this->adapterResult as $result) {
            switch (strtolower($result['platform'])) {
                case 'windows' :
                    $platform = Image::IMAGE_WINDOWS;
                    break;
                default:
                    $platform = Image::IMAGE_LINUX;
                    break;
            }

            $images[]= array (
                Image::IMAGE_ID           => $result['imageId'],
                Image::IMAGE_NAME         => '',
                Image::IMAGE_DESCRIPTION  => $result['imageLocation'],
                Image::IMAGE_OWNERID      => $result['imageOwnerId'],
                Image::IMAGE_ARCHITECTURE => $result['architecture'],
                Image::IMAGE_PLATFORM     => $platform,
            );
        }
        return new ImageList($images,$this->ec2Image);
    }

    /**
     * Return all the available zones
     * 
     * @return array
     */
    public function zonesInstance()
    {
        if (!isset($this->ec2Zone)) {
            $this->ec2Zone = new Ec2Zone($this->accessKey,$this->accessSecret,$this->region);
        }
        $this->adapterResult = $this->ec2Zone->describe();
        $zones = array();
        foreach ($this->adapterResult as $zone) {
            if (strtolower($zone['zoneState'])==='available') {
                $zones[] = array (
                    Instance::ZONE_NAME => $zone['zoneName'],
                );
            }
        }
        return $zones;
    }

    /**
     * Return the system informations about the $metric of an instance
     * 
     * @param  string $id
     * @param  string $metric
     * @param  null|array $options
     * @return array|boolean
     */ 
    public function monitorInstance($id, $metric, $options = null)
    {
        if (empty($id) || empty($metric)) {
            return false;
        }
        if (!in_array($metric,$this->validMetrics)) {
            throw new Exception\InvalidArgumentException(sprintf('The metric "%s" is not valid', $metric));
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

        $this->adapterResult = $this->ec2Monitor->getMetricStatistics($param);
        $monitor             = array();
        $num                 = 0;
        $average             = 0;

        foreach ($this->adapterResult['datapoints'] as $result) {
            $monitor['series'][] = array (
                'timestamp' => $result['Timestamp'],
                'value'     => $result['Average'],
            );
            $average += $result['Average'];
            $num++;
        }

        if ($num>0) {
            $monitor['average'] = $average/$num;
        }
        return $monitor;
    }

    /**
     * Run arbitrary shell script on an instance
     *
     * @param  string $id
     * @param  array $param
     * @param  string|array $cmd
     * @return string|array
     */ 
    public function deployInstance($id, $params, $cmd)
    {
        if (!function_exists("ssh2_connect")) {
            throw new Exception\RuntimeException('Deployment requires the PHP "SSH" extension (ext/ssh2)');
        }
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('You must specify the instance where to deploy');
        }
        if (empty($cmd)) {
            throw new Exception\InvalidArgumentException('You must specify the shell commands to run on the instance');
        }
        if (empty($params) 
            || empty($params[Instance::SSH_USERNAME]) 
            || (empty($params[Instance::SSH_PASSWORD]) 
                && empty($params[Instance::SSH_KEY]))
        ) {
            throw new Exception\InvalidArgumentException('You must specify the params for the SSH connection');
        }
        $host = $this->publicDnsInstance($id);
        if (empty($host)) {
            throw new Exception\RuntimeException(sprintf(
                'The instance identified by "%s" does not exist', $id
            ));
        }
        $conn = ssh2_connect($host);
        if (!ssh2_auth_password($conn, $params[Instance::SSH_USERNAME], $params[Instance::SSH_PASSWORD])) {
            throw new Exception\RuntimeException('SSH authentication failed');
        }

        if (is_array($cmd)) {
            $result = array();
            foreach ($cmd as $command) {
                $stream = ssh2_exec($conn, $command);
                stream_set_blocking($stream, true); 
                $result[$command] = stream_get_contents($stream);
            }
        } else {
            $stream = ssh2_exec($conn, $cmd);
            $result = stream_set_blocking($stream, true); 
        }    
        return $result;
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
        return $this->ec2->getHttpClient()->getLastRequest();
    }

    /**
     * Get the last HTTP response
     * 
     * @return Zend\Http\Response 
     */
    public function getLastHttpResponse()
    {
        return $this->ec2->getHttpClient()->getLastResponse();
    }
}
