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
namespace Zend\Tool\Project\Context\Zf;

/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Filter\FilterChain
 * @uses       \Zend\Filter\StringToLower
 * @uses       \Zend\Filter\Word\CamelCaseToDash
 * @uses       \Zend\Tool\Project\Context\Filesystem\Directory
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewControllerScriptsDirectory extends \Zend\Tool\Project\Context\Filesystem\Directory
{

    /**
     * @var string
     */
    protected $_filesystemName = 'controllerName';

    /**
     * @var name
     */
    protected $_forControllerName = null;

    /**
     * init()
     *
     * @return \Zend\Tool\Project\Context\Zf\ViewControllerScriptsDirectory
     */
    public function init()
    {
        $this->_forControllerName = $this->_resource->getAttribute('forControllerName');
        $this->_filesystemName = $this->_convertControllerNameToFilesystemName($this->_forControllerName);
        parent::init();
        return $this;
    }

    /**
     * getPersistentAttributes()
     *
     * @return array
     */
    public function getPersistentAttributes()
    {
        return array(
            'forControllerName' => $this->_forControllerName
            );
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'ViewControllerScriptsDirectory';
    }
    
    protected function _convertControllerNameToFilesystemName($controllerName)
    {
        $filter = new \Zend\Filter\FilterChain();
        $filter->attach(new \Zend\Filter\Word\CamelCaseToDash())
               ->attach(new \Zend\Filter\StringToLower());
        return $filter->filter($controllerName);
    }

}
