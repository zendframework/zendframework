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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Layout;
use Zend\Config;
use Zend\Controller;
use Zend\Controller\Action\HelperBroker;
use Zend\Filter;

/**
 * Provide Layout support for MVC applications
 *
 * @uses       \Zend\Controller\Action\HelperBroker
 * @uses       \Zend\Controller\Front
 * @uses       \Zend\Filter\Inflector
 * @uses       \Zend\Layout\Exception
 * @uses       \Zend\Loader
 * @uses       \Zend\View\Helper\Placeholder\Registry
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Layout
{
    /**
     * Placeholder container for layout variables
     * @var \Zend\View\Helper\Placeholder\Container
     */
    protected $_container;

    /**
     * Key used to store content from 'default' named response segment
     * @var string
     */
    protected $_contentKey = 'content';

    /**
     * Are layouts enabled?
     * @var bool
     */
    protected $_enabled = true;

    /**
     * Helper class
     * @var string
     */
    protected $_helperClass = 'Zend\Layout\Controller\Action\Helper\Layout';

    /**
     * Inflector used to resolve layout script
     * @var \Zend\Filter\Inflector
     */
    protected $_inflector;

    /**
     * Flag: is inflector enabled?
     * @var bool
     */
    protected $_inflectorEnabled = true;

    /**
     * Inflector target
     * @var string
     */
    protected $_inflectorTarget = ':script.:suffix';

    /**
     * Layout view
     * @var string
     */
    protected $_layout = 'layout';

    /**
     * Layout view script path
     * @var string
     */
    protected $_viewScriptPath = null;

    protected $_viewBasePath = null;
    protected $_viewBasePrefix = 'Layout\View';

    /**
     * Flag: is MVC integration enabled?
     * @var bool
     */
    protected $_mvcEnabled = true;

    /**
     * Instance registered with MVC, if any
     * @var \Zend\Layout\Layout
     */
    protected static $_mvcInstance;

    /**
     * Flag: is MVC successful action only flag set?
     * @var bool
     */
    protected $_mvcSuccessfulActionOnly = true;

    /**
     * Plugin class
     * @var string
     */
    protected $_pluginClass = 'Zend\Layout\Controller\Plugin\Layout';

    /**
     * @var \Zend\View\Renderer
     */
    protected $_view;

    /**
     * View script suffix for layout script
     * @var string
     */
    protected $_viewSuffix = 'phtml';

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
     * @param  string|array|\Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = null, $initMvc = false)
    {
        if (null !== $options) {
            if (is_string($options)) {
                $this->setLayoutPath($options);
            } elseif (is_array($options)) {
                $this->setOptions($options);
            } elseif ($options instanceof Config\Config) {
                $this->setConfig($options);
            } else {
                throw new Exception('Invalid option provided to constructor');
            }
        }

        $this->_initVarContainer();

        if ($initMvc) {
            $this->_setMvcEnabled(true);
            $this->_initMvc();
        } else {
            $this->_setMvcEnabled(false);
        }
    }

    /**
     * Static method for initialization with MVC support
     *
     * @param  string|array|\Zend\Config\Config $options
     * @return \Zend\Layout\Layout
     */
    public static function startMvc($options = null)
    {
        if (null === self::$_mvcInstance) {
            self::$_mvcInstance = new self($options, true);
        }

        if (is_string($options)) {
            self::$_mvcInstance->setLayoutPath($options);
        } elseif (is_array($options) || $options instanceof Config\Config) {
            self::$_mvcInstance->setOptions($options);
        }

        return self::$_mvcInstance;
    }

    /**
     * Retrieve MVC instance of Zend_Layout object
     *
     * @return \Zend\Layout\Layout|null
     */
    public static function getMvcInstance()
    {
        return self::$_mvcInstance;
    }

    /**
     * Reset MVC instance
     *
     * Unregisters plugins and helpers, and destroys MVC layout instance.
     *
     * @return void
     */
    public static function resetMvcInstance()
    {
        if (null !== self::$_mvcInstance) {
            $layout = self::$_mvcInstance;
            $pluginClass = $layout->getPluginClass();
            $front = Controller\Front::getInstance();
            if ($front->hasPlugin($pluginClass)) {
                $front->unregisterPlugin($pluginClass);
            }

            $broker = $front->getHelperBroker();
            if ($broker->hasPlugin('layout')) {
                $broker->unregister('layout');
            }

            unset($layout);
            self::$_mvcInstance = null;
        }
    }

    /**
     * Set options en masse
     *
     * @param  array|\Zend\Config\Config $options
     * @return void
     */
    public function setOptions($options)
    {
        if ($options instanceof Config\Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            throw new Exception('setOptions() expects either an array or a Zend_Config object');
        }

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Initialize MVC integration
     *
     * @return void
     */
    protected function _initMvc()
    {
        $this->_initPlugin();
        $this->_initHelper();
    }

    /**
     * Initialize front controller plugin
     *
     * @return void
     */
    protected function _initPlugin()
    {
        $pluginClass = $this->getPluginClass();
        $front = Controller\Front::getInstance();
        if (!$front->hasPlugin($pluginClass)) {
            if (!class_exists($pluginClass)) {
                \Zend\Loader::loadClass($pluginClass);
            }
            $front->registerPlugin(
                // register to run last | BUT before the ErrorHandler (if its available)
                new $pluginClass($this),
                99
            );
        }
    }

    /**
     * Initialize action helper
     *
     * @return void
     */
    protected function _initHelper()
    {
        $helperClass = $this->getHelperClass();
        $front       = Controller\Front::getInstance();
        $broker      = $front->getHelperBroker();
        if (!$broker->hasPlugin('layout')) {
            $helper = new $helperClass($this);
            $broker->register('layout', $helper);
            if ($broker instanceof Controller\Action\HelperBroker) {
                $broker->getStack()->offsetSet(-90, $helper);
            }
        }
    }

    /**
     * Set options from a config object
     *
     * @param  \Zend\Config\Config $config
     * @return \Zend\Layout\Layout
     */
    public function setConfig(Config\Config $config)
    {
        $this->setOptions($config->toArray());
        return $this;
    }

    /**
     * Initialize placeholder container for layout vars
     *
     * @return \Zend\View\Helper\Placeholder\Container
     */
    protected function _initVarContainer()
    {
        if (null === $this->_container) {
            $this->_container = \Zend\View\Helper\Placeholder\Registry::getRegistry()->getContainer(__CLASS__);
        }

        return $this->_container;
    }

    /**
     * Set layout script to use
     *
     * Note: enables layout by default, can be disabled
     *
     * @param  string $name
     * @param  boolean $enabled
     * @return \Zend\Layout\Layout
     */
    public function setLayout($name, $enabled = true)
    {
        $this->_layout = (string) $name;
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
        return $this->_layout;
    }

    /**
     * Disable layout
     *
     * @return \Zend\Layout\Layout
     */
    public function disableLayout()
    {
        $this->_enabled = false;
        return $this;
    }

    /**
     * Enable layout
     *
     * @return \Zend\Layout\Layout
     */
    public function enableLayout()
    {
        $this->_enabled = true;
        return $this;
    }

    /**
     * Is layout enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }


    public function setViewBasePath($path, $prefix = 'Layout\View')
    {
        $this->_viewBasePath = $path;
        $this->_viewBasePrefix = $prefix;
        return $this;
    }

    public function getViewBasePath()
    {
        return $this->_viewBasePath;
    }

    public function setViewScriptPath($path)
    {
        $this->_viewScriptPath = $path;
        return $this;
    }

    public function getViewScriptPath()
    {
        return $this->_viewScriptPath;
    }

    /**
     * Set layout script path
     *
     * @param  string $path
     * @return \Zend\Layout\Layout
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
     * @return \Zend\Layout\Layout
     */
    public function setContentKey($contentKey)
    {
        $this->_contentKey = (string) $contentKey;
        return $this;
    }

    /**
     * Retrieve content key
     *
     * @return string
     */
    public function getContentKey()
    {
        return $this->_contentKey;
    }

    /**
     * Set MVC enabled flag
     *
     * @param  bool $mvcEnabled
     * @return \Zend\Layout\Layout
     */
    protected function _setMvcEnabled($mvcEnabled)
    {
        $this->_mvcEnabled = ($mvcEnabled) ? true : false;
        return $this;
    }

    /**
     * Retrieve MVC enabled flag
     *
     * @return bool
     */
    public function getMvcEnabled()
    {
        return $this->_mvcEnabled;
    }

    /**
     * Set MVC Successful Action Only flag
     *
     * @param bool $successfulActionOnly
     * @return \Zend\Layout\Layout
     */
    public function setMvcSuccessfulActionOnly($successfulActionOnly)
    {
        $this->_mvcSuccessfulActionOnly = ($successfulActionOnly) ? true : false;
        return $this;
    }

    /**
     * Get MVC Successful Action Only Flag
     *
     * @return bool
     */
    public function getMvcSuccessfulActionOnly()
    {
        return $this->_mvcSuccessfulActionOnly;
    }

    /**
     * Set view object
     *
     * @param  \Zend\View\Renderer $view
     * @return \Zend\Layout\Layout
     */
    public function setView(\Zend\View\Renderer $view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Retrieve helper class
     *
     * @return string
     */
    public function getHelperClass()
    {
        return $this->_helperClass;
    }

    /**
     * Set helper class
     *
     * @param  string $helperClass
     * @return \Zend\Layout\Layout
     */
    public function setHelperClass($helperClass)
    {
        $this->_helperClass = (string) $helperClass;
        return $this;
    }

    /**
     * Retrieve plugin class
     *
     * @return string
     */
    public function getPluginClass()
    {
        return $this->_pluginClass;
    }

    /**
     * Set plugin class
     *
     * @param  string $pluginClass
     * @return \Zend\Layout\Layout
     */
    public function setPluginClass($pluginClass)
    {
        $this->_pluginClass = (string) $pluginClass;
        return $this;
    }

    /**
     * Get current view object
     *
     * If no view object currently set, retrieves it from the ViewRenderer.
     *
     * @todo Set inflector from view renderer at same time
     * @return \Zend\View\Renderer
     */
    public function getView()
    {
        if (null === $this->_view) {
            $front  = Controller\Front::getInstance();
            $broker = $front->getHelperBroker();
            $viewRenderer = $broker->load('viewRenderer');
            if (null === $viewRenderer->view) {
                $viewRenderer->initView();
            }
            $this->setView($viewRenderer->view);
        }
        return $this->_view;
    }

    /**
     * Set layout view script suffix
     *
     * @param  string $viewSuffix
     * @return \Zend\Layout\Layout
     */
    public function setViewSuffix($viewSuffix)
    {
        $this->_viewSuffix = (string) $viewSuffix;
        return $this;
    }

    /**
     * Retrieve layout view script suffix
     *
     * @return string
     */
    public function getViewSuffix()
    {
        return $this->_viewSuffix;
    }

    /**
     * Retrieve inflector target
     *
     * @return string
     */
    public function getInflectorTarget()
    {
        return $this->_inflectorTarget;
    }

    /**
     * Set inflector target
     *
     * @param  string $inflectorTarget
     * @return \Zend\Layout\Layout
     */
    public function setInflectorTarget($inflectorTarget)
    {
        $this->_inflectorTarget = (string) $inflectorTarget;
        return $this;
    }

    /**
     * Set inflector to use when resolving layout names
     *
     * @param  \Zend\Filter\Inflector $inflector
     * @return \Zend\Layout\Layout
     */
    public function setInflector(Filter\Inflector $inflector)
    {
        $this->_inflector = $inflector;
        return $this;
    }

    /**
     * Retrieve inflector
     *
     * @return \Zend\Filter\Inflector
     */
    public function getInflector()
    {
        if (null === $this->_inflector) {
            $inflector = new Filter\Inflector();
            $inflector->setTargetReference($this->_inflectorTarget)
                      ->addRules(array(':script' => array('Word\CamelCaseToDash', 'StringToLower')))
                      ->setStaticRuleReference('suffix', $this->_viewSuffix);
            $this->setInflector($inflector);
        }

        return $this->_inflector;
    }

    /**
     * Enable inflector
     *
     * @return \Zend\Layout\Layout
     */
    public function enableInflector()
    {
        $this->_inflectorEnabled = true;
        return $this;
    }

    /**
     * Disable inflector
     *
     * @return \Zend\Layout\Layout
     */
    public function disableInflector()
    {
        $this->_inflectorEnabled = false;
        return $this;
    }

    /**
     * Return status of inflector enabled flag
     *
     * @return bool
     */
    public function inflectorEnabled()
    {
        return $this->_inflectorEnabled;
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
        $this->_container[$key] = $value;
    }

    /**
     * Get layout variable
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->_container[$key])) {
            return $this->_container[$key];
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
        return (isset($this->_container[$key]));
    }

    /**
     * Unset a layout variable?
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        if (isset($this->_container[$key])) {
            unset($this->_container[$key]);
        }
    }

    /**
     * Assign one or more layout variables
     *
     * @param  mixed $spec Assoc array or string key; if assoc array, sets each
     * key as a layout variable
     * @param  mixed $value Value if $spec is a key
     * @return \Zend\Layout\Layout
     * @throws \Zend\Layout\Exception if non-array/string value passed to $spec
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $orig = $this->_container->getArrayCopy();
            $merged = array_merge($orig, $spec);
            $this->_container->exchangeArray($merged);
            return $this;
        }

        if (is_string($spec)) {
            $this->_container[$spec] = $value;
            return $this;
        }

        throw new Exception('Invalid values passed to assign()');
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

        if ($this->inflectorEnabled() && (null !== ($inflector = $this->getInflector())))
        {
            $name = $this->_inflector->filter(array('script' => $name));
        }

        $view = $this->getView();

        if (null !== ($path = $this->getViewScriptPath())) {
            if ($view instanceof \Zend\View\PhpRenderer) {
                $view->resolver()->addPath($path);
            }
        } elseif (null !== ($path = $this->getViewBasePath())) {
            if ($view instanceof \Zend\View\PhpRenderer) {
                $view->resolver()->addPath($path . '/scripts');
            }
        }

        return $view->render($name);
    }
}
