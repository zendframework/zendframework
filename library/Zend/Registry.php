<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Registry.php
 */

namespace Zend;

use ArrayObject;
use DomainException;
use RuntimeException;

/**
 * Generic storage class helps to manage global data.
 *
 * @category   Zend
 * @package    Zend_Registry
 */
class Registry extends ArrayObject
{
    /**
     * Constructs a parent ArrayObject with default
     * ARRAY_AS_PROPS to allow acces as an object
     *
     * @param array $array data array
     * @param integer $flags ArrayObject flags
     */
    public function __construct($array = array(), $flags = parent::ARRAY_AS_PROPS)
    {
        parent::__construct($array, $flags);
    }

    /**
     * getter method, basically same as offsetGet().
     *
     * @param  string $index - get the value associated with $index
     * @param  mixed $default Default value to return if $index does not exist
     * @return mixed
     */
    public function get($index, $default = null)
    {
        if (!$this->offsetExists($index)) {
            return $default;
        }

        return $this->offsetGet($index);
    }

    /**
     * setter method, basically same as offsetSet().
     *
     * @param  string $index The location in the ArrayObject in which to store *   the value.
     * @param  mixed $value The object to store in the ArrayObject.
     * @return void
     */
    public function set($index, $value)
    {
        $this->offsetSet($index, $value);
    }

    /**
     * Returns TRUE if the $index is a named value in the registry,
     * or FALSE if $index was not found in the registry.
     *
     * @param  string $index
     * @return boolean
     */
    public function isRegistered($index)
    {
        return $this->offsetExists($index);
    }
}
