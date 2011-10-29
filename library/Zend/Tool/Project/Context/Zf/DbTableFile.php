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
use Zend\Code\Generator\FileGenerator,
    Zend\Code\Generator\ClassGenerator,
    Zend\Code\Generator\PropertyGenerator;

/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Code\Generator\ClassGenerator
 * @uses       \Zend\Code\Generator\FileGenerator
 * @uses       \Zend\Code\Generator\PropertyGenerator
 * @uses       \Zend\Tool\Project\Context\Zf\AbstractClassFile
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DbTableFile extends AbstractClassFile
{

    protected $_dbTableName = null;
    
    protected $_actualTableName = null;
    
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'DbTableFile';
    }

    /**
     * init()
     *
     */
    public function init()
    {
        $this->_dbTableName = $this->_resource->getAttribute('dbTableName');
        $this->_actualTableName = $this->_resource->getAttribute('actualTableName');
        $this->_filesystemName = ucfirst($this->_dbTableName) . '.php';
        parent::init();
    }
    
    public function getPersistentAttributes()
    {
        return array('dbTableName' => $this->_dbTableName);
    }

    public function getContents()
    {
        $className = $this->getFullClassName($this->_dbTableName, 'Model\DbTable');
        
        $codeGenFile = new FileGenerator($this->getPath());
        $codeGenFile->setClass(new ClassGenerator($className, null, null, '\Zend\Db\Table\AbstractTable', null, array(
                    new PropertyGenerator('_name', $this->_actualTableName, PropertyGenerator::FLAG_PROTECTED),
                )
            ));
                
        return $codeGenFile->generate();
    }
    
}
