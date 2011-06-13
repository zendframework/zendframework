<?php
/**
 * EC2 adapter for infrastructure service.
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
namespace Zend\Cloud\Infrastructure\Adapter;

use Zend\Cloud\Infrastructure\Adapter,
    Zend\Cloud\Infrastructure\Adapter\AbstractAdapter,
    Zend\Service\Amazon\Ec2\Instance as Ec2Instance,
    Zend\Cloud\Infrastructure\Instance,    
    Zend\Cloud\Infrastructure\InstanceList,        
    Zend\Cloud\Infrastructure\Exception;

class Ec2
    extends AbstractAdapter
    implements Adapter
{
    /**
     * AWS constants
     */
    const AWS_ACCESS_KEY   = 'aws_accesskey';
    const AWS_SECRET_KEY   = 'aws_secretkey';
    const AWS_REGION       = 'aws_region';
    /**
     * Ec2 service instance.
     * @var Zend\Servic\Amazon\Ec2
     */
    protected $ec2;
    /**
     * Map array between Infrastructure and EC2 params
     * 
     * @var array 
     */
    protected $map_params= array (
        Adapter::PARAM_ID => 'instanceId',
        Adapter::PARAM_STATUS => 'instanceState'
    );
    /**
     * Map array between EC2 and Infrastructure status
     * 
     * @var array 
     */
    protected $map_status= array (
        'running' => Adapter::STATUS_RUNNING,
        'terminating' => Adapter::STATUS_TERMINATE
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
        $region='';
        if (isset($options[self::AWS_REGION])) {
            $region= $options[self::AWS_REGION];
        }
        try {
            $this->ec2 = new Ec2Instance($options[self::AWS_ACCESS_KEY], $options[self::AWS_SECRET_KEY], $region);
        } catch (\Zend\Service\Amazon\Ec2\Exception  $e) {
            throw new Exception\RuntimeException('Error on create: '.$e->getMessage(), $e->getCode(), $e);
        }
        if (isset($options[self::HTTP_ADAPTER])) {
            $this->ec2->getHttpClient()->setAdapter($options[self::HTTP_ADAPTER]);
        }
        $this->options= $options;

    }
    private function convertParams($param)
    {
        if (is_array($param))
        $result=array();
        if (!empty($param) && is_array($param)) {
            $result[Adapter::PARAM_ID]= $param['instanceId'];
            $result[Adapter::PARAM_STATUS]= $param['instanceState']['name'];
        }
        return $result;
    }
    /**
     * Return a list of the available instancies
     *
     * @return array|boolean
     */ 
    public function listInstances() 
    {
        $this->adapterResult=  $this->ec2->describe();
        if (empty($this->adapterResult)) {
            return false;
        }
        $result=array();
        foreach ($this->adapterResult['instances'] as $instance) {
            $result[]= $this->convertParams($instance);
        }
        return new InstanceList($this,$result);
    }
    /**
     * Return the status of an instance
     *
     * @param string
     * @return string|boolean
     */ 
    public function statusInstance($id)
    {
        $this->adapterResult=  $this->ec2->describe($id);
        if (empty($this->adapterResult)) {
            return false;
        }    
        $result= $this->convertParams($this->adapterResult['instances'][0]);
        return $result[Adapter::PARAM_STATUS];
    }
    /**
     * Reboot one or more instances
     *
     * @param array|string $ids
     * @return boolean
     */ 
    public function rebootInstance($ids)
    {
        return $this->ec2->reboot($ids);
    }
 
    /**
     * Create a new instance
     *
     * @param string $name
     * @param array $options
     * @return boolean
     */ 
    public function createInstance($name,$options)
    {
        
    }
 
    /**
     * Stop one or more instances
     *
     * @param string $ids
     * @return boolean
     */ 
    public function stopInstance($ids)
    {
        return $this->ec2->stop($ids);
    }
 
    /**
     * Start one or more instances
     *
     * @param string $ids
     * @return boolean
     */ 
    public function startInstance($ids)
    {
        return $this->ec2->start($ids);
    }
 
    /**
     * Destroy an instance
     *
     * @param string $id
     * @return boolean
     */ 
    public function destroyInstance($id)
    {
        return $this->ec2->terminate($id);
    }
 
    /**
     * Return a list of all the available instance images
     *
     * @return array
     */ 
    public function imagesInstance()
    {
        
    }
 
    /**
     * Return the system informations about an instance
     *
     * @param string $id
     * @return array
     */ 
    public function monitorInstance($id)
    {
        
    }
 
    /**
     * Run arbitrary shell script on an instance
     *
     * @param string $id
     * @param array $cmd
     * @return array|false
     */ 
    public function deployInstance($id,$cmd)
    {
        
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
    /**
     * Get the adapter result
     * 
     * @return array 
     */
    public function getAdapterResult()
    {
        return $this->adapterResult;
    }
}
