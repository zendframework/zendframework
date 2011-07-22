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
 * @uses       \Zend\Tool\Project\Context\Filesystem\Directory
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ApplicationDirectory 
    extends \Zend\Tool\Project\Context\Filesystem\Directory
{

    protected $_filesystemName = 'application';

    protected $_classNamePrefix = 'Application\\';
    
    public function init()
    {
        if ($this->_resource->hasAttribute('classNamePrefix')) {
            $this->_classNamePrefix = $this->_resource->getAttribute('classNamePrefix');
        }
        parent::init();
    }
    
    /**
     * getPersistentAttributes
     *
     * @return array
     */
    public function getPersistentAttributes()
    {
        return array(
            'classNamePrefix' => $this->getClassNamePrefix()
            );
    }
    
    public function getName()
    {
        return 'ApplicationDirectory';
    }
    
    public function setClassNamePrefix($classNamePrefix)
    {
        $this->_classNamePrefix = $classNamePrefix;
    }
    
    public function getClassNamePrefix()
    {
        return $this->_classNamePrefix;
    }

}
