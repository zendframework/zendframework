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
    ArrayIterator,
    Traversable,
    Zend\Stdlib\ArrayUtils,
    Zend\View\Exception,
    Zend\View\Model,
    Zend\View\Variables as ViewVariables;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewModel implements ModelInterface
{
    /**
     * What variable a parent model should capture this model to 
     * 
     * @var string
     */
    protected $captureTo = 'content';

    /**
     * Child models
     * @var array
     */
    protected $children = array();

    /**
     * Renderer options
     * @var array
     */
    protected $options = array();

    /**
     * Template to use when rendering this model 
     * 
     * @var string
     */
    protected $template = '';

    /**
     * Is this a standalone, or terminal, model?
     * 
     * @var bool
     */
    protected $terminate = false;

    /**
     * View variables
     * @var array|ArrayAccess&Traversable
     */
    protected $variables = array();

    /**
     * Constructor
     * 
     * @param  null|array|Traversable $variables 
     * @param  array|Traversable $options 
     * @return void
     */
    public function __construct($variables = null, $options = null)
    {
        if (null === $variables) {
            $variables = new ViewVariables();
        }
        $this->setVariables($variables);

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Property overloading: set variable value
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return void
     */
    public function __set($name, $value)
    {
        $variables = $this->getVariables();
        $variables[$name] = $value;
    }

    /**
     * Property overloading: get variable value
     * 
     * @param  string $name 
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->__isset($name)) {
            return null;
        }

        $variables = $this->getVariables();
        return $variables[$name];
    }

    /**
     * Property overloading: do we have the requested variable value?
     * 
     * @param  string $name 
     * @return bool
     */
    public function __isset($name)
    {
        $variables = $this->getVariables();
        return isset($variables[$name]);
    }

    /**
     * Property overloading: unset the requested variable
     * 
     * @param  string $name 
     * @return void
     */
    public function __unset($name)
    {
        if (!$this->__isset($name)) {
            return null;
        }

        $variables = $this->getVariables();
        unset($variables[$name]);
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
        // Assumption is that lowest common denominator for renderer configuration
        // is an array
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
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
        // Assumption is that renderers can handle arrays or ArrayAccess objects
        if ($variables instanceof ArrayAccess && $variables instanceof Traversable) {
            $this->variables = $variables;
            return $this;
        }

        if ($variables instanceof Traversable) {
            $variables = ArrayUtils::iteratorToArray($variables);
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

    /**
     * Set the template to be used by this model 
     * 
     * @param  string $template
     * @return ViewModel
     */
    public function setTemplate($template)
    {
        $this->template = (string) $template;
        return $this;
    }

    /**
     * Get the template to be used by this model
     * 
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add a child model
     * 
     * @param  ModelInterface $child
     * @param  null|string $captureTo Optional; if specified, the "capture to" value to set on the child
     * @return ViewModel
     */
    public function addChild(ModelInterface $child, $captureTo = null)
    {
        $this->children[] = $child;
        if (null !== $captureTo) {
            $child->setCaptureTo($captureTo);
        }
        return $this;
    }

    /**
     * Return all children.
     *
     * Return specifies an array, but may be any iterable object.
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Does the model have any children? 
     * 
     * @return bool
     */
    public function hasChildren()
    {
        return (0 < count($this->children));
    }

    /**
     * Set the name of the variable to capture this model to, if it is a child model
     * 
     * @param  string $capture 
     * @return ViewModel
     */
    public function setCaptureTo($capture)
    {
        $this->captureTo = (string) $capture;
        return $this;
    }

    /**
     * Get the name of the variable to which to capture this model
     * 
     * @return string
     */
    public function captureTo()
    {
        return $this->captureTo;
    }

    /**
     * Set flag indicating whether or not this is considered a terminal or standalone model
     * 
     * @param  bool $terminate 
     * @return ViewModel
     */
    public function setTerminal($terminate)
    {
        $this->terminate = (bool) $terminate;
        return $this;
    }

    /**
     * Is this considered a terminal or standalone model?
     * 
     * @return bool
     */
    public function terminate()
    {
        return $this->terminate;
    }

    /**
     * Return count of children
     * 
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Get iterator of children
     * 
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->children);
    }
}
