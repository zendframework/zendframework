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
 * @package    Zend\Service\Rackspace
 * @subpackage Files
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Rackspace\Files;

use Zend\Service\Rackspace\Files as RackspaceFiles,
 Zend\Service\Rackspace\Exception\InvalidArgumentException;

class Container
{
    const ERROR_PARAM_CONSTRUCT= 'You must pass a RackspaceFiles and an array';
    const ERROR_PARAM_NO_NAME= 'You must pass the container name in the array (name)';
    const ERROR_PARAM_NO_TTL= 'You must pass the CDN ttl of the container in the array (ttl)';
    const ERROR_PARAM_NO_LOG_RETENTION= 'You must pass the CDN log retention of the container in the array (log_retention)';
    const ERROR_PARAM_NO_CDN_URI= 'You must pass the CDN uri of the container in the array (cdn_uri)';
    const ERROR_PARAM_NO_COUNT= 'You must pass the object count of the container in the array (count)';
    const ERROR_PARAM_NO_BYTES= 'You must pass the byte size of the container in the array (bytes)';
    /**
     * @var string
     */
    protected $_name;
    /**
     * Count total of object in the container
     *
     * @var integer
     */
    protected $_objectCount;
    /**
     * Size in byte of the container
     *
     * @var integer
     */
    protected $_size;
    /**
     * @var array
     */
    protected $_metadata = array();
    /**
     * If it's true means we called the getMetadata API
     * 
     * @var boolean
     */
    private $_getMetadata = false;
    /**
     * The service that has created the container object
     *
     * @var RackspaceFile
     */
    protected $_service;
    /**
     * CDN enabled
     * 
     * @var boolean
     */
    protected $_cdn;
    /**
     * CDN URI
     *
     * @var string
     */
    protected $_cdnUri;
    /**
     * CDN URI SSL
     *
     * @var string
     */
    protected $_cdnUriSsl;
    /**
     * TTL of the CDN container
     *
     * @var integer
     */
    protected $_ttl;
    /**
     * Log retention enabled for the CDN
     *
     * @var boolean
     */
    protected $_logRetention;
    /**
     * __construct()
     *
     * You must pass the RackspaceFiles object of the caller and an associative
     * array with the keys "name", "count", "bytes" where:
     * name= name of the container
     * count= number of objects in the container
     * bytes= size in bytes of the container
     *
     * @param RackspaceFiles $service
     * @param array $data
     */
    public function __construct(RackspaceFiles $service, $data)
    {
        if (!($service instanceof RackspaceFiles) || !is_array($data)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_CONSTRUCT);
        }
        if (!array_key_exists('name', $data)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME);
        }
        if (!empty($data['cdn_enabled'])) {
            if (!array_key_exists('ttl', $data)) {
                throw new InvalidArgumentException(self::ERROR_PARAM_NO_TTL);
            }
            if (!array_key_exists('log_retention', $data)) {
                throw new InvalidArgumentException(self::ERROR_PARAM_NO_LOG_RETENTION);
            }
            if (!array_key_exists('cdn_uri', $data)) {
                throw new InvalidArgumentException(self::ERROR_PARAM_NO_CDN_URI);
            }
        } else {
            if (!array_key_exists('count', $data)) {
                throw new InvalidArgumentException(self::ERROR_PARAM_NO_COUNT);
            }
            if (!array_key_exists('bytes', $data)) {
                throw new InvalidArgumentException(self::ERROR_PARAM_NO_BYTES);
            }
        }
        $this->_service = $service;
        $this->_name = $data['name'];
        if (!empty($data['cdn_enabled'])) {
            $this->_cdn= (strtolower($data['cdn_enabled'])!=='false');
            $this->_ttl= $data['ttl'];
            $this->_logRetention= (strtolower($data['log_retention'])!=='false');
            $this->_cdnUri= $data['cdn_uri'];
            if (!empty($data['cdn_uri_ssl'])) {
                $this->_cdnUriSsl= $data['cdn_uri_ssl'];
            }
        } else  {
            $this->_objectCount = $data['count'];
            $this->_size = $data['bytes'];
            if (!empty($data['metadata']) && is_array($data['metadata'])) {
                $this->_metadata = $data['metadata'];
                $this->_getMetadata = true;
            }
        }
    }
    /**
     * Get the name of the container
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    /**
     * Get the size in bytes of the container
     *
     * @return integer
     */
    public function getSize()
    {
        if (!isset($this->_size)) {
            $null= $this->getMetadata();
        }
        return $this->_size;
    }
    /**
     * Get the total count of objects in the container
     *
     * @return integer
     */
    public function getObjectCount()
    {
        if (!isset($this->_size)) {
            $null= $this->getMetadata();
        }
        return $this->_objectCount;
    }
    /**
     * Return true if the container is CDN enabled
     * 
     * @return boolean
     */
    public function isCdnEnabled()
    {
        if (!isset($this->_cdn)) {
            $this->updateCdnInfo();
        }
        return $this->_cdn;
    }
    /**
     * Get the TTL of the CDN
     * 
     * @return integer 
     */
    public function getCdnTtl() {
        if (!isset($this->_ttl)) {
            $this->updateCdnInfo();
        }
        return $this->_ttl;
    }
    /**
     * Return true if the log retention is enabled for the CDN
     *
     * @return boolean
     */
    public function isCdnLogEnabled()
    {
        if (!isset($this->_logRetention)) {
            $this->updateCdnInfo();
        }
        return $this->_logRetention;
    }
    /**
     * Get the CDN URI
     * 
     * @return string
     */
    public function getCdnUri()
    {
        if (!isset($this->_cdnUri)) {
            $this->updateCdnInfo();
        }
        return $this->_cdnUri;
    }
    /**
     * Get the CDN URI SSL
     *
     * @return string
     */
    public function getCdnUriSsl()
    {
        if (!isset($this->_cdnUriSsl)) {
            $this->updateCdnInfo();
        }
        return $this->_cdnUriSsl;
    }
    /**
     * Get the metadata of the container
     *
     * If $key is empty return the array of metadata
     *
     * @param string $key
     * @return array|string
     */
    public function getMetadata($key=null)
    {
        if (empty($this->_metadata) && (!$this->_getMetadata)) {
            $result = $this->_service->getMetadataContainer($this->getName());
            if (!empty($result)) {
                $this->_objectCount = $result['tot_objects'];
                $this->_size = $result['size'];
                if (!empty($result['metadata']) && is_array($result['metadata'])) {
                    $this->_metadata = $result['metadata'];
                }
            }
            $this->_getMetadata = true;
        }
        if (!empty($this->_metadata[$key])) {
            return $this->_metadata[$key];
        }
        return $this->_metadata;
    }
    /**
     * Get all the object of the container
     *
     * @return Zend\Service\Rackspace\Files\ObjectList
     */
    public function getObjects()
    {
        return $this->_service->getObjects($this->getName());
    }
    /**
     * Get an object of the container
     * 
     * @param string $name
     * @param array $headers
     * @return Zend\Service\Rackspace\Files\Object|boolean
     */
    public function getObject($name, $headers=array())
    {
        return $this->_service->getObject($this->getName(), $name, $headers);
    }
    /**
     * Add an object in the container
     *
     * @param string $name
     * @param string $file the content of the object
     * @param array $metadata
     * @return boolen
     */
    public function addObject($name, $file, $metadata=array())
    {
        return $this->_service->storeObject($this->getName(), $name, $file, $metadata);
    }
    /**
     * Delete an object in the container
     *
     * @param string $obj
     * @return boolean
     */
    public function deleteObject($obj)
    {
        return $this->_service->deleteObject($this->getName(), $obj);
    }
    /**
     * Copy an object to another container
     *
     * @param string $obj_source
     * @param string $container_dest
     * @param string $obj_dest
     * @param array $metadata
     * @param string $content_type
     * @return boolean
     */
    public function copyObject($obj_source, $container_dest, $obj_dest, $metadata=array(), $content_type=null)
    {
        return $this->_service->copyObject($this->getName(), $obj_source, $container_dest, $obj_dest, $metadata, $content_type);
    }
    /**
     * Get the metadata of an object in the container
     *
     * @param string $object
     * @return array
     */
    public function getMetadataObject($object)
    {
        return $this->_service->getMetadataObject($this->getName(),$object);
    }
    /**
     * Set the metadata of an object in the container
     *
     * @param string $object
     * @param array $metadata
     * @return boolean
     */
    public function setMetadataObject($object,$metadata=array()) {
        return $this->_service->setMetadataObject($this->getName(),$object,$metadata);
    }
    /**
     * Enable the CDN for the container
     *
     * @param integer $ttl
     * @return boolean
     */
    public function enableCdn($ttl=RackspaceFiles::CDN_TTL_MIN) {
        $result= $this->_service->enableCdnContainer($this->getName(),$ttl);
        if ($result!==false) {
           $this->_cdn= true;
           $this->_ttl= $ttl;
           $this->_logRetention= true;
           $this->_cdnUri= $result['cdn_uri'];
           $this->_cdnUriSsl= $result['cdn_uri_ssl'];
           return true;
        }
        return false;
    }
    /**
     * Disable the CDN for the container
     * 
     * @return boolean
     */
    public function disableCdn() {
        $result=  $this->_service->updateCdnContainer($this->getName(),null,false);
        if ($result!==false) {
            $this->_cdn= false;
            $this->_resetParamsCdn();
            return true;
        }
        return false;
    }
    /**
     * Change the TTL for the CDN container
     *
     * @param integer $ttl
     * @return boolean
     */
    public function changeTtlCdn($ttl) {
        $result=  $this->_service->updateCdnContainer($this->getName(),$ttl);
        if ($result!==false) {
            $this->_ttl= $ttl;
            return true;
        }
        return false;
    }
    /**
     * Enable the log retention for the CDN
     *
     * @return boolean
     */
    public function enableLogCdn() {
        $result=  $this->_service->updateCdnContainer($this->getName(),null,null,true);
        if ($result!==false) {
            $this->_logRetention= true;
            return true;
        }
        return false;
    }
    /**
     * Disable the log retention for the CDN
     *
     * @return boolean
     */
    public function disableLogCdn() {
        $result=  $this->_service->updateCdnContainer($this->getName(),null,null,false);
        if ($result!==false) {
             $this->_logRetention= false;
            return true;
        }
        return false;
    }
    /**
     * Update the CDN information
     *
     * @return boolean
     */
    public function updateCdnInfo() {
        $result=  $this->_service->getInfoCdn($this->getName());
        if ($result!==false) {
            $this->_cdn= (strtolower($result['cdn_enabled'])!=='false');
            $this->_ttl= $result['ttl'];
            $this->_logRetention= (strtolower($result['log_retention'])!=='false');
            $this->_cdnUri= $result['cdn_uri'];
            $this->_cdnUriSsl= $result['cdn_uri_ssl'];
            return true;
        }
        return false;
    }
    /**
     * Reset all the parameters related to the CDN container
     */
    private function _resetParamsCdn() {
        $this->_ttl= null;
        $this->_logRetention= null;
        $this->_cdnUri= null;
        $this->_cdnUriSsl= null;
    }
}