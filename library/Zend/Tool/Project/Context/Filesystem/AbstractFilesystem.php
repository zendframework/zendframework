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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Project\Context\Filesystem;

use Zend\Tool\Project\Profile\Resource\Resource;

/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Tool\Project\Context
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractFilesystem implements \Zend\Tool\Project\Context\Context
{

    /**
     * @var \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected $_resource = null;

    /**
     * @var string
     */
    protected $_baseDirectory = null;

    /**
     * @var string
     */
    protected $_filesystemName = null;

    /**
     * init()
     *
     * @return \Zend\Tool\Project\Context\Filesystem\AbstractFilesystem
     */
    public function init()
    {
        $parentBaseDirectory = $this->_resource->getParentResource()->getContext()->getPath();
        $this->_baseDirectory = $parentBaseDirectory;
        return $this;
    }

    /**
     * setResource()
     *
     * @param \Zend\Tool\Project\Profile\Resource\Resource $resource
     * @return \Zend\Tool\Project\Context\Filesystem\AbstractFilesystem
     */
    public function setResource(Resource $resource)
    {
        $this->_resource = $resource;
        return $this;
    }

    /**
     * setBaseDirectory()
     *
     * @param string $baseDirectory
     * @return \Zend\Tool\Project\Context\Filesystem\AbstractFilesystem
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->_baseDirectory = rtrim(str_replace('\\', '/', $baseDirectory), '/');
        return $this;
    }

    /**
     * getBaseDirectory()
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }

    /**
     * setFilesystemName()
     *
     * @param string $filesystemName
     * @return \Zend\Tool\Project\Context\Filesystem\AbstractFilesystem
     */
    public function setFilesystemName($filesystemName)
    {
        $this->_filesystemName = $filesystemName;
        return $this;
    }

    /**
     * getFilesystemName()
     *
     * @return string
     */
    public function getFilesystemName()
    {
        return $this->_filesystemName;
    }

    /**
     * getPath()
     *
     * @return string
     */
    public function getPath()
    {
        $path = $this->_baseDirectory;
        if ($this->_filesystemName) {
            $path .= '/' . $this->_filesystemName;
        }
        return $path;
    }

    /**
     * exists()
     *
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->getPath());
    }

    /**
     * create()
     *
     * Create this resource/context
     *
     */
    abstract public function create();

    /**
     * delete()
     *
     * Delete this resouce/context
     *
     */
    abstract public function delete();

}
