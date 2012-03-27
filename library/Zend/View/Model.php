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
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View;

use Countable,
    IteratorAggregate;

/**
 * Interface describing a view model.
 *
 * Extends "Countable"; count() should return the number of children attached
 * to the model.
 *
 * Extends "IteratorAggregate"; should allow iterating over children.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Model extends Countable, IteratorAggregate
{
    /**
     * Set renderer option/hint
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return Model
     */
    public function setOption($name, $value);

    /**
     * Set renderer options/hints en masse
     * 
     * @param  array|\Traversable $name 
     * @return Model
     */
    public function setOptions($options);

    /**
     * Get renderer options/hints
     * 
     * @return array|\Traversable
     */
    public function getOptions();
     
    /**
     * Set view variable
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return Model
     */
    public function setVariable($name, $value);

    /**
     * Set view variables en masse
     * 
     * @param  array|\ArrayAccess $variables 
     * @return Model
     */
    public function setVariables($variables);

    /**
     * Get view variables
     * 
     * @return array|\ArrayAccess
     */
    public function getVariables();

    /**
     * Set the template to be used by this model 
     * 
     * @param  string $template
     * @return Model
     */
    public function setTemplate($template);

    /**
     * Get the template to be used by this model
     * 
     * @return string
     */
    public function getTemplate();

    /**
     * Add a child model
     * 
     * @param  Model $child 
     * @param  null|string $captureTo Optional; if specified, the "capture to" value to set on the child
     * @return Model
     */
    public function addChild(Model $child, $captureTo = null);

    /**
     * Return all children.
     *
     * Return specifies an array, but may be any iterable object.
     *
     * @return array
     */
    public function getChildren();

    /**
     * Does the model have any children? 
     * 
     * @return bool
     */
    public function hasChildren();

    /**
     * Set the name of the variable to capture this model to, if it is a child model
     * 
     * @param  string $capture 
     * @return Model
     */
    public function setCaptureTo($capture);

    /**
     * Get the name of the variable to which to capture this model
     * 
     * @return string
     */
    public function captureTo();

    /**
     * Set flag indicating whether or not this is considered a terminal or standalone model
     * 
     * @param  bool $terminate 
     * @return Model
     */
    public function setTerminal($terminate);

    /**
     * Is this considered a terminal or standalone model?
     * 
     * @return bool
     */
    public function terminate();
}
