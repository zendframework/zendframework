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

use Zend\Tool\Project\Profile\Resource\Resource;

/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Tool\Project\Context\Filesystem\File
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractClassFile 
    extends \Zend\Tool\Project\Context\Filesystem\File
{
    
    /**
     * getFullClassName()
     * 
     * @param $localClassName
     * @param $classContextName
     */
    public function getFullClassName($localClassName, $classContextName = null)
    {

        // find the ApplicationDirectory OR ModuleDirectory
        $currentResource = $this->_resource;
        do {
            $resourceName = $currentResource->getName();
            if ($resourceName == 'ApplicationDirectory' || $resourceName == 'ModuleDirectory') {
                $containingResource = $currentResource;
                break;
            }
        } while ($currentResource instanceof Resource
            && $currentResource = $currentResource->getParentResource());
        
        $fullClassName = '';

        // go find the proper prefix
        if (isset($containingResource)) {
            if ($containingResource->getName() == 'ApplicationDirectory') {
                $prefix = $containingResource->getAttribute('classNamePrefix');
                $fullClassName = $prefix;
            } elseif ($containingResource->getName() == 'ModuleDirectory') {
                $prefix = $containingResource->getAttribute('moduleName') . '\\';
                $fullClassName = $prefix;    
            }
        }

        if ($classContextName) {
            $fullClassName .= rtrim($classContextName, '\\') . '\\';
        }
        $fullClassName .= $localClassName;

        return $fullClassName;
    }

}
