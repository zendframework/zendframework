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
 * @uses       \Zend\Code\Generator\ClassGenerator
 * @uses       \Zend\Code\Generator\FileGenerator
 * @uses       \Zend\Tool\Project\Context\Zf\AbstractClassFile
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ModelFile extends AbstractClassFile
{

    /**
     * @var string
     */
    protected $_modelName = 'Base';
    
    /**
     * @var string
     */
    protected $_filesystemName = 'modelName';
    
    /**
     * init()
     *
     */
    public function init()
    {
        $this->_modelName = $this->_resource->getAttribute('modelName');
        $this->_filesystemName = ucfirst($this->_modelName) . '.php';
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
            'modelName' => $this->getModelName()
            );
    }
    
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'ModelFile';
    }

    public function getModelName()
    {
        return $this->_modelName;
    }
    
    public function getContents()
    {
        
        $className = $this->getFullClassName($this->_modelName, 'Model');
        
        $codeGenFile = new \Zend\Code\Generator\FileGenerator();
        $codeGenFile->setFilename($this->getPath());
        $codeGenFile->setClass(new \Zend\Code\Generator\ClassGenerator($className));
        
        return $codeGenFile->generate();
    }
    
    
}
