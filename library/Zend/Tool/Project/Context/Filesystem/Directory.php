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
 * @uses       \Zend\Tool\Project\Context\Filesystem\AbstractFilesystem
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Directory extends AbstractFilesystem
{

    /**
     * getName()
     * 
     * @return string
     */
    public function getName()
    {
        return 'directory';
    }
    
    /**
     * create()
     *
     * @return \Zend\Tool\Project\Context\Filesystem\Directory;
     */
    public function create()
    {
        // check to ensure the parent exists, if not, call it and create it
        if (($parentResource = $this->_resource->getParentResource()) instanceof Resource) {
            if ((($parentContext = $parentResource->getContext()) instanceof AbstractFilesystem)
                && (!$parentContext->exists())) {
                $parentResource->create();
            }
        }

        if (!file_exists($this->getPath())) {
            mkdir($this->getPath());
        }

        return $this;
    }

    /**
     * delete()
     *
     * @return \Zend\Tool\Project\Context\Filesystem\Directory
     */
    public function delete()
    {
        $this->_resource->setDeleted(true);
        rmdir($this->getPath());

        return $this;
    }

}
