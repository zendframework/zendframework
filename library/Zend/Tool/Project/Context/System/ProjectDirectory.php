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
namespace Zend\Tool\Project\Context\System;
use Zend\Tool\Project\Context\System,
    Zend\Tool\Project\Context\Exception;

/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Tool\Project\Context\Filesystem\Directory
 * @uses       \Zend\Tool\Project\Context\System
 * @uses       \Zend\Tool\Project\Context\System\NotOverwritable
 * @uses       \Zend\Tool\Project\Context\System\TopLevelRestrictable
 * @uses       \Zend\Tool\Project\Exception
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ProjectDirectory
    extends \Zend\Tool\Project\Context\Filesystem\Directory
    implements System,
               NotOverwritable,
               TopLevelRestrictable
{

    /**
     * @var string
     */
    protected $_filesystemName = null;

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'ProjectDirectory';
    }

    /**
     * init()
     *
     * @return \Zend\Tool\Project\Context\System\ProjectDirectory
     */
    public function init()
    {
        // get base path from attributes (would be in path attribute)
        $projectDirectory = $this->_resource->getAttribute('path');

        // if not, get from profile
        if ($projectDirectory == null) {
            $projectDirectory = $this->_resource->getProfile()->getAttribute('projectDirectory');
        }

        // if not, exception.
        if ($projectDirectory == null) {
            throw new Exception\RuntimeException('projectDirectory cannot find the directory for this project.');
        }

        $this->_baseDirectory = rtrim($projectDirectory, '\\/');
        return $this;
    }

    /**
     * create()
     *
     * @return \Zend\Tool\Project\Context\System\ProjectDirectory
     */
    public function create()
    {
        parent::create();
        return $this;
    }

}
