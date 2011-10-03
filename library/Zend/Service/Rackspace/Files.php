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
    Zend\Http\Client as HttpClient;

class Files extends RackspaceAbstract
{
    const ERROR_CONTAINER_NOT_EMPTY            = 'The container is not empty, I cannot delete it.';
    const ERROR_CONTAINER_NOT_FOUND            = 'The container was not found.';
    const ERROR_OBJECT_NOT_FOUND               = 'The object was not found.';
    const ERROR_OBJECT_MISSING_PARAM           = 'Missing Content-Length or Content-Type header in the request';
    const ERROR_OBJECT_CHECKSUM                = 'Checksum of the file content failed';
    const ERROR_CONTAINER_EXIST                = 'The container already exists';
    const ERROR_PARAM_NO_NAME_CONTAINER        = 'You must specify the container name';
    const ERROR_PARAM_NO_NAME_OBJECT           = 'You must specify the object name';
    const ERROR_PARAM_NO_CONTENT               = 'You must specify the content of the object';
    const ERROR_PARAM_NO_NAME_SOURCE_CONTAINER = 'You must specify the source container name';
    const ERROR_PARAM_NO_NAME_SOURCE_OBJECT    = 'You must specify the source object name';
    const ERROR_PARAM_NO_NAME_DEST_CONTAINER   = 'You must specify the destination container name';
    const ERROR_PARAM_NO_NAME_DEST_OBJECT      = 'You must specify the destination object name';
    const ERROR_PARAM_NO_METADATA              = 'You must specify the metadata array';
    const ERROR_CDN_TTL_OUT_OF_RANGE           = 'TTL must be a number in seconds, min is 900 sec and maximum is 1577836800 (50 years)';
    const ERROR_PARAM_UPDATE_CDN               = 'You must specify at least one the parameters: ttl, cdn_enabled or log_retention';
    const HEADER_CONTENT_TYPE                  = 'Content-type';
    const HEADER_HASH                          = 'Etag';
    const HEADER_LAST_MODIFIED                 = 'Last-modified';
    const HEADER_CONTENT_LENGTH                = 'Content-length';
    const HEADER_COPY_FROM                     = 'X-Copy-From';
    const METADATA_OBJECT_HEADER               = "X-Object-Meta-";
    const METADATA_CONTAINER_HEADER            = "X-Container-Meta-";
    const CDN_URI                              = "X-CDN-URI";
    const CDN_SSL_URI                          = "X-CDN-SSL-URI";
    const CDN_ENABLED                          = "X-CDN-Enabled";
    const CDN_LOG_RETENTION                    = "X-Log-Retention";
    const CDN_ACL_USER_AGENT                   = "X-User-Agent-ACL";
    const CDN_ACL_REFERRER                     = "X-Referrer-ACL";
    const CDN_TTL                              = "X-TTL";
    const CDN_TTL_MIN                          = 900;
    const CDN_TTL_MAX                          = 1577836800;
    const CDN_EMAIL                            = "X-Purge-Email";
    const ACCOUNT_CONTAINER_COUNT              = "X-Account-Container-Count";
    const ACCOUNT_BYTES_USED                   = "X-Account-Bytes-Used";
    const ACCOUNT_OBJ_COUNT                    = "X-Account-Object-Count";
    const CONTAINER_OBJ_COUNT                  = "X-Container-Object-Count";
    const CONTAINER_BYTES_USE                  = "X-Container-Bytes-Used";
    const MANIFEST_OBJECT_HEADER               = "X-Object-Manifest";

