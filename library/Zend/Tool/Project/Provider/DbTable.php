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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: View.php 18386 2009-09-23 20:44:43Z ralph $
 */

/**
 * @see Zend_Tool_Project_Provider_Abstract
 */
require_once 'Zend/Tool/Project/Provider/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Provider_DbTable extends Zend_Tool_Project_Provider_Abstract
{
    
    protected $_specialties = array('FromDatabase');
    
    /**
     * @var Zend_Filter
     */
    protected $_nameFilter = null;
    
    public static function createResource(Zend_Tool_Project_Profile $profile, $dbTableName, $moduleName = null)
    {
        $profileSearchParams = array();

        if ($moduleName != null && is_string($moduleName)) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => array('moduleName' => $moduleName));
        }

        $profileSearchParams[] = 'modelsDirectory';

        $modelsDirectory = $profile->search($profileSearchParams);
        
        if (!($dbTableDirectory = $modelsDirectory->search('DbTableDirectory'))) {
            $dbTableDirectory = $modelsDirectory->createResource('DbTableDirectory');
        }
        
        $dbTableFile = $dbTableDirectory->createResource('DbTableFile', array('dbTableName' => $dbTableName));
        
        return $dbTableFile;
    }
      
    
    public function create($tableName, $module = null)
    {
        //@todo create
    }
    
    public function createFromDatabase($module = null)
    {
        //@todo create from db
    }
    
    protected function _convertTableNameToClassName($tableName)
    {
        if ($this->_nameFilter == null) {
            $this->_nameFilter = new Zend_Filter();
            $this->_nameFilter
                ->addFilter(new Zend_Filter_Word_UnderscoreToCamelCase());
        }
        
        
        return $this->_nameFilter->filter($tableName);
    }
    
}