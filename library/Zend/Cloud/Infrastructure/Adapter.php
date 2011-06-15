<?php
/**
 * Adapter interface for infrastructure service
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

interface Adapter 
{ 
    const HTTP_ADAPTER = 'http_adapter'; 
    /**
     * Return a list of the available instances
     *
     * @return array
     */ 
    public function listInstances(); 
 
    /**
     * Return the status of an instance
     *
     * @param strin $id
     * @return string
     */ 
    public function statusInstance($id); 
 
    /**
     * Return the public DNS name of the instance
     * 
     * @param string $id
     * @return string|boolean 
     */
    public function publicDnsInstance($id);
    
    /**
     * Reboot an instance
     *
     * @param string $id
     * @return boolean
     */ 
    public function rebootInstance($id); 
 
    /**
     * Create a new instance
     *
     * @param string $name
     * @param array $options
     * @return boolean
     */ 
    public function createInstance($name,$options); 
 
    /**
     * Stop the execution of an instance
     *
     * @param string $id
     * @return boolean
     */ 
    public function stopInstance($id); 
 
    /**
     * Start the execution of an instance
     *
     * @param string $id
     * @return boolean
     */ 
    public function startInstance($id); 
 
    /**
     * Destroy an instance
     *
     * @param string $id
     * @return boolean
     */ 
    public function destroyInstance($id); 
 
    /**
     * Return all the available instances images
     *
     * @return array
     */ 
    public function imagesInstance(); 
    
    /**
     * Return all the available zones
     */
    public function zonesInstance();
    
    /**
     * Return the system informations about the $metric of an instance
     *
     * @param string $id
     * @param string $metric
     * @param array $options
     * @return array
     */ 
    public function monitorInstance($id,$metric,$options=null); 
 
    /**
     * Run arbitrary shell script on an instance
     *
     * @param string $id
     * @param array $param
     * @param string|array $cmd
     * @return string|array
     */ 
    public function deployInstance($id,$param,$cmd);
            
    /**
     * Get the adapter instance
     * 
     * @return object
     */
    public function getAdapter();
    
    /**
     * Get the adapter result
     * 
     * @return array
     */
    public function getAdapterResult();
    
    /**
     * Get the last HTTP response
     * 
     * @return Zend\Http\Response
     */
    public function getLastHttpResponse();
    
    /**
     * Ge the last HTTP request
     * 
     * @return string
     */
    public function getLastHttpRequest();
} 
