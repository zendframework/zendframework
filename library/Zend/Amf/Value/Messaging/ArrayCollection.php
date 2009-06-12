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
 * @package    Zend_Amf
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Type encapsulating Flex ArrayCollection
 *
 * Corresponds to flex.messaging.io.ArrayCollection
 *
 * @package    Zend_Amf
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Amf_Value_Messaging_ArrayCollection implements Iterator
{
    /**
     * Current index of source 
     * @var integer
     */
    protected $_sourceIndex = 0;
    
    /**
     * Data in the ArrayCollection
     * @var unknown_type
     */
    protected $_source;
    
    /**
     * Constructor will build an arracollection from the data supplied
     * @param array data
     */
    public function __construct($data = null)
    {
        $this->_source = array();
        if (!is_null($data)) {
            $this->loadSource($data); 
        }
    }
    
    /**
     * Get the number of elements in the collection
     * @return integer Count
     */
    public function count()
    {
        return count($this->_source);
    }
    
    /**
     * Reset the iterator to the beginning of the data
     * @return void
     */
    public function rewind()
    {
        $this->_sourceIndex = 0;
    }
    
    /**
     * Returns the data at the current index in the collection
     * 
     * @return mixed the current row or null if no rows exist. 
     */
    public function current()
    {
        if(isset($this->_source[$this->_sourceIndex])) {
            return $this->_source[$this->_sourceIndex];
        } else {
            return null;
        }
    }
    
    
    /**
     * Returns the collections current index number
     * @return mixed the current row number (starts at 0), null if there is no data 
     */
    public function key()
    {
        return $this->_sourceIndex;
    }

    /**
     * @return mixed the next row number collection, or null if not another row.
     */
    public function next()
    {
       return ++$this->_sourceIndex;
    }

    /**
	 * checks if the iterator is valid
	 * @return boolean is the iterator valid
     */
    public function valid()
    {
        return 0 <= $this->_sourceIndex && $this->_sourceIndex < $this->count();
    }
    
    /**
     * Allow name value pairs to be added to the ArrayCollection
     * @param mixed name pair
     * @param mixed value pair
     */
    public function __set($name, $value)
    {
        if($name == 'externalizedData') {
            $this->loadSource($value);
        } else {
            $this->_source[] = array($name => $value); 
        }
    }
    
    /**
     * Builds an Array into an ArrayCollection and handles Zend_DB_Table
     * 
     * @param array data to be added to the collection
     * @todo Should fire an exception if the data is not an array
     */
    private function loadSource($data)
    {
        if (is_array($data)) {
            foreach($data as $row) {
                if ($row instanceof Zend_Db_Table_Row_Abstract) {
                    $row = $row->toArray();
                }
                if (is_object($row)) {
                    $this->_source[] = $row;
                } else if (is_array($row)) {
                    $source_row = array();
                    foreach($row as $colkey => $colvalue) {
                        $source_row[$colkey] = $colvalue;
                    }
                    if ($source_row) {
                        $this->_source[] = $source_row;
                    }
                }
            }
        }
    }
}