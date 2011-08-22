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

use Zend\Filter\FilterChain,
    ArrayAccess;

/**
 * Abstract class for Zend_View to help enforce private constructs.
 *
 * @todo       Allow specifying string names for broker, filter chain, variables
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpRenderer implements Renderer
{
    /**
     * Template resolver
     *
     * @var TemplateResolver
     */
    private $templateResolver;

    /**
     * Script file name to execute
     *
     * @var string
     */
    private $file = null;

    /**
     * Helper broker
     *
     * @var HelperBroker
     */
    private $helperBroker;

    /**
     * @var Zend\Filter\FilterChain
     */
    private $filterChain;

    /**
     * @var ArrayAccess|array ArrayAccess or associative array representing available variables
     */
    private $vars;

    /**
     * @var ArrayAccess|array Temporary variable cache; used when variables passed to render()
     */
    private $varsCache;

    /**
     * Constructor.
     *
     *
     * @todo handle passing helper broker, options
     * @todo handle passing filter chain, options
     * @todo handle passing variables object, options
     * @todo handle passing resolver object, options
     * @param array $config Configuration key-value pairs.
     */
    public function __construct($config = array())
    {
        $this->init();
    }

    /**
     * Return the template engine object
     *
     * Returns the object instance, as it is its own template engine
     *
     * @return \Zend\View\PhpRenderer
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Allow custom object initialization when extending Zend_View_Abstract or
     * Zend_View
     *
     * Triggered by {@link __construct() the constructor} as its final action.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Set script resolver
     * 
     * @param  string|TemplateResolver $resolver 
     * @param  mixed $options 
     * @return PhpRenderer
     */
    public function setResolver($resolver, $options = null)
    {
        if (is_string($resolver)) {
            if (!class_exists($resolver)) {
                throw new Exception('Class passed as resolver could not be found');
            }
            $resolver = new $resolver($options);
        }
        if (!$resolver instanceof TemplateResolver) {
            throw new Exception(sprintf(
                'Expected resolver to implement TemplateResolver; received "%s"',
                (is_object($resolver) ? get_class($resolver) : gettype($resolver))
            ));
        }
        $this->templateResolver = $resolver;
        return $this;
    }

    /**
     * Retrieve template name or template resolver
     * 
     * @param  null|string $name 
     * @return string|TemplateResolver
     */
    public function resolver($name = null)
    {
        if (null === $this->templateResolver) {
            $this->setResolver(new TemplatePathStack());
        }

        if (null !== $name) {
            return $this->templateResolver->getScriptPath($name);
        }

        return $this->templateResolver;
    }

    /**
     * Set variable storage
     *
     * Expects either an array, or an object implementing ArrayAccess.
     * 
     * @param  array|ArrayAccess $variables 
     * @return PhpRenderer
     */
    public function setVars($variables)
    {
        if (!is_array($variables) && !$variables instanceof ArrayAccess) {
            throw new Exception(sprintf(
                'Expected array or ArrayAccess object; received "%s"',
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }
        $this->vars = $variables;
        return $this;
    }

    /**
     * Get a single variable, or all variables
     * 
     * @param  mixed $key 
     * @return mixed
     */
    public function vars($key = null)
    {
        if (null === $this->vars) {
            $this->setVars(new Variables());
        }

        if (null === $key) {
            return $this->vars;
        }
        return $this->vars[$key];
    }

    /**
     * Set helper broker instance
     * 
     * @param  string|HelperBroker $broker 
     * @return Zend\View\Abstract
     */
    public function setBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception(sprintf(
                    'Invalid helper broker class provided (%s)',
                    $broker
                ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof HelperBroker) {
            throw new Exception(sprintf(
                'Helper broker must extend Zend\View\HelperBroker; got type "%s" instead',
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        $broker->setView($this);
        $this->helperBroker = $broker;
    }

    /**
     * Get helper broker instance
     * 
     * @param  null|string $helper Helper name to return
     * @param  null|array $options Options to pass to helper constructor (if not already instantiated)
     * @return HelperBroker|Helper
     */
    public function broker($helper = null, array $options = null)
    {
        if (null === $this->helperBroker) {
            $this->setBroker(new HelperBroker());
        }
        if (null === $helper) {
            return $this->helperBroker;
        }
        return $this->helperBroker->load($helper, $options);
    }

    /**
     * Set filter chain
     * 
     * @param  FilterChain $filters 
     * @return Zend\View\PhpRenderer
     */
    public function setFilterChain(FilterChain $filters)
    {
        $this->filterChain = $filters;
        return $this;
    }

    /**
     * Retrieve filter chain for post-filtering script content
     * 
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (null === $this->filterChain) {
            $this->setFilterChain(new FilterChain());
        }
        return $this->filterChain;
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script name to process.
     * @return string The script output.
     */
    public function render($name, $vars = null)
    {
        // find the script file name using the parent private method
        $this->file = $this->resolver($name);
        unset($name); // remove $name from local scope

        if (null !== $vars) {
            $this->varsCache = $this->vars();
            $this->setVars($vars);
        }

        unset($vars); // remove $vars from local scope

        ob_start();
        include $this->file;
        $content = ob_get_clean();

        if (null !== $this->varsCache) {
            $this->setVars($this->varsCache);
            $this->varsCache = null;
        }

        return $this->getFilterChain()->filter($content); // filter output
    }
}
