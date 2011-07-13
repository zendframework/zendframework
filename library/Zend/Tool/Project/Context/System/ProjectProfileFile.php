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
use Zend\Tool\Project\Context\System;

/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Tool\Project\Context\Filesystem\File
 * @uses       \Zend\Tool\Project\Context\System
 * @uses       \Zend\Tool\Project\Context\System\NotOverwritable
 * @uses       \Zend\Tool\Project\Profile\FileParser\Xml
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ProjectProfileFile
    extends \Zend\Tool\Project\Context\Filesystem\File
    implements System,
               NotOverwritable
{

    /**
     * @var string
     */
    protected $_filesystemName = '.zfproject.xml';

    /**
     * @var \Zend\Tool\Project\Profile\Profile
     */
    protected $_profile = null;
    
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'ProjectProfileFile';
    }
    
    /**
     * setProfile()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @return \Zend\Tool\Project\Context\System\ProjectProfileFile
     */
    public function setProfile($profile)
    {
        $this->_profile = $profile;
        return $this;
    }

    /**
     * save()
     *
     * Proxy to create
     *
     * @return \Zend\Tool\Project\Context\System\ProjectProfileFile
     */
    public function save()
    {
        parent::create();
        return $this;
    }

    /**
     * getContents()
     *
     * @return string
     */
    public function getContents()
    {
        $parser = new \Zend\Tool\Project\Profile\FileParser\Xml();
        $profile = $this->_resource->getProfile();
        $xml = $parser->serialize($profile);
        return $xml;
    }

}
