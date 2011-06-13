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
    protected $attributeRequired= array ( Adapter::PARAM_ID, Adapter::PARAM_STATUS );
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
                    throw new Exception\InvalidArgumentException ("The param $key is not present in the array of Zend\Cloud\Infrastructure\Instance");
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
     * Get the status of the infrastructure
     * 
     * @return string|boolean 
     */
    public function getStatus()
    {
        if (!empty($this->attributes[Adapter::PARAM_STATUS])) {
            return $this->attributes[Adapter::PARAM_STATUS];
        }
        return false;
    }
    /**
     * Reboot the instance
     * 
     * @return boolean 
     */
    public function reboot()
    {
        return $this->adapter->rebootInstance($this->attributes[Adapter::PARAM_ID]);
    }
}