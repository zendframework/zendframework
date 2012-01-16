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

namespace Zend\View\Model;

use ArrayAccess,
    Traversable,
    Zend\Stdlib\IteratorToArray,
    Zend\View\Exception,
    Zend\View\Model;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewModel implements Model
{
    /**
     * Renderer options
     * @var array
     */
    protected $options = array();

    /**
     * View variables
     * @var array|ArrayAccess&Traversable
     */
    protected $variables = array();

    /**
     * Constructor
     * 
     * @param  array|Traversable $variables 
     * @param  array|Traversable $options 
     * @return void
     */
    public function __construct($variables = array(), $options = array())
    {
        $this->setVariables($variables);
        $this->setOptions($options);
    }

    /**
     * Set renderer option/hint
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return ViewModel
     */
    public function setOption($name, $value)
    {
        $this->options[(string) $name] = $value;
        return $this;
    }

    /**
     * Set renderer options/hints en masse
     * 
     * @param  array|Traversable $name 
     * @return ViewModel
     */
    public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = IteratorToArray::convert($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        $this->options = $options;
        return $this;
    }

    /**
     * Get renderer options/hints
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
     
    /**
     * Set view variable
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return ViewModel
     */
    public function setVariable($name, $value)
    {
        $this->variables[(string) $name] = $value;
        return $this;
    }

    /**
     * Set view variables en masse
     *
     * Can be an array or a Traversable + ArrayAccess object.
     * 
     * @param  array|ArrayAccess&Traversable $variables 
     * @return ViewModel
     */
    public function setVariables($variables)
    {
        if ($variables instanceof ArrayAccess && $variables instanceof Traversable) {
            $this->variables = $variables;
            return $this;
        }

        if ($variables instanceof Traversable) {
            $variables = IteratorToArray::convert($variables);
        }

        if (!is_array($variables)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable ArrayAccess argument; received "%s"',
                __METHOD__,
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }

        $this->variables = $variables;
        return $this;
    }

    /**
     * Get view variables
     * 
     * @return array|ArrayAccess|Traversable
     */
    public function getVariables()
    {
        return $this->variables;
    }
}
