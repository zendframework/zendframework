<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper\Placeholder\Container;

use Zend\Escaper\Escaper;
use Zend\View\Exception;
use Zend\View\Helper\Placeholder\Registry;
use Zend\View\Renderer\RendererInterface;

/**
 * Base class for targeted placeholder helpers
 */
abstract class AbstractStandalone
    extends \Zend\View\Helper\AbstractHelper
    implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var \Zend\View\Helper\Placeholder\Container\AbstractContainer
     */
    protected $container;

    /**
     * @var Escaper[]
     */
    protected $escapers = array();

    /**
     * @var \Zend\View\Helper\Placeholder\Registry
     */
    protected $registry;

    /**
     * Registry key under which container registers itself
     * @var string
     */
    protected $regKey;

    /**
     * Flag whether to automatically escape output, must also be
     * enforced in the child class if __toString/toString is overridden
     * @var bool
     */
    protected $autoEscape = true;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->setRegistry(Registry::getRegistry());
        $this->setContainer($this->getRegistry()->getContainer($this->regKey));
    }

    /**
     * Retrieve registry
     *
     * @return \Zend\View\Helper\Placeholder\Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Set registry object
     *
     * @param  \Zend\View\Helper\Placeholder\Registry $registry
     * @return \Zend\View\Helper\Placeholder\Container\AbstractStandalone
     */
    public function setRegistry(Registry $registry)
    {
        $this->registry = $registry;
        return $this;
    }

    /**
     * Set Escaper instance
     *
     * @param  Escaper $escaper
     * @return AbstractStandalone
     */
    public function setEscaper(Escaper $escaper)
    {
        $encoding = $escaper->getEncoding();
        $this->escapers[$encoding] = $escaper;
        return $this;
    }

    /**
     * Get Escaper instance
     *
     * Lazy-loads one if none available
     *
     * @param  string|null $enc Encoding to use
     * @return mixed
     */
    public function getEscaper($enc = 'UTF-8')
    {
        $enc = strtolower($enc);
        if (!isset($this->escapers[$enc])) {
            $this->setEscaper(new Escaper($enc));
        }
        return $this->escapers[$enc];
    }

    /**
     * Set whether or not auto escaping should be used
     *
     * @param  bool $autoEscape whether or not to auto escape output
     * @return \Zend\View\Helper\Placeholder\Container\AbstractStandalone
     */
    public function setAutoEscape($autoEscape = true)
    {
        $this->autoEscape = ($autoEscape) ? true : false;
        return $this;
    }

    /**
     * Return whether autoEscaping is enabled or disabled
     *
     * return bool
     */
    public function getAutoEscape()
    {
        return $this->autoEscape;
    }

    /**
     * Escape a string
     *
     * @param  string $string
     * @return string
     */
    protected function escape($string)
    {
        if ($this->view instanceof RendererInterface
            && method_exists($this->view, 'getEncoding')
        ) {
            $enc     = $this->view->getEncoding();
            $escaper = $this->view->plugin('escapeHtml');
            return $escaper((string) $string);
        }

        $escaper = $this->getEscaper();
        return $escaper->escapeHtml((string) $string);
    }

    /**
     * Set container on which to operate
     *
     * @param  \Zend\View\Helper\Placeholder\Container\AbstractContainer $container
     * @return \Zend\View\Helper\Placeholder\Container\AbstractStandalone
     */
    public function setContainer(AbstractContainer $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Retrieve placeholder container
     *
     * @return \Zend\View\Helper\Placeholder\Container\AbstractContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Overloading: set property value
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $container = $this->getContainer();
        $container[$key] = $value;
    }

    /**
     * Overloading: retrieve property
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        $container = $this->getContainer();
        if (isset($container[$key])) {
            return $container[$key];
        }

        return null;
    }

    /**
     * Overloading: check if property is set
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        $container = $this->getContainer();
        return isset($container[$key]);
    }

    /**
     * Overloading: unset property
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        $container = $this->getContainer();
        if (isset($container[$key])) {
            unset($container[$key]);
        }
    }

    /**
     * Overload
     *
     * Proxy to container methods
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception\BadMethodCallException
     */
    public function __call($method, $args)
    {
        $container = $this->getContainer();
        if (method_exists($container, $method)) {
            $return = call_user_func_array(array($container, $method), $args);
            if ($return === $container) {
                // If the container is returned, we really want the current object
                return $this;
            }
            return $return;
        }

        throw new Exception\BadMethodCallException('Method "' . $method . '" does not exist');
    }

    /**
     * String representation
     *
     * @return string
     */
    public function toString()
    {
        return $this->getContainer()->toString();
    }

    /**
     * Cast to string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Countable
     *
     * @return int
     */
    public function count()
    {
        $container = $this->getContainer();
        return count($container);
    }

    /**
     * ArrayAccess: offsetExists
     *
     * @param  string|int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->getContainer()->offsetExists($offset);
    }

    /**
     * ArrayAccess: offsetGet
     *
     * @param  string|int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getContainer()->offsetGet($offset);
    }

    /**
     * ArrayAccess: offsetSet
     *
     * @param  string|int $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return $this->getContainer()->offsetSet($offset, $value);
    }

    /**
     * ArrayAccess: offsetUnset
     *
     * @param  string|int $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        return $this->getContainer()->offsetUnset($offset);
    }

    /**
     * IteratorAggregate: get Iterator
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->getContainer()->getIterator();
    }
}
