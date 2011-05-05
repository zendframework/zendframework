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
        Zend\Service\Rackspace\Exception\InvalidArgumentException;;

class Object
{
    protected $_name;
    protected $_hash;
    protected $_size;
    protected $_contentType;
    protected $_lastModified;

    public function getName() {
        return $this->_name;
    }
    public function getHash() {
        return $this->_hash;
    }
    public function getSize() {
        return $this->_size;
    }
    public function getContentType() {
        return $this->_contentType;
    }
    public function getLastModified() {
        return $this->_lastModified;
    }

    public function __construct(RackspaceFiles $service,$data)
    {
        if (!($service instanceof RackspaceFiles) || !is_array($data)) {
             throw new InvalidArgumentException("You must pass a RackspaceFiles and an array");
        }
        if (!array_key_exists('name', $data)) {
             throw new InvalidArgumentException("You must pass the name of the object in the array (name)");
        }
        if (!array_key_exists('hash', $data)) {
             throw new InvalidArgumentException("You must pass the hash of the object in the array (hash)");
        }
        if (!array_key_exists('bytes', $data)) {
             throw new InvalidArgumentException("You must pass the byte size of the object in the array (bytes)");
        }
        if (!array_key_exists('content_type', $data)) {
             throw new InvalidArgumentException("You must pass the content type of the object in the array (content_type)");
        }
        if (!array_key_exists('last_modified', $data)) {
             throw new InvalidArgumentException("You must pass the last modified data of the object in the array (last_modified)");
        }
        $this->_name= $data['name'];
        $this->_hash= $data['hash'];
        $this->_size= $data['bytes'];
        $this->_contentType= $data['content_type'];
        $this->_lastModified= $data['last_modified'];
        $this->_service= $service;
    }
    public function getMetadata() {

    }
}
