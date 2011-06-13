<?php
namespace Zend\Cloud\Infrastructure;

interface Adapter 
{ 
    // HTTP adapter to use for connections 
    const HTTP_ADAPTER = 'http_adapter'; 
    const STATUS_RUNNING = 'running'; 
    const STATUS_STOPPED = 'stopped'; 
    const STATUS_SHUTTING_DOWN= 'shutting-down'; 
    const STATUS_TERMINATE= 'terminate';
    const PARAM_ID= 'id';
    const PARAM_NAME= 'name';
    const PARAM_STATUS= 'status';
    const PARAM_CPU= 'cpu';
    const PARAM_RAM= 'ram';
 
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
     * Return a list of all the available instances images
     *
     * @return array
     */ 
    public function imagesInstance(); 
 
    /**
     * Return the system informations about an instance
     *
     * @param string $id
     * @return array
     */ 
    public function monitorInstance($id); 
 
    /**
     * Run arbitrary shell script on an instance
     *
     * @param string $id
     * @param array $cmd
     * @return array|false
     */ 
    public function deployInstance($id,$cmd); 
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