    /**
     * Return the total count of containers
     *
     * @return integer
     */
    public function getCountContainers()
    {
        $data= $this->getInfoAccount();
        return $data['tot_containers'];
    }
    /**
     * Return the size in bytes of all the containers
     *
     * @return integer
     */
    public function getSizeContainers()
    {
        $data= $this->getInfoAccount();
        return $data['size_containers'];
    }
    /**
     * Return the count of objects contained in all the containers
     *
     * @return integer
     */
    public function getCountObjects()
    {
        $data= $this->getInfoAccount();
        return $data['tot_objects'];
    }
    /**
     * Get all the containers
     *
     * @param array $options
     * @return Zend\Service\Rackspace\Files\ContainerList|boolean
     */
    public function getContainers($options=array())
    {
        $result= $this->httpCall($this->getStorageUrl(),'GET',null,$options);
        if ($result->isSuccess()) {
            return new Files\ContainerList($this,json_decode($result->getBody(),true));
        }
        return false;
    }
    /**
     * Get all the CDN containers
     *
     * @param array $options
     * @return array|boolean
     */
    public function getCdnContainers($options=array())
    {
        $options['enabled_only']= true;
        $result= $this->httpCall($this->getCdnUrl(),'GET',null,$options);
        if ($result->isSuccess()) {
            return new Files\ContainerList($this,json_decode($result->getBody(),true));
        }
        return false;
    }
    /**
     * Get the metadata information of the accounts:
     * - total count containers
     * - size in bytes of all the containers
     * - total objects in all the containers
     * 
     * @return array|boolean
     */
    public function getInfoAccount()
    {
        $result= $this->httpCall($this->getStorageUrl(),'HEAD');
        if ($result->isSuccess()) {
            $output= array(
                'tot_containers'  => (int) $result->headers()->get(self::ACCOUNT_CONTAINER_COUNT)->getFieldValue(),
                'size_containers' => (int) $result->headers()->get(self::ACCOUNT_BYTES_USED)->getFieldValue(),
                'tot_objects'     => (int) $result->headers()->get(self::ACCOUNT_OBJ_COUNT)->getFieldValue()
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container),'GET',null,$options);
        if ($result->isSuccess()) {
            return new Files\ObjectList($this,json_decode($result->getBody(),true),$container);
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $headers=array();
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $headers[self::METADATA_CONTAINER_HEADER.rawurlencode(strtolower($key))]= rawurlencode($value);
            }
        }
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container),'PUT',$headers);
        $status= $result->getStatusCode();
        switch ($status) {
            case '201': // break intentionally omitted
                $data= array(
                    'name' => $container
                );
                return new Files\Container($this,$data);
            case '202':
                $this->errorMsg= self::ERROR_CONTAINER_EXIST;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container),'DELETE');
        $status= $result->getStatusCode();
        switch ($status) {
            case '204': // break intentionally omitted
                return true;
            case '409':
                $this->errorMsg= self::ERROR_CONTAINER_NOT_EMPTY;
                break;
            case '404':
                $this->errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container),'HEAD');
        $status= $result->getStatusCode();
        switch ($status) {
            case '204': // break intentionally omitted
                $headers= $result->headers();
                $count= strlen(self::METADATA_CONTAINER_HEADER);
                $metadata= array();
                foreach ($headers as $h) {
                    $type = $h->getFieldName();
                    if (strpos($type,self::METADATA_CONTAINER_HEADER)!==false) {
                        $metadata[strtolower(substr($type, $count))]= $h->getFieldValue();
                    }
                }
                $data= array (
                    'name'     => $container,
                    'count'    => (int) $headers->get(self::CONTAINER_OBJ_COUNT)->getFieldValue(),
                    'bytes'    => (int) $headers->get(self::CONTAINER_BYTES_USE)->getFieldValue(),
                    'metadata' => $metadata
                );
                return $data;
            case '404':
                $this->errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
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
            return new Files\Container($this,$result);
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),'GET',$headers);
        $status= $result->getStatusCode();
        switch ($status) {
            case '200': // break intentionally omitted
                $data= array(
                    'name'          => $object,
                    'container'     => $container,
                    'hash'          => $result->headers()->get(self::HEADER_HASH)->getFieldValue(),
                    'bytes'         => (int) $result->headers()->get(self::HEADER_CONTENT_LENGTH)->getFieldValue(),
                    'last_modified' => $result->headers()->get(self::HEADER_LAST_MODIFIED)->getFieldValue(),
                    'content_type'  => $result->headers()->get(self::HEADER_CONTENT_TYPE)->getFieldValue(),
                    'content'       => $result->getBody()
                );
                return new Files\Object($this,$data);
            case '404':
                $this->errorMsg= self::ERROR_OBJECT_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
        return false;
    }
    /**
     * Store a file in a container 
     *
     * @param string $container
     * @param string $object
     * @param string $content
     * @param array $metadata
     * @return boolean
     */
    public function storeObject($container,$object,$content,$metadata=array()) {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        if (empty($content)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_CONTENT);
        }
        if (!empty($metadata) && is_array($metadata)) {
            foreach ($metadata as $key => $value) {
                $headers[self::METADATA_OBJECT_HEADER.$key]= $value;
            }
        }
        $headers[self::HEADER_HASH]= md5($content);
        $headers[self::HEADER_CONTENT_LENGTH]= strlen($content);
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),'PUT',$headers,null,$content);
        $status= $result->getStatusCode();
        switch ($status) {
            case '201': // break intentionally omitted
                return true;
            case '412':
                $this->errorMsg= self::ERROR_OBJECT_MISSING_PARAM;
                break;
            case '422':
                $this->errorMsg= self::ERROR_OBJECT_CHECKSUM;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),'DELETE');
        $status= $result->getStatusCode();
        switch ($status) {
            case '204': // break intentionally omitted
                return true;
            case '404':
                $this->errorMsg= self::ERROR_OBJECT_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_SOURCE_CONTAINER);
        }
        if (empty($obj_source)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_SOURCE_OBJECT);
        }
        if (empty($container_dest)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_DEST_CONTAINER);
        }
        if (empty($obj_dest)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_DEST_OBJECT);
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
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container_dest).'/'.rawurlencode($obj_dest),'PUT',$headers);
        $status= $result->getStatusCode();
        switch ($status) {
            case '201': // break intentionally omitted
                return true;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),'HEAD');
        $status= $result->getStatusCode();
        switch ($status) {
            case '200': // break intentionally omitted
                $headers= $result->headers();
                $count= strlen(self::METADATA_OBJECT_HEADER);
                $metadata= array();
                foreach ($headers as $h) {
                    $type= $h->getFieldName();
                    if (strpos($type,self::METADATA_OBJECT_HEADER)!==false) {
                        $metadata[strtolower(substr($type, $count))]= $h->getFieldValue();
                    }
                }
                $data= array (
                    'name'          => $object,
                    'container'     => $container,
                    'hash'          => $headers->get(self::HEADER_HASH)->getFieldValue(),
                    'bytes'         => (int) $headers->get(self::HEADER_CONTENT_LENGTH)->getFieldValue(),
                    'content_type'  => $headers->get(self::HEADER_CONTENT_TYPE)->getFieldValue(),
                    'last_modified' => $headers->get(self::HEADER_LAST_MODIFIED)->getFieldValue(),
                    'metadata'      => $metadata
                );
                return $data;
            case '404':
                $this->errorMsg= self::ERROR_OBJECT_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
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
    public function setMetadataObject($container,$object,$metadata)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        if (empty($metadata) || !is_array($metadata)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_OBJECT);
        }
        $headers=array();
        foreach ($metadata as $key => $value) {
            $headers[self::METADATA_OBJECT_HEADER.$key]= $value;
        }
        $result= $this->httpCall($this->getStorageUrl().'/'.rawurlencode($container).'/'.rawurlencode($object),'POST',$headers);
        $status= $result->getStatusCode();
        switch ($status) {
            case '202': // break intentionally omitted
                return true;
            case '404':
                $this->errorMsg= self::ERROR_OBJECT_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
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
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $headers=array();
        if (is_numeric($ttl) && ($ttl>=self::CDN_TTL_MIN) && ($ttl<=self::CDN_TTL_MAX)) {
            $headers[self::CDN_TTL]= $ttl;
        } else {
            throw new Exception\InvalidArgumentException(self::ERROR_CDN_TTL_OUT_OF_RANGE);
        }
        $result= $this->httpCall($this->getCdnUrl().'/'.rawurlencode($container),'PUT',$headers);
        $status= $result->getStatusCode();
        switch ($status) {
            case '201':
            case '202': // break intentionally omitted
                $data= array (
                    'cdn_uri'     => $result->headers()->get(self::CDN_URI)->getFieldValue(),
                    'cdn_uri_ssl' => $result->headers()->get(self::CDN_SSL_URI)->getFieldValue()
                );
                return $data;
            case '404':
                $this->errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
        return false;
    }
    /**
     * Update the attribute of a CDN container
     *
     * @param string $container
     * @param integer $ttl
     * @param boolean $cdn_enabled
     * @param boolean $log
     * @return boolean
     */
    public function updateCdnContainer($container,$ttl=null,$cdn_enabled=null,$log=null)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        if (empty($ttl) && (!isset($cdn_enabled)) && (!isset($log))) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_UPDATE_CDN);
        }
        $headers=array();
        if (isset($ttl)) {
            if (is_numeric($ttl) && ($ttl>=self::CDN_TTL_MIN) && ($ttl<=self::CDN_TTL_MAX)) {
                $headers[self::CDN_TTL]= $ttl;
            } else {
                throw new Exception\InvalidArgumentException(self::ERROR_CDN_TTL_OUT_OF_RANGE);
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
        $result= $this->httpCall($this->getCdnUrl().'/'.rawurlencode($container),'POST',$headers);
        $status= $result->getStatusCode();
        switch ($status) {
            case '200':
            case '202': // break intentionally omitted
                return true;
            case '404':
                $this->errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
        return false;
    }
    /**
     * Get the information of a Cdn container
     *
     * @param string $container
     * @return array|boolean
     */
    public function getInfoCdnContainer($container) {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME_CONTAINER);
        }
        $result= $this->httpCall($this->getCdnUrl().'/'.rawurlencode($container),'HEAD');
        $status= $result->getStatusCode();
        switch ($status) {
            case '204': // break intentionally omitted
                $data= array (
                    'ttl'         => (int) $result->headers()->get(self::CDN_TTL)->getFieldValue(),
                    'cdn_uri'     => $result->headers()->get(self::CDN_URI)->getFieldValue(),
                    'cdn_uri_ssl' => $result->headers()->get(self::CDN_SSL_URI)->getFieldValue()
                );
                $data['cdn_enabled']= (strtolower($result->headers()->get(self::CDN_ENABLED)->getFieldValue())!=='false');
                $data['log_retention']= (strtolower($result->headers()->get(self::CDN_LOG_RETENTION)->getFieldValue())!=='false');
                return $data;
            case '404':
                $this->errorMsg= self::ERROR_CONTAINER_NOT_FOUND;
                break;
            default:
                $this->errorMsg= $result->getBody();
                break;
        }
        $this->errorCode= $status;
        return false;
    }
}