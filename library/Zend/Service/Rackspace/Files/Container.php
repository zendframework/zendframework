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
    /**
     * @var string
     */
    private $_name;
    /**
     * Count total of object in the container
     *
     * @var integer
     */
    private $_objectCount;
    /**
     * Size in byte of the container
     *
     * @var integer
     */
    private $_size;
    /**
     * The service that has created the container object
     *
     * @var RackspaceFile
     */
    private $_service;
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
    public function __construct(RackspaceFiles $service,$data)
    {
        if (!($service instanceof RackspaceFiles) || !is_array($data)) {
             throw new InvalidArgumentException("You must pass a RackspaceFiles and an array");
        }
        if (!array_key_exists('name', $data)) {
             throw new InvalidArgumentException("You must pass the container name in the array (name)");
        }
        if (!array_key_exists('count', $data)) {
             throw new InvalidArgumentException("You must pass the object count of the container in the array (count)");
        }
        if (!array_key_exists('bytes', $data)) {
             throw new InvalidArgumentException("You must pass the byte size of the container in the array (bytes)");
        }
        $this->_service= $service;
        $this->_name= $data['name'];
        $this->_objectCount= $data['count'];
        $this->_size= $data['bytes'];
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
        return $this->_size;
    }
    /**
     * Get the total count of objects in the container
     *
     * @return integer
     */
    public function getObjectCount() {
        return $this->_objectCount;
    }

    public function getMetadata()
    {

    }
    /**
     * Get the files of the container
     *
     * @return Zend\Service\Rackspace\Files\ObjectList
     */
    public function getFiles()
    {
        return $this->_service->getFiles($this->getName());
    }
    public function addFile($file)
    {

    }
    public function deleteFile($file)
    {

    }
}