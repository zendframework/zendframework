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
 * @package    Zend\Service
 * @subpackage Rackspace
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Rackspace;

use Zend\Service\Rackspace\Rackspace as RackspaceAbstract,
        Zend\Service\Rackspace\Files\Container,
        Zend\Service\Rackspace\Files\ContainerList,
        Zend\Service\Rackspace\Files\Object,
        Zend\Service\Rackspace\Files\ObjectList,
        Zend\Http\Client as HttpClient,
        Zend\Service\Rackspace\Exception\InvalidArgumentException;

class Files extends RackspaceAbstract
{
    const ERROR_CONTAINER_NOT_EMPTY= 'The container is not empty, I cannot delete it.';
    const ERROR_CONTAINER_NOT_FOUND= 'The container was not found.';
    const ERROR_OBJECT_NOT_FOUND= 'The object was not found.';
    const ERROR_OBJECT_MISSING_PARAM= 'Missing Content-Length or Content-Type header in the request';
    const ERROR_OBJECT_CHECKSUM= 'Checksum of the file content failed';
    const ERROR_CONTAINER_EXIST= 'The container already exists';
    const ERROR_PARAM_NO_NAME_CONTAINER= 'You must specify the container name';
    const ERROR_PARAM_NO_NAME_OBJECT= 'You must specify the object name';
    const ERROR_PARAM_NO_FILE= 'You must specify the content of the file';
    const ERROR_PARAM_NO_NAME_SOURCE_CONTAINER= 'You must specify the source container name';
    const ERROR_PARAM_NO_NAME_SOURCE_OBJECT= 'You must specify the source object name';
    const ERROR_PARAM_NO_NAME_DEST_CONTAINER= 'You must specify the destination container name';
    const ERROR_PARAM_NO_NAME_DEST_OBJECT= 'You must specify the destination object name';
    const ERROR_CDN_TTL_OUT_OF_RANGE= 'TTL must be a number in seconds, min is 900 sec and maximum is 1577836800 (50 years)';
    const ERROR_PARAM_UPDATE_CDN= 'You must specify at least one the parameters: ttl, cdn_enabled or log_retention';
    const HEADER_CONTENT_TYPE= 'Content-type';
    const HEADER_HASH= 'Etag';
    const HEADER_LAST_MODIFIED= 'Last-modified';
    const HEADER_CONTENT_LENGTH= 'Content-length';
    const HEADER_COPY_FROM= 'X-Copy-From';
    const METADATA_OBJECT_HEADER= "X-object-meta-";
    const METADATA_CONTAINER_HEADER= "X-container-meta-";
    const CDN_URI= "X-CDN-URI";
    const CDN_SSL_URI= "X-CDN-SSL-URI";
    const CDN_ENABLED= "X-CDN-Enabled";
    const CDN_LOG_RETENTION= "X-Log-Retention";
    const CDN_ACL_USER_AGENT= "X-User-Agent-ACL";
    const CDN_ACL_REFERRER= "X-Referrer-ACL";
    const CDN_TTL= "X-TTL";
    const CDN_TTL_MIN= 900;
    const CDN_TTL_MAX= 1577836800;
    const CDN_EMAIL= "X-Purge-Email";
    const ACCOUNT_CONTAINER_COUNT= "X-account-container-count";
    const ACCOUNT_BYTES_USED= "X-account-bytes-used";
    const ACCOUNT_OBJ_COUNT= "X-account-object-count";
    const CONTAINER_OBJ_COUNT= "X-container-object-count";
    const CONTAINER_BYTES_USE= "X-container-bytes-used";
    const MANIFEST_OBJECT_HEADER= "X-Object-Manifest";
    /**
     * @var integer
     */
    protected $_countContainers;
    /**
     * @var integer
     */
    protected $_sizeContainers;
    /**
     * @var integer
     */
    protected $_countObjects;
    /**
     * Return the total count of containers
     *
     * @return integer
     */
    public function getCountContainers()
    {
        if (!isset($this->_countContainers)) {
            $this->getInfoContainers();
        }
        return $this->_countContainers;
    }
    /**
     * Return the size in bytes of all the containers
     *
     * @return integer
     */
    public function getSizeContainers()
    {
         if (!isset($this->_sizeContainers)) {
             $this->getInfoContainers();
         }
        return $this->_sizeContainers;
    }
    /**
     * Return the count of objects contained in all the containers
     *
     * @return integer
     */
    public function getCountObjects()
    {
        if (!isset($this->_countObjects)) {
            $this->getInfoContainers();
        }
        return $this->_countObjects;
    }
    /**
     * Get all the containers
     *
     * @param array $options
     * @return Zend\Service\Rackspace\Files\ContainerList|boolean
     */
    public function getContainers($options=array())
    {
        $result= $this->_httpCall($this->getStorageUrl(),HttpClient::GET,null,$options);
        if ($result->isSuccessful()) {
            $this->_countContainers= $result->getHeader(self::ACCOUNT_CONTAINER_COUNT);
            $this->_sizeContainers= $result->getHeader(self::ACCOUNT_BYTES_USED);
            $this->_countObjects= $result->getHeader(self::ACCOUNT_OBJ_COUNT);
            return new ContainerList($this,json_decode($result->getBody(),true));
        }
        return false;
    }
    /**
     * Get all the CDN containers
     *
     * @param array $options
     * @return Zend\Service\Rackspace\Files\ContainerList|boolean
     */
    public function getCdnContainers($options=array())
    {
        $options['enabled_only']= true;
        $result= $this->_httpCall($this->getCdnUrl(),HttpClient::GET,null,$options);
         if ($result->isSuccessful()) {
            return new ContainerList($this,json_decode($result->getBody(),true));
        }
        return false;
    }
    /**
     * Get the metadata information of the accounts:
     * - total count containers
     * - size in bytes of all the containers
     * - total count objects in all the containers
     *
     * @return array|boolean
     */
    public function getInfoContainers()
    {
        $result= $this->_httpCall($this->getStorageUrl(),HttpClient::HEAD);
        if ($result->isSuccessful()) {
            $this->_countContainers= $result->getHeader(self::ACCOUNT_CONTAINER_COUNT);
            $this->_sizeContainers= $result->getHeader(self::ACCOUNT_BYTES_USED);
            $this->_countObjects= $result->getHeader(self::ACCOUNT_OBJ_COUNT);
            $output= array(
                'tot_containers' => $this->_countContainers,
                'size_containers' => $this->_sizeContainers,
                'tot_objects' => $this->_countObjects
            );
            return $output;
        }
        return false;
    }
    /**
     * Get all the objects of a container
     *
     * @param string $container
     * @param array $options
     * @return  Zend\Service\Rackspace\Files\ObjectList|boolean
     */
    public function getObjects($container,$options=array())
    {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container),HttpClient::GET,null,$options);
        if ($result->isSuccessful()) {
            return new ObjectList($this,json_decode($result->getBody(),true),$container);
        }
        return false;
    }
    /**
     * Create a container
     *
     * @param string $container
     * @param array $metadata
     * @return Zend\Service\Rackspace\Files\Container|boolean
     */
    public function createContainer($container,$metadata=array())
    {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $headers=array();
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $headers[self::METADATA_CONTAINER_HEADER.rawurlencode($key)]= rawurlencode($value);
            }
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container),HttpClient::PUT,$headers);
        $status= $result->getStatus();
        switch ($status) {
            case '201': // break intentionally omitted
                $data= array(
                    'name' => $container,
                    'count' => 0,
                    'bytes' => 0,
                    'metadata' => $metadata
                );
                return new Container($this,$data);
            case '202':
                $this->_errorMsg= self::ERROR_CONTAINER_EXIST;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Delete a container (only if it's empty)
     *
     * @param sting $container
     * @return boolean
     */
    public function deleteContainer($container)
    {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container),HttpClient::DELETE);
        $status= $result->getStatus();
        switch ($status) {
            case '204': // break intentionally omitted
                return true;
            case '409':
                $this->_errorMsg= self::ERROR_CONTAINER_NOT_EMPTY;
                break;
            case '404':
                $this->_errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Get the metadata of a container
     *
     * @param string $container
     * @return array|boolean
     */
    public function getMetadataContainer($container)
    {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container),HttpClient::HEAD);
        $status= $result->getStatus();
        switch ($status) {
            case '204': // break intentionally omitted
                $headers= $result->getHeaders();
                $count= strlen(self::METADATA_CONTAINER_HEADER);
                $metadata= array();
                foreach ($headers as $key => $value) {
                    if (strpos($key,self::METADATA_CONTAINER_HEADER)!==false) {
                        $metadata[substr($key, $count)]= $value;
                    }
                }
                $data= array (
                    'name' => $container,
                    'count' => $headers[self::CONTAINER_OBJ_COUNT],
                    'bytes' => $headers[self::CONTAINER_BYTES_USE],
                    'metadata' => $metadata
                );
                return $data;
            case '404':
                $this->_errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Get a container
     * 
     * @param string $container
     * @return Container|boolean
     */
    public function getContainer($container) {
        $result= $this->getMetadataContainer($container);
        if (!empty($result)) {
            return new Container($this,$result);
        }
        return false;
    }
    /**
     * Get an object in a container
     *
     * @param string $container
     * @param string $object
     * @param array $headers
     * @return Zend\Service\Rackspace\Files\Object|boolean
     */
    public function getObject($container,$object,$headers=array())
    {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),HttpClient::GET);
        $status= $result->getStatus();
        switch ($status) {
            case '200': // break intentionally omitted
                $data= array(
                    'name' => $object,
                    'container' => $container,
                    'hash' => $result->getHeader(self::HEADER_HASH),
                    'bytes' => $result->getHeader(self::HEADER_CONTENT_LENGTH),
                    'last_modified' => $result->getHeader(self::HEADER_LAST_MODIFIED),
                    'content_type' => $result->getHeader(self::HEADER_CONTENT_TYPE),
                    'file' => $result->getBody()
                );
                return new Object($this,$data);
            case '404':
                $this->_errorMsg= self::ERROR_OBJECT_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Store an object in a container
     *
     * @param string $container
     * @param string $object
     * @param string $file
     * @param array $metadata
     * @return boolean
     */
    public function storeObject($container,$object,$file,$metadata=array()) {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        if (empty($file)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_FILE);
        }
        if (!empty($metadata) && is_array($metadata)) {
            foreach ($metadata as $key => $value) {
                $headers[self::METADATA_OBJECT_HEADER.$key]= $value;
            }
        }
        $headers[self::HEADER_HASH]= md5($file);
        $headers[self::HEADER_CONTENT_LENGTH]= strlen($file);
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),HttpClient::PUT,$headers,null,$file);
        $status= $result->getStatus();
        switch ($status) {
            case '201': // break intentionally omitted
                return true;
            case '412':
                $this->_errorMsg= self::ERROR_OBJECT_MISSING_PARAM;
                break;
            case '422':
                $this->_errorMsg= self::ERROR_OBJECT_CHECKSUM;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Delete an object in a container
     *
     * @param string $container
     * @param string $object
     * @return boolean
     */
    public function deleteObject($container,$object) {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),HttpClient::DELETE);
        $status= $result->getStatus();
        switch ($status) {
            case '204': // break intentionally omitted
                return true;
            case '404':
                $this->_errorMsg= self::ERROR_OBJECT_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Copy an object from a container to another
     *
     * @param string $container_source
     * @param string $obj_source
     * @param string $container_dest
     * @param string $obj_dest
     * @param array $metadata
     * @param string $content_type
     * @return boolean
     */
    public function copyObject($container_source,$obj_source,$container_dest,$obj_dest,$metadata=array(),$content_type=null) {
        if (empty($container_source)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_SOURCE_CONTAINER);
        }
        if (empty($obj_source)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_SOURCE_OBJECT);
        }
        if (empty($container_dest)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_DEST_CONTAINER);
        }
        if (empty($obj_dest)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_DEST_OBJECT);
        }
        $headers= array(
            self::HEADER_COPY_FROM => '/'.rawurlencode($container_source).'/'.rawurlencode($obj_source),
            self::HEADER_CONTENT_LENGTH => 0
        );
        if (!empty($content_type)) {
            $headers[self::HEADER_CONTENT_TYPE]= $content_type;
        }
        if (!empty($metadata) && is_array($metadata)) {
            foreach ($metadata as $key => $value) {
                $headers[self::METADATA_OBJECT_HEADER.$key]= $value;
            }
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container_dest).'/'.rawurlencode($obj_dest),HttpClient::PUT,$headers);
        $status= $result->getStatus();
        var_dump($status);
        switch ($status) {
            case '201': // break intentionally omitted
                return true;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Get the metadata of an object
     *
     * @param string $container
     * @param string $object
     * @return array|boolean
     */
    public function getMetadataObject($container,$object) {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),HttpClient::HEAD);
        $status= $result->getStatus();
        switch ($status) {
            case '200': // break intentionally omitted
                $headers= $result->getHeaders();
                $count= strlen(self::METADATA_OBJECT_HEADER);
                $metadata= array();
                foreach ($headers as $key => $value) {
                    if (strpos($key,self::METADATA_OBJECT_HEADER)!==false) {
                        $metadata[substr($key, $count)]= $value;
                    }
                }
                $data= array (
                    'name' => $object,
                    'container' => $container,
                    'hash' => $headers[self::HEADER_HASH],
                    'bytes' => $headers[self::HEADER_CONTENT_LENGTH],
                    'content_type' => $headers[self::HEADER_CONTENT_TYPE],
                    'last_modified' => $headers[self::HEADER_LAST_MODIFIED],
                    'metadata' => $metadata
                );
                return $data;
            case '404':
                $this->_errorMsg= self::ERROR_OBJECT_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Set the metadata of a object in a container
     * The old metadata values are replaced with the new one
     * 
     * @param string $container
     * @param string $object
     * @param array $metadata
     * @return boolean
     */
    public function setMetadataObject($container,$object,$metadata=array())
    {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        $headers=array();
        if (!empty($metadata) && is_array($metadata)) {
            foreach ($metadata as $key => $value) {
                $headers[self::METADATA_OBJECT_HEADER.$key]= $value;
            }
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),HttpClient::POST,$headers);
        $status= $result->getStatus();
        switch ($status) {
            case '202': // break intentionally omitted
                return true;
            case '404':
                $this->_errorMsg= self::ERROR_OBJECT_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Enable the CDN for a container
     *
     * @param string $container
     * @param integer $ttl
     * @return array|boolean
     */
    public function enableCdnContainer ($container,$ttl=self::CDN_TTL_MIN) {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $headers=array();
        if (is_numeric($ttl) && ($ttl>=self::CDN_TTL_MIN) && ($ttl<=self::CDN_TTL_MAX)) {
            $headers[self::CDN_TTL]= $ttl;
        } else {
            throw new InvalidArgumentException(self::ERROR_CDN_TTL_OUT_OF_RANGE);
        }
        $result= $this->_httpCall($this->getCdnUrl().'/'.rawurlencode($container),HttpClient::PUT,$headers);
        $status= $result->getStatus();
        switch ($status) {
            case '201': // break intentionally omitted
                $data= array (
                    'cdn_uri' => $result->getHeader(self::CDN_URI),
                    'cdn_uri_ssl' => $result->getHeader(self::CDN_SSL_URI)
                );
                return $data;
            case '404':
                $this->_errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Update the attribute of a CDN container
     *
     * @param string $container
     * @param integer $ttl
     * @param boolean $cdn_enabled
     * @param boolean $log
     * @return array|boolean
     */
    public function updateCdnContainer($container,$ttl=null,$cdn_enabled=null,$log=null)
    {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($ttl) && (!isset($cdn_enabled)) && (!isset($log))) {
            throw new InvalidArgumentException(self::ERROR_PARAM_UPDATE_CDN);
        }
        $headers=array();
        if (isset($ttl)) {
            if (is_numeric($ttl) && ($ttl>=self::CDN_TTL_MIN) && ($ttl<=self::CDN_TTL_MAX)) {
                $headers[self::CDN_TTL]= $ttl;
            } else {
                throw new InvalidArgumentException(self::ERROR_CDN_TTL_OUT_OF_RANGE);
            }
        }
        if (isset($cdn_enabled)) {
            if ($cdn_enabled===true) {
                $headers[self::CDN_ENABLED]= 'true';
            } else {
                $headers[self::CDN_ENABLED]= 'false';
            }
        }
        if (isset($log)) {
            if ($log===true) {
                $headers[self::CDN_LOG_RETENTION]= 'true';
            } else  {
                $headers[self::CDN_LOG_RETENTION]= 'false';
            }
        }
        $result= $this->_httpCall($this->getCdnUrl().'/'.rawurlencode($container),HttpClient::POST,$headers);
        $status= $result->getStatus();
        switch ($status) {
            case '202': // break intentionally omitted
                $data= array (
                    'cdn_uri' => $result->getHeader(self::CDN_URI),
                    'cdn_uri_ssl' => $result->getHeader(self::CDN_SSL_URI)
                );
                return $data;
            case '404':
                $this->_errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
    /**
     * Get the information about a Cdn container
     *
     * @param string $container
     * @return array|boolean
     */
    public function getInfoCdn($container) {
        if (empty($container)) {
            throw new InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $result= $this->_httpCall($this->getCdnUrl().'/'.rawurlencode($container),HttpClient::HEAD);
        $status= $result->getStatus();
        switch ($status) {
            case '204': // break intentionally omitted
                $data= array (
                    'ttl' =>  $result->getHeader(self::CDN_TTL),
                    'cdn_uri' => $result->getHeader(self::CDN_URI),
                    'cdn_uri_ssl' => $result->getHeader(self::CDN_SSL_URI)
                );
                $data['cdn_enabled']= (strtolower($result->getHeader(self::CDN_ENABLED))!=='false');
                $data['log_retention']= (strtolower($result->getHeader(self::CDN_LOG_RETENTION))!=='false');
                return $data;
            case '404':
                $this->_errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $status;
        return false;
    }
}