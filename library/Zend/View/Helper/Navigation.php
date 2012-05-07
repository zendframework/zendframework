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
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper;

use Zend\Loader\ShortNameLocator,
    Zend\Loader\PluginClassLoader,
    Zend\Navigation\Container,
    Zend\View\Helper\Navigation\AbstractHelper as AbstractNavigationHelper,
    Zend\View\Helper\Navigation\HelperInterface as NavigationHelper,
    Zend\View\Exception;

/**
 * Proxy helper for retrieving navigational helpers and forwarding calls
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Navigation extends AbstractNavigationHelper
{
    /**
     * View helper namespace
     *
     * @var string
     */
    const NS = 'Zend\View\Helper\Navigation';

    /**
     * @var ShortNameLocator
     */
    protected $loader;

    /**
     * Default proxy to use in {@link render()}
     *
     * @var string
     */
    protected $_defaultProxy = 'menu';

    /**
     * Contains references to proxied helpers
     *
     * @var array
     */
    protected $_helpers = array();

    /**
     * Whether container should be injected when proxying
     *
     * @var bool
     */
    protected $_injectContainer = true;

    /**
     * Whether ACL should be injected when proxying
     *
     * @var bool
     */
    protected $_injectAcl = true;

    /**
     * Whether translator should be injected when proxying
     *
     * @var bool
     */
    protected $_injectTranslator = true;

    /**
     * Helper entry point
     *
     * @param  \Zend\Navigation\Container $container  [optional] container to
     *                                               operate on
     * @return \Zend\View\Helper\Navigation           fluent interface, returns
     *                                               self
     */
    public function __invoke(Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }

    /**
     * Magic overload: Proxy to other navigation helpers or the container
     *
     * Examples of usage from a view script or layout:
     * <code>
     * // proxy to Menu helper and render container:
     * echo $this->navigation()->menu();
     *
     * // proxy to Breadcrumbs helper and set indentation:
     * $this->navigation()->breadcrumbs()->setIndent(8);
     *
     * // proxy to container and find all pages with 'blog' route:
     * $blogPages = $this->navigation()->findAllByRoute('blog');
     * </code>
     *
     * @param  string $method             helper name or method name in
     *                                    container
     * @param  array  $arguments          [optional] arguments to pass
     * @return mixed                      returns what the proxied call returns
     * @throws \Zend\View\Exception\ExceptionInterface        if proxying to a helper, and the
     *                                    helper is not an instance of the
     *                                    interface specified in
     *                                    {@link findHelper()}
     * @throws \Zend\Navigation\Exception\ExceptionInterface  if method does not exist in container
     */
    public function __call($method, array $arguments = array())
    {
        // check if call should proxy to another helper
        $helper = $this->findHelper($method, false);
        if ($helper) {
            return call_user_func_array($helper, $arguments);
        }

        // default behaviour: proxy call to container
        return parent::__call($method, $arguments);
    }

    /**
     * Set plugin loader for retrieving navigation helpers
     *
     * @param ShortNameLocator $loader
     * @return Navigation
     */
    public function setPluginLoader(ShortNameLocator $loader)
    {
        $this->loader = $loader;
        return $this;
    }

    /**
     * Retrieve plugin loader for navigation helpers
     *
     * Lazy-loads an instance of Navigation\HelperLoader if none currently
     * registered.
     *
     * @return ShortNameLocator
     */
    public function getPluginLoader()
    {
        if (null === $this->loader) {
            $this->setPluginLoader(new Navigation\HelperLoader());
        }
        return $this->loader;
    }

    /**
     * Returns the helper matching $proxy
     *
     * The helper must implement the interface
     * {@link Zend\View\Helper\Navigation\Helper}.
     *
     * @param string $proxy                        helper name
     * @param bool   $strict                       [optional] whether
     *                                             exceptions should be
     *                                             thrown if something goes
     *                                             wrong. Default is true.
     * @return \Zend\View\Helper\Navigation\Helper\HelperInterface  helper instance
     * @throws \Zend\Loader\PluginLoader\Exception  if $strict is true and
     *         helper cannot be found
     * @throws Exception\InvalidArgumentException if $strict is true and
     *         helper does not implement the specified interface
     */
    public function findHelper($proxy, $strict = true)
    {
        if (isset($this->_helpers[$proxy])) {
            return $this->_helpers[$proxy];
        }

        $loader = $this->getPluginLoader();

        if ($strict) {
            $class = $loader->load($proxy);
        } else {
            try {
                $class = $loader->load($proxy);
            } catch (\Zend\Loader\Exception $e) {
                return null;
            }
        }

        $helper = new $class();

        if (!$helper instanceof AbstractNavigationHelper) {
            if ($strict) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'Proxy helper "%s" is not an instance of ' .
                        'Zend\View\Helper\Navigation\Helper',
                        get_class($helper)
                ));
            }

            return null;
        }

        $helper->setView($this->view);
        $this->_inject($helper);
        $this->_helpers[$proxy] = $helper;

        return $helper;
    }

    /**
     * Injects container, ACL, and translator to the given $helper if this
     * helper is configured to do so
     *
     * @param  NavigationHelper $helper  helper instance
     * @return void
     */
    protected function _inject(NavigationHelper $helper)
    {
        if ($this->getInjectContainer() && !$helper->hasContainer()) {
            $helper->setContainer($this->getContainer());
        }

        if ($this->getInjectAcl()) {
            if (!$helper->hasAcl()) {
                $helper->setAcl($this->getAcl());
            }
            if (!$helper->hasRole()) {
                $helper->setRole($this->getRole());
            }
        }

        if ($this->getInjectTranslator() && !$helper->hasTranslator()) {
            $helper->setTranslator($this->getTranslator());
        }
    }

    // Accessors:

    /**
     * Sets the default proxy to use in {@link render()}
     *
     * @param  string $proxy                default proxy
     * @return \Zend\View\Helper\Navigation  fluent interface, returns self
     */
    public function setDefaultProxy($proxy)
    {
        $this->_defaultProxy = (string) $proxy;
        return $this;
    }

    /**
     * Returns the default proxy to use in {@link render()}
     *
     * @return string  the default proxy to use in {@link render()}
     */
    public function getDefaultProxy()
    {
        return $this->_defaultProxy;
    }

    /**
     * Sets whether container should be injected when proxying
     *
     * @param bool $injectContainer         [optional] whether container should
     *                                      be injected when proxying. Default
     *                                      is true.
     * @return \Zend\View\Helper\Navigation  fluent interface, returns self
     */
    public function setInjectContainer($injectContainer = true)
    {
        $this->_injectContainer = (bool) $injectContainer;
        return $this;
    }

    /**
     * Returns whether container should be injected when proxying
     *
     * @return bool  whether container should be injected when proxying
     */
    public function getInjectContainer()
    {
        return $this->_injectContainer;
    }

    /**
     * Sets whether ACL should be injected when proxying
     *
     * @param  bool $injectAcl              [optional] whether ACL should be
     *                                      injected when proxying. Default is
     *                                      true.
     * @return \Zend\View\Helper\Navigation  fluent interface, returns self
     */
    public function setInjectAcl($injectAcl = true)
    {
        $this->_injectAcl = (bool) $injectAcl;
        return $this;
    }

    /**
     * Returns whether ACL should be injected when proxying
     *
     * @return bool  whether ACL should be injected when proxying
     */
    public function getInjectAcl()
    {
        return $this->_injectAcl;
    }

    /**
     * Sets whether translator should be injected when proxying
     *
     * @param  bool $injectTranslator       [optional] whether translator should
     *                                      be injected when proxying. Default
     *                                      is true.
     * @return Navigation  fluent interface, returns self
     */
    public function setInjectTranslator($injectTranslator = true)
    {
        $this->_injectTranslator = (bool) $injectTranslator;
        return $this;
    }

    /**
     * Returns whether translator should be injected when proxying
     *
     * @return bool  whether translator should be injected when proxying
     */
    public function getInjectTranslator()
    {
        return $this->_injectTranslator;
    }

    // Zend\View\Helper\Navigation\Helper:

    /**
     * Renders helper
     *
     * @param  \Zend\Navigation\Container $container  [optional] container to
     *                                               render. Default is to
     *                                               render the container
     *                                               registered in the helper.
     * @return string                                helper output
     * @throws \Zend\Loader\PluginLoader\Exception    if helper cannot be found
     * @throws \Zend\View\Exception\ExceptionInterface                   if helper doesn't implement
     *                                               the interface specified in
     *                                               {@link findHelper()}
     */
    public function render(Container $container = null)
    {
        $helper = $this->findHelper($this->getDefaultProxy());
        return $helper->render($container);
    }
}
