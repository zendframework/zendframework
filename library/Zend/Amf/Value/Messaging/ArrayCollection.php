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
class Zend_Amf_Value_Messaging_ArrayCollection implements  ArrayAccess , IteratorAggregate, Countable
{
    /**
     * Data in the ArrayCollection
     * @var ArrayObject
     */
    protected $_source;
    
    /**
     * Constructor will build an arracollection from the data supplied
     * @param array data
     */
    public function __construct($data = null)
    {
        $this->_source = new ArrayObject();
        if (!is_null($data)) {
            $this->loadSource($data); 
        }
    }
    
    
    /**
     * Value is append to the last element of the ArrayCollection
     * @param misc new value to be added to the ArrayCollection
     */
    public function append($value)
    {
        if(!is_null($value))
        {
            $this->_source->append($value); 
        }
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
     * Get the number of elements in the collection
     * @return integer Count
     */
    public function count()
    {
        return count($this->_source);
    }
        
	/**
     * Check if the specified offset exists exists for the key supplied. 
     *
     * @param mixed $offset
     * @return bool true if it exists. 
     */
    function offsetExists($offset) {
        return isset($this->_source[$offset]);
    }

    /**
     * Value of given offset
     *
     * @param mixed $offset
     * @return mixed
     */
    function offsetGet($offset) {
        return $this->_source[$offset];
    }

    /**
     * Update or add a new value based on the on the offset key. Careful as this will overwrite any existing propery by the same offset id
     *
     * @param mixed Offset to modify
     * @param mixed New value for the offset. 
     */
    function offsetSet($offset,$value) {
        if (!is_null($offset)) {
            $this->_source[$offset] = $value;
        }
    }

    /**
     * Offest to delete from the collection
     *
     * @param mixed $offset
     */
    function offsetUnset($offset) {
        unset($this->_source[$offset]);
    }
    
    /**
	 * Return the source of the iterator
	 * @return ArrayObject
     */
    function getIterator()
    {
        return $this->_source;
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
        } else {
            require_once 'Zend/Amf/Server/Exception.php';
            throw new Zend_Amf_Server_Exception("Could not load source data into an ArrayCollection must be an Array");
        }
    }
}