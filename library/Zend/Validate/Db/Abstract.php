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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */  
  
/**
 * @see Zend_Validate_Abstract
 */  
require_once 'Zend/Validate/Abstract.php';  
  
/**
 * Class for Database record validation
 * 
 * @category   Zend
 * @package    Zend_Validate
 * @uses       Zend_Validate_Abstract
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */  
abstract class Zend_Validate_Db_Abstract extends Zend_Validate_Abstract    
{   
    /**
     * Error constants
     */ 
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';  
    const ERROR_RECORD_FOUND    = 'recordFound';  
   
    /**
     * @var array Message templates
     */    
    protected $_messageTemplates = array(self::ERROR_NO_RECORD_FOUND => 'No record matching %value% was found', 
                                         self::ERROR_RECORD_FOUND    => 'A record matching %value% was found');     
 
    /**
     * @var string
     */    
    protected $_table = '';    
    
    /**
     * @var string
     */    
    protected $_field = '';    
       
    /**
     * @var mixed
     */   
    protected $_exclude = null;   
       
    /**
     * Database adapter to use. If null isValid() will use Zend_Db::getInstance instead
     *
     * @var unknown_type
     */    
    protected $_adapter = null;    
   
    /**
     * Provides basic configuration for use with Zend_Validate_Db Validators 
     * Setting $exclude allows a single record to be excluded from matching.
     * Exclude can either be a String containing a where clause, or an array with `field` and `value` keys
     * to define the where clause added to the sql.  
     * A database adapter may optionally be supplied to avoid using the registered default adapter. 
     * 
     * @param string $table The database table to validate against
     * @param string $field The field to check for a match
     * @param string||array $exclude An optional where clause or field/value pair to exclude from the query
     * @param Zend_Db_Adapter_Abstract $adapter An optional database adapter to use.
     */   
    public function __construct($table, $field, $exclude = null, Zend_Db_Adapter_Abstract $adapter = null)    
    {    
        if ($adapter !== null) { 
            $this->_adapter = $adapter;    
        }   
        $this->_exclude = $exclude;   
        $this->_table   = (string) $table;    
        $this->_field   = (string) $field;   
    }  
     
    /**
     * Run query and returns matches, or null if no matches are found.
     *
     * @param  String $value
     * @return Array when matches are found.
     */ 
    protected function _query($value) 
    { 
        /**
         * Check for an adapter being defined. if not, fetch the default adapter.
         */ 
        if($this->_adapter === null) {
            $this->_adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        }

        /**
         * Build select object
         */ 
        $select = new Zend_Db_Select($this->_adapter);
        $select->from($this->_table)
               ->columns($this->_field)
               ->where($this->_adapter->quoteIdentifier($this->_field).' = ?', $value); 
        if ($this->_exclude !== null) { 
            if (is_array($this->_exclude)) { 
                $select->where($this->_adapter->quoteIdentifier($this->_exclude['field']).' != ?', $this->_exclude['value']); 
            } else { 
                $select->where($this->_exclude); 
            } 
        } 
        $select->limit(1); 
                 
        /**
         * Run query
         */ 
        $result = $this->_adapter->fetchRow($select, array(), Zend_Db::FETCH_ASSOC); 
         
        return $result; 
    } 
}
