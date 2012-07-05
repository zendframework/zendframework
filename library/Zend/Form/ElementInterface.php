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
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ElementInterface
{
    /**
     * Set the name of this element
     *
     * In most cases, this will proxy to the attributes for storage, but is
     * present to indicate that elements are generally named.
     * 
     * @param  string $name 
     * @return ElementInterface
     */
    public function setName($name);

    /**
     * Retrieve the element name
     * 
     * @return string
     */
    public function getName();

    /**
     * Set options for an element
     *
     * @param  array|\Traversable $options
     * @return ElementInterface
     */
    public function setOptions($options);

    /**
     * Set a single element attribute
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return ElementInterface
     */
    public function setAttribute($key, $value);

    /**
     * Retrieve a single element attribute
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key);

    /**
     * Return true if a specific attribute is set
     *
     * @param  string $key
     * @return bool
     */
    public function hasAttribute($key);

    /**
     * Set many attributes at once
     *
     * Implementation will decide if this will overwrite or merge.
     * 
     * @param  array|\Traversable $arrayOrTraversable
     * @return ElementInterface
     */
    public function setAttributes($arrayOrTraversable);

    /**
     * Retrieve all attributes at once
     * 
     * @return array|\Traversable
     */
    public function getAttributes();

    /**
     * Set the label (if any) used for this element
     *
     * @param  $label
     * @return ElementInterface
     */
    public function setLabel($label);

    /**
     * Retrieve the label (if any) used for this element
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set a list of messages to report when validation fails
     *
     * @param  array|\Traversable $messages
     * @return ElementInterface
     */
    public function setMessages($messages);

    /**
     * Get validation error messages, if any
     *
     * Returns a list of validation failure messages, if any.
     * 
     * @return array|\Traversable
     */
    public function getMessages();
}
