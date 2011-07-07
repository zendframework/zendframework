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
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View;

use ArrayObject;

/**
 * Abstract class for Zend_View to help enforce private constructs.
 *
 * @todo       Allow specifying string names for broker, filter chain, variables
 * @todo       Move escaping into variables object
 * @todo       Move strict variables into variables object
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Variables extends ArrayObject
{
    /**
     * @var string Default encoding (used for escaping)
     */
    protected $encoding = 'UTF-8';

    /**
     * @var callback
     */
    protected $escapeCallback;

    /**
     * Raw values
     *
     * @var array
     */
    protected $rawValues = array();

    /**
     * Strict variables flag; when on, undefined variables accessed in the view
     * scripts will trigger notices
     *
     * @var bool 
     */
    protected $strictVars = false;

    /**
     * Constructor
     * 
     * @param  array $variables 
     * @param  array $options 
     * @return void
     */
    public function __construct(array $variables = array(), array $options = array()) 
    {
        parent::__construct(
            array(), 
            ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS, 
            'ArrayIterator'
        );
        
        // Load each variable into the object using offsetSet() so that they
        // are escaped correctly.
        foreach ($variables as $key => $value) {
            $this->$key = $value;
        }
        $this->setOptions($options);
    }

    /**
     * Configure object
     * 
     * @param  array $options 
     * @return Variables
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'strict_vars':
                    $this->setStrictVars($value);
                    break;
                case 'encoding':
                    $this->setEncoding($value);
                    break;
                case 'escape':
                    $this->setEscapeCallback($value);
                    break;
                default:
                    // Unknown options are considered variables
                    $this[$key] = $value;
                    break;
            }
        }
        return $this;
    }

    /**
     * Set encoding (for escaping)
     * 
     * @param  string $encoding 
     * @return Variables
     */
    public function setEncoding($encoding)
    {
        $this->encoding = (string) $encoding;
        return $this;
    }

    /**
     * Retrieve encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set status of "strict vars" flag
     * 
     * @param  bool $flag 
     * @return Variables
     */
    public function setStrictVars($flag)
    {
        $this->strictVars = (bool) $flag;
        return $this;
    }

    /**
     * Are we operating with strict variables?
     * 
     * @return bool
     */
    public function isStrict()
    {
        return $this->strictVars;
    }

    /**
     * Set escape callback mechanism
     * 
     * @param  callback $spec 
     * @return Variables
     */
    public function setEscapeCallback($spec)
    {
        if (!is_callable($spec)) {
            throw new Exception('Escape callback must be callable');
        }
        $this->escapeCallback = $spec;
    }

    /**
     * Get callback used for escaping variables
     * 
     * @return callback
     */
    public function getEscapeCallback()
    {
        if (null === $this->escapeCallback) {
            $view = $this;
            $this->setEscapeCallback(function($value) use ($view) {
                return htmlspecialchars($value, ENT_COMPAT, $view->getEncoding());
            });
        }
        return $this->escapeCallback;
    }

    /**
     * Escape a value
     *
     * If the value is not a string, it is immediately returned. Otherwise, it
     * is passed to the registered escape callback.
     * 
     * @param  string $value 
     * @return string
     */
    public function escape($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        $escaper = $this->getEscapeCallback();
        return call_user_func($escaper, $value);
    }

    /**
     * Assign many values at once
     * 
     * @param  array|object $spec 
     * @return Variables
     */
    public function assign($spec)
    {
        if (is_object($spec)) {
            if (method_exists($spec, 'toArray')) {
                $spec = $spec->toArray();
            } else {
                $spec = (array) $spec;
            }
        }
        if (!is_array($spec)) {
            throw new Exception(sprintf(
                'assign() expects either an array or an object as an argument; received "%s"',
                gettype($spec)
            ));
        }
        foreach ($spec as $key => $value) {
            $this[$key] = $value;
        }

        return $this;
    }

    /**
     * Sets the value of the specified key
     *
     * If the value is a string, passes it to the escape mechanism before 
     * storage. The raw value may be obtained by calling getRawValue().
     * 
     * @param  mixed $key 
     * @param  mixed $value 
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->rawValues[$key] = $value;
        return parent::offsetSet($key, $this->escape($value));
    }

    /**
     * Set a value that should never be escaped
     * 
     * @param  mixed $key 
     * @param  mixed $value 
     * @return Variables
     */
    public function setCleanValue($key, $value)
    {
        $this->rawValues[$key] = $value;
        parent::offsetSet($key, $value);
        return $this;
    }

    /**
     * Get the variable value
     *
     * If the value has not been defined, a null value will be returned; if 
     * strict vars on in place, a notice will also be raised.
     *
     * Otherwise, returns _escaped_ version of the value.
     * 
     * @param  mixed $key 
     * @return void
     */
    public function offsetGet($key)
    {
        if (!$this->offsetExists($key)) {
            if ($this->isStrict()) {
                trigger_error(sprintf(
                    'View variable "%s" does not exist', $key
                ), E_USER_NOTICE);
            }
            return null;
        }

        return parent::offsetGet($key);
    }

    /**
     * Get the raw value associated with a variable key
     *
     * If the value has not been defined, a null value will be returned; if 
     * strict vars on in place, a notice will also be raised.
     * 
     * @param  mixed $key 
     * @return mixed
     */
    public function getRawValue($key)
    {
        if (!$this->offsetExists($key)) {
            if ($this->isStrict()) {
                trigger_error(sprintf(
                    'View variable "%s" does not exist', $key
                ), E_USER_NOTICE);
            }
            return null;
        }

        return $this->rawValues[$key];
    }

    /**
     * Get all raw values
     * 
     * @return array
     */
    public function getRawValues()
    {
        return $this->rawValues;
    }

    /**
     * Clear all variables
     * 
     * @return void
     */
    public function clear()
    {
        $this->exchangeArray(array());
        $this->rawValues = array();
    }
}
