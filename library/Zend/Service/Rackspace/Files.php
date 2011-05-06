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
        Zend\Service\Rackspace\Files\ObjectList,
        Zend\Http\Client as HttpClient,
        Zend\Service\Rackspace\Exception\InvalidArgumentException;

class Files extends RackspaceAbstract
{
    const API_FORMAT= 'json';
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
     * @return ContainerList|boolean
     */
    public function getContainers($options=array())
    {
        $result= $this->_httpCall($this->getStorageUrl(),HttpClient::GET,null,$options);
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
            $this->_countObjects= $result->getHeader(self::CONTAINER_OBJ_COUNT);
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
     * Get all the files of a container
     *
     * @param string $container
     * @param array $options
     * @return  ContainerObject|boolean
     */
    public function getFiles($container,$options=array())
    {
        if (empty($container)) {
            throw new InvalidArgumentException("You must specify the container name");
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container),HttpClient::GET,null,$options);
        if ($result->isSuccessful()) {
            return new ObjectList($this,json_decode($result->getBody(),true));
        }
        return false;
    }
    /**
     * Create a container
     *
     * @param string $container
     * @param array $metadata
     * @return boolean
     */
    public function createContainer($container,$metadata=array())
    {
        if (empty($container)) {
            throw new InvalidArgumentException("You must specify the container name");
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
                    'bytes' => 0
                );
                return new Container($this,$data);
            case '202':
                $this->_errorMsg= 'The container already exists';
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $result->getStatus();
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
            throw new InvalidArgumentException("You must specify the container name");
        }
        $result= $this->_httpCall($this->getStorageUrl().'/'.rawurlencode($container),HttpClient::DELETE);
        $status= $result->getStatus();
        switch ($status) {
            case '204': // break intentionally omitted
                return true;
            case '409':
                $this->_errorMsg= 'The container is not empty, I cannot delete it.';
                break;
            case '404':
                $this->_errorMsg= 'The container was not found.';
                break;
            default:
                $this->_errorMsg= $result->getBody();
                break;
        }
        $this->_errorStatus= $result->getStatus();
        return false;
    }
    public function getMetadataContainer($container)
    {

    }
    public function getObject($container)
    {

    }
}