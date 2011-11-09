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
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Layout;

use Traversable,
    Zend\Config\Config,
    Zend\Filter,
    Zend\View\Helper\Placeholder\Registry as PlaceholderRegistry,
    Zend\View\PhpRenderer,
    Zend\View\Renderer;

/**
 * Provide Layout support for MVC applications
 *
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Layout
{
    /**
     * Placeholder container for layout variables
     * @var \Zend\View\Helper\Placeholder\Container
     */
    protected $container;

    /**
     * Key used to store content from 'default' named response segment
     * @var string
     */
    protected $contentKey = 'content';

    /**
     * Are layouts enabled?
     * @var bool
     */
    protected $enabled = true;

    /**
     * Inflector used to resolve layout script
     * @var Filter\Inflector
     */
    protected $inflector;

    /**
     * Flag: is inflector enabled?
     * @var bool
     */
    protected $inflectorEnabled = true;

    /**
     * Inflector target
     * @var string
     */
    protected $inflectorTarget = ':script.:suffix';

    /**
     * Layout view
     * @var string
     */
    protected $layout = 'layout';

    /**
     * Layout view script path
     * @var string
     */
    protected $viewScriptPath = null;

    protected $viewBasePath = null;
    protected $viewBasePrefix = 'Layout\View';

    /**
     * @var \Zend\View\Renderer
     */
    protected $view;

    /**
     * View script suffix for layout script
     * @var string
     */
    protected $viewSuffix = 'phtml';

    /**
     * Constructor
     *
     * Accepts either:
     * - A string path to layouts
     * - An array of options
     * - A Zend_Config object with options
     *
     * Layout script path, either as argument or as key in options, is
     * required.
     *
     * If mvcEnabled flag is false from options, simply sets layout script path.
     * Otherwise, also instantiates and registers action helper and controller
     * plugin.
     *
     * @param  string|array|Traversable $options
     * @throws Exception\InvalidArgumentException on invalid $options argument
     * @return void
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            if (is_string($options)) {
                $this->setLayoutPath($options);
            } elseif (is_array($options) || ($options instanceof Traversable)) {
                $this->setOptions($options);
            } else {
                throw new Exception\InvalidArgumentException('Invalid option provided to constructor');
            }
        }

        $this->initVarContainer();
    }

    /**
     * Set options en masse
     *
     * @param  array|Traversable $options
     * @return Layout
     * @throws Exception\InvalidArgumentException on invalid $options argument
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !($options instanceof Traversable)) {
            throw new Exception\InvalidArgumentException('setOptions() expects either an array or a Traversable object');
        }

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Set options from a config object
     *
     * @param  Config $config
     * @return Layout
     */
    public function setConfig(Config $config)
    {
        $this->setOptions($config);
        return $this;
    }

    /**
     * Initialize placeholder container for layout vars
     *
     * @return \Zend\View\Helper\Placeholder\Container
     */
    protected function initVarContainer()
    {
        if (null === $this->container) {
            $this->container = PlaceholderRegistry::getRegistry()->getContainer(get_called_class());
        }

        return $this->container;
    }

    /**
     * Set layout script to use
     *
     * Note: enables layout by default, can be disabled
     *
     * @param  string $name
     * @param  boolean $enabled
     * @return Layout
     */
    public function setLayout($name, $enabled = true)
    {
        $this->layout = (string) $name;
        if ($enabled) {
            $this->enableLayout();
        }
        return $this;
    }

    /**
     * Get current layout script
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Disable layout
     *
     * @return Layout
     */
    public function disableLayout()
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * Enable layout
     *
     * @return Layout
     */
    public function enableLayout()
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * Is layout enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the base path for the view
     * 
     * @param  string $path 
     * @param  string $prefix Optional prefix for helpers and filters in this path
     * @return void
     */
    public function setViewBasePath($path, $prefix = 'Layout\View')
    {
        $this->viewBasePath   = $path;
        $this->viewBasePrefix = $prefix;
        return $this;
    }

    /**
     * Retrieve the base view path
     * 
     * @return string
     */
    public function getViewBasePath()
    {
        return $this->viewBasePath;
    }

    /**
     * Set the view script path
     * 
     * @param  string $path 
     * @return Layout
     */
    public function setViewScriptPath($path)
    {
        $this->viewScriptPath = $path;
        return $this;
    }

    /**
     * Retrieve the view script path
     * 
     * @return string
     */
    public function getViewScriptPath()
    {
        return $this->viewScriptPath;
    }

    /**
     * Set layout script path
     *
     * @param  string $path
     * @return Layout
     */
    public function setLayoutPath($path)
    {
        return $this->setViewScriptPath($path);
    }

    /**
     * Get current layout script path
     *
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->getViewScriptPath();
    }

    /**
     * Set content key
     *
     * Key in namespace container denoting default content
     *
     * @param  string $contentKey
     * @return Layout
     */
    public function setContentKey($contentKey)
    {
        $this->contentKey = (string) $contentKey;
        return $this;
    }

    /**
     * Retrieve content key
     *
     * @return string
     */
    public function getContentKey()
    {
        return $this->contentKey;
    }

    /**
     * Set view object
     *
     * @param  Renderer $view
     * @return Layout
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get current view object
     *
     * If no view object currently set, instantiates a PhpRenderer instance.
     *
     * @return Renderer
     */
    public function getView()
    {
        if (null === $this->view) {
            $this->setView(new PhpRenderer());
        }
        return $this->view;
    }

    /**
     * Set layout view script suffix
     *
     * @param  string $viewSuffix
     * @return Layout
     */
    public function setViewSuffix($viewSuffix)
    {
        $this->viewSuffix = (string) $viewSuffix;
        return $this;
    }

    /**
     * Retrieve layout view script suffix
     *
     * @return string
     */
    public function getViewSuffix()
    {
        return $this->viewSuffix;
    }

    /**
     * Retrieve inflector target
     *
     * @return string
     */
    public function getInflectorTarget()
    {
        return $this->inflectorTarget;
    }

    /**
     * Set inflector target
     *
     * @param  string $inflectorTarget
     * @return Layout
     */
    public function setInflectorTarget($inflectorTarget)
    {
        $this->inflectorTarget = (string) $inflectorTarget;
        return $this;
    }

    /**
     * Set inflector to use when resolving layout names
     *
     * @param  Filter\Inflector $inflector
     * @return Layout
     */
    public function setInflector(Filter\Inflector $inflector)
    {
        $this->inflector = $inflector;
        return $this;
    }

    /**
     * Retrieve inflector
     *
     * @return Filter\Inflector
     */
    public function getInflector()
    {
        if (null === $this->inflector) {
            $inflector = new Filter\Inflector();
            $inflector->setTargetReference($this->inflectorTarget)
                      ->addRules(array(':script' => array('Word\CamelCaseToDash', 'StringToLower')))
                      ->setStaticRuleReference('suffix', $this->viewSuffix);
            $this->setInflector($inflector);
        }

        return $this->inflector;
    }

    /**
     * Enable inflector
     *
     * @return Layout
     */
    public function enableInflector()
    {
        $this->inflectorEnabled = true;
        return $this;
    }

    /**
     * Disable inflector
     *
     * @return Layout
     */
    public function disableInflector()
    {
        $this->inflectorEnabled = false;
        return $this;
    }

    /**
     * Return status of inflector enabled flag
     *
     * @return bool
     */
    public function inflectorEnabled()
    {
        return $this->inflectorEnabled;
    }

    /**
     * Set layout variable
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * Get layout variable
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->container[$key])) {
            return $this->container[$key];
        }

        return null;
    }

    /**
     * Is a layout variable set?
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return (isset($this->container[$key]));
    }

    /**
     * Unset a layout variable?
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        if (isset($this->container[$key])) {
            unset($this->container[$key]);
        }
    }

    /**
     * Assign one or more layout variables
     *
     * @param  mixed $spec Assoc array or string key; if assoc array, sets each
     *                     key as a layout variable
     * @param  mixed $value Value if $spec is a key
     * @return Layout
     * @throws Exception\InvalidArgumentException if non-array/string value passed to $spec
     */
    public function assign($spec, $value = null)
    {
        if (!is_array($spec) && !is_string($spec)) {
            throw new Exception\InvalidArgumentException('Invalid values passed to assign()');
        }

        if (is_array($spec)) {
            $orig   = $this->container->getArrayCopy();
            $merged = array_merge($orig, $spec);
            $this->container->exchangeArray($merged);
            return $this;
        }

        $this->container[$spec] = $value;
        return $this;
    }

    /**
     * Render layout
     *
     * Sets internal script path as last path on script path stack, assigns
     * layout variables to view, determines layout name using inflector, and
     * renders layout view script.
     *
     * $name will be passed to the inflector as the key 'script'.
     *
     * @param  mixed $name
     * @return mixed
     */
    public function render($name = null)
    {
        if (null === $name) {
            $name = $this->getLayout();
        }

        if ($this->inflectorEnabled() 
            && (null !== ($inflector = $this->getInflector()))
        ) {
            $name = $this->inflector->filter(array('script' => $name));
        }

        $view = $this->getView();

        if (null !== ($path = $this->getViewScriptPath())) {
            if ($view instanceof PhpRenderer) {
                $view->resolver()->addPath($path);
            }
        } elseif (null !== ($path = $this->getViewBasePath())) {
            if ($view instanceof PhpRenderer) {
                $view->resolver()->addPath($path . '/scripts');
            }
        }

        return $view->render($name);
    }
}
