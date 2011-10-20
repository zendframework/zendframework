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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config;

/**
 * @uses       \Zend\Config\Exception
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Config implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * Whether in-memory modifications to configuration data are allowed
     *
     * @var boolean
     */
    protected $_allowModifications;

    /**
     * Iteration index
     *
     * @var integer
     */
    protected $_index;

    /**
     * Number of elements in configuration data
     *
     * @var integer
     */
    protected $_count;

    /**
     * Contains array of configuration data
     *
     * @var array
     */
    protected $_data;

    /**
     * Used when unsetting values during iteration to ensure we do not skip
     * the next element
     *
     * @var boolean
     */
    protected $_skipNextIteration;

    /**
     * Contains which config file sections were loaded. This is null
     * if all sections were loaded, a string name if one section is loaded
     * and an array of string names if multiple sections were loaded.
     *
     * @var mixed
     */
    protected $_loadedSection;

    /**
     * This is used to track section inheritance. The keys are names of sections that
     * extend other sections, and the values are the extended sections.
     *
     * @var array
     */
    protected $_extends = array();

    /**
     * Internal error messages
     *
     * @var null|array
     */
    protected $_errorMessages = array();

    /**
     * Zend_Config provides a property based interface to
     * an array. The data are read-only unless $allowModifications
     * is set to true on construction.
     *
     * Zend_Config also implements Countable and Iterator to
     * facilitate easy access to the data.
     *
     * @param  array   $array
     * @param  boolean $allowModifications
     * @return void
     */
    public function __construct(array $array, $allowModifications = false)
    {
        $this->_allowModifications = (boolean) $allowModifications;
        $this->_loadedSection = null;
        $this->_index = 0;
        $this->_data = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new self($value, $this->_allowModifications);
            } else {
                $this->_data[$key] = $value;
            }
        }
        $this->_count = count($this->_data);
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if (array_key_exists($name, $this->_data)) {
            $result = $this->_data[$name];
        }
        return $result;
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws \Zend\Config\Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if ($this->_allowModifications) {
            if (is_array($value)) {
                $this->_data[$name] = new self($value, true);
            } else {
                $this->_data[$name] = $value;
            }
            $this->_count = count($this->_data);
        } else {
            throw new Exception\InvalidArgumentException('Zend_Config is read only');
        }
    }

    /**
     * Deep clone of this instance to ensure that nested Zend_Configs
     * are also cloned.
     *
     * @return void
     */
    public function __clone()
    {
      $array = array();
      foreach ($this->_data as $key => $value) {
          if ($value instanceof Config) {
              $array[$key] = clone $value;
          } else {
              $array[$key] = $value;
          }
      }
      $this->_data = $array;
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $data = $this->_data;
        foreach ($data as $key => $value) {
            if ($value instanceof Config) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Support unset() overloading on PHP 5.1
     *
     * @param  string $name
     * @throws \Zend\Config\Exception
     * @return void
     */
    public function __unset($name)
    {
        if ($this->_allowModifications) {
            unset($this->_data[$name]);
            $this->_count = count($this->_data);
            $this->_skipNextIteration = true;
        } else {
            throw new Exception\InvalidArgumentException('Zend_Config is read only');
        }

    }

    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        $this->_skipNextIteration = false;
        return current($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function next()
    {
        if ($this->_skipNextIteration) {
            $this->_skipNextIteration = false;
            return;
        }
        next($this->_data);
        $this->_index++;
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function rewind()
    {
        $this->_skipNextIteration = false;
        reset($this->_data);
        $this->_index = 0;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_index < $this->_count;
    }

    /**
     * offsetExists(): defined by ArrayAccess interface.
     * 
     * @see    ArrayAccess::offsetExists()
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }
    
    /**
     * offsetGet(): defined by ArrayAccess interface.
     * 
     * @see    ArrayAccess::offsetGet()
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
    
    /**
     * offsetSet(): defined by ArrayAccess interface.
     * 
     * @see    ArrayAccess::offsetSet()
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }
    
    /**
     * offsetUnset(): defined by ArrayAccess interface.
     * 
     * @see    ArrayAccess::offsetUnset()
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }
    
    /**
     * Returns the section name(s) loaded.
     *
     * @return mixed
     */
    public function getSectionName()
    {
        if(is_array($this->_loadedSection) && count($this->_loadedSection) == 1) {
            $this->_loadedSection = $this->_loadedSection[0];
        }
        return $this->_loadedSection;
    }

    /**
     * Returns true if all sections were loaded
     *
     * @return boolean
     */
    public function areAllSectionsLoaded()
    {
        return $this->_loadedSection === null;
    }

    /**
     * Merge another Zend_Config with this one. The items
     * in $merge will override the same named items in
     * the current config.
     *
     * @param \Zend\Config\Config $merge
     * @return \Zend\Config\Config
     */
    public function merge(Config $merge)
    {
        foreach($merge as $key => $item) {
            if(array_key_exists($key, $this->_data)) {
                if($item instanceof Config && $this->$key instanceof Config) {
                    $this->$key = $this->$key->merge(new Config($item->toArray(), !$this->readOnly()));
                } else {
                    $this->$key = $item;
                }
            } else {
                if($item instanceof Config) {
                    $this->$key = new Config($item->toArray(), !$this->readOnly());
                } else {
                    $this->$key = $item;
                }
            }
        }

        return $this;
    }

    /**
     * Prevent any more modifications being made to this instance. Useful
     * after merge() has been used to merge multiple Zend_Config objects
     * into one object which should then not be modified again.
     *
     */
    public function setReadOnly()
    {
        $this->_allowModifications = false;
        foreach ($this->_data as $key => $value) {
            if ($value instanceof Config) {
                $value->setReadOnly();
            }
        }
    }

    /**
     * Returns if this Zend_Config object is read only or not.
     *
     * @return boolean
     */
    public function readOnly()
    {
        return !$this->_allowModifications;
    }

    /**
     * Get the current extends
     *
     * @return array
     */
    public function getExtends()
    {
        return $this->_extends;
    }

    /**
     * Set an extend for Zend_Config_Writer
     *
     * @param  string $extendingSection
     * @param  string $extendedSection
     * @return void
     */
    public function setExtend($extendingSection, $extendedSection = null)
    {
        if ($extendedSection === null && isset($this->_extends[$extendingSection])) {
            unset($this->_extends[$extendingSection]);
        } else if ($extendedSection !== null) {
            $this->_extends[$extendingSection] = $extendedSection;
        }
    }

    /**
     * Throws an exception if $extendingSection may not extend $extendedSection,
     * and tracks the section extension if it is valid.
     *
     * @param  string $extendingSection
     * @param  string $extendedSection
     * @throws \Zend\Config\Exception
     * @return void
     */
    protected function _assertValidExtend($extendingSection, $extendedSection)
    {
        // detect circular section inheritance
        $extendedSectionCurrent = $extendedSection;
        while (array_key_exists($extendedSectionCurrent, $this->_extends)) {
            if ($this->_extends[$extendedSectionCurrent] == $extendingSection) {
                throw new Exception\RuntimeException('Illegal circular inheritance detected');
            }
            $extendedSectionCurrent = $this->_extends[$extendedSectionCurrent];
        }
        // remember that this section extends another section
        $this->_extends[$extendingSection] = $extendedSection;
    }

    /**
     * Merge two arrays recursively, overwriting keys of the same name
     * in $firstArray with the value in $secondArray.
     *
     * @param  mixed $firstArray  First array
     * @param  mixed $secondArray Second array to merge into first array
     * @return array
     */
    protected function _arrayMergeRecursive($firstArray, $secondArray)
    {
        if (is_array($firstArray) && is_array($secondArray)) {
            return array_replace_recursive($firstArray, $secondArray);
        }
        return $secondArray;
    }

    /**
     * Set internal error handler
     *
     * @return void
     */
    protected function _setErrorHandler()
    {
        set_error_handler(array($this, '_handleError'));
    }

    /**
     * Restore internal error handler
     *
     * @return array Handled error messages
     */
    protected function _restoreErrorHandler()
    {
        restore_error_handler();
        $errorMessages = $this->_errorMessages;
        $this->_errorMessages = array();
        return $errorMessages;
    }

    /**
     * Handle internal errors
     *
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline
     * @return void
     */
    protected function _handleError($errno, $errstr, $errfile, $errline)
    {
        $this->_errorMessages[] = trim($errstr);
    }

}
