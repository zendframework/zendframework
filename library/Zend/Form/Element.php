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
 * @package    Zend_Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Element implements ElementInterface
{
    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var array Validation error messages
     */
    protected $messages = array();

    /**
     * Constructor
     * 
     * @param  null|string|int $name Optional name for the element
     * @return void
     */
    public function __construct($name = null)
    {
        if (null !== $name) {
            $this->setName($name);
        }
    }

    /**
     * Set value for name
     *
     * @param  string|int name
     * @return Element
     */
    public function setName($name)
    {
        $this->setAttribute('name', $name);
        return $this;
    }
    
    /**
     * Get value for name
     *
     * @return string|int
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Set a single element attribute
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return Element
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Retrieve a single element attribute
     * 
     * @param  string $optionalKey 
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (!array_key_exists($key, $this->attributes)) {
            return null;
        }
        return $this->attributes[$key];
    }

    /**
     * Set many attributes at once
     *
     * Implementation will decide if this will overwrite or merge.
     * 
     * @param  array|Traversable $arrayOrTraversable 
     * @return Element
     */
    public function setAttributes($arrayOrTraversable)
    {
        if (!is_array($arrayOrTraversable) && !$arrayOrTraversable instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($arrayOrTraversable) ? get_class($arrayOrTraversable) : gettype($arrayOrTraversable))
            ));
        }
        foreach ($arrayOrTraversable as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    /**
     * Retrieve all attributes at once
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Clear all attributes
     * 
     * @return void
     */
    public function clearAttributes()
    {
        $this->attributes = array();
    }

    /**
     * Set a list of messages to report when validation fails
     *
     * @param  array|Traversable $messages
     * @return ElementInterface
     */
    public function setMessages($messages)
    {
        if (!is_array($messages) && !$messages instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object of validation error messages; received "%s"',
                __METHOD__,
                (is_object($messages) ? get_class($messages) : gettype($messages))
            ));
        }

        $this->messages = $messages;
        return $this;
    }

    /**
     * Get validation error messages, if any
     *
     * Returns a list of validation failure messages, if any.
     * 
     * @return array|Traversable
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
