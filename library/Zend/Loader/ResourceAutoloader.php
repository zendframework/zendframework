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
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Loader;

/**
 * Resource loader
 *
 * @catebory   Zend
 * @package    Zend_Loader
 * @subpackage Autoloader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ResourceAutoloader implements SplAutoloader
{
    /**
     * @var string Base path to resource classes
     */
    protected $_basePath;

    /**
     * @var array Components handled within this resource
     */
    protected $_components = array();

    /**
     * @var string Default resource/component to use when using object registry
     */
    protected $_defaultResourceType;

    /**
     * @var string Namespace of classes within this resource
     */
    protected $_namespace;

    /**
     * @var string Prefix of classes within this resource
     */
    protected $_prefix;

    /**
     * @var array Available resource types handled by this resource autoloader
     */
    protected $_resourceTypes = array();

    /**
     * Constructor
     *
     * @param  array|Traversable $options Configuration options for resource autoloader
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function __construct($options = null)
    {
        if (null === $options) {
            throw new Exception\InvalidArgumentException('Options must be passed to resource loader constructor');
        }

        $this->setOptions($options);

        $namespace = $this->getNamespace();
        $prefix    = $this->getPrefix();
        if (((null === $namespace) || (null === $this->getBasePath()))
            && ((null === $prefix) || (null === $this->getBasePath()))
        ) {
            throw new Exception\InvalidArgumentException('Resource loader requires both a base path and either a namespace or prefix for initialization');
        }
    }

    /**
     * Overloading: methods
     *
     * Allow retrieving concrete resource object instances using 'get<Resourcename>()'
     * syntax. Example:
     * <code>
     * $loader = new ResourceAutoloader(array(
     *     'namespace' => 'Stuff',
     *     'basePath'  => '/path/to/some/stuff',
     * ))
     * $loader->addResourceType('Model', 'models', 'Model');
     *
     * $foo = $loader->getModel('Foo'); // get instance of Stuff_Model_Foo class
     * </code>
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception\InvalidArgumentException if method not beginning with 'get' or not matching a valid resource type is called
     * @throws Exception\BadMethodCallException
     */
    public function __call($method, $args)
    {
        if ('get' == substr($method, 0, 3)) {
            $type  = strtolower(substr($method, 3));
            if (!$this->hasResourceType($type)) {
                throw new Exception\InvalidArgumentException("Invalid resource type $type; cannot load resource");
            }
            if (empty($args)) {
                throw new Exception\InvalidArgumentException("Cannot load resources; no resource specified");
            }
            $resource = array_shift($args);
            return $this->load($resource, $type);
        }

        throw new Exception\BadMethodCallException("Method '$method' is not supported");
    }

    /**
     * Helper method to calculate the correct class path
     *
     * @param string $class
     * @return False if not matched other wise the correct path
     */
    public function getClassPath($class)
    {
        if (null !== $this->getNamespace()) {
            if (false !== ($path = $this->getNamespacedClassPath($class))) {
                return $path;
            }
        }
        return $this->getPrefixedClassPath($class);
    }

    /**
     * Lookup class path via namespaces
     * 
     * @param  string $class 
     * @return false|string
     */
    public function getNamespacedClassPath($class)
    {
        $class             = ltrim($class, '\\');
        $segments          = explode('\\', $class);
        $namespaceTopLevel = $this->getNamespace();
        $namespace         = '';


        if (!empty($namespaceTopLevel)) {
            $namespace = array_shift($segments);
            if ($namespace != $namespaceTopLevel) {
                // wrong namespace? we're done
                return false;
            }
        }

        if (count($segments) < 2) {
            // assumes all resources have a component and class name, minimum
            return false;
        }

        $final     = array_pop($segments);
        $component = $namespace;
        $lastMatch = false;
        do {
            $segment    = array_shift($segments);
            $component .= empty($component) ? $segment : '\\' . $segment;
            if (isset($this->_components[$component])) {
                $lastMatch = $component;
            }
        } while (count($segments));

        if (!$lastMatch) {
            return false;
        }

        $final = substr($class, strlen($lastMatch) + 1);
        $path = $this->_components[$lastMatch];
        $classPath = $path . '/' . str_replace(array('\\', '_'), '/', $final) . '.php';

        if (\Zend\Loader::isReadable($classPath)) {
            return $classPath;
        }

        return false;
    }

    /**
     * Get class path for class with vendor prefix
     * 
     * @param  string $class 
     * @return false|string
     */
    public function getPrefixedClassPath($class)
    {
        $segments       = explode('_', $class);
        $prefixTopLevel = $this->getPrefix();
        $prefix         = '';

        if (!empty($prefixTopLevel)) {
            $prefix = array_shift($segments);
            if ($prefix != $prefixTopLevel) {
                // wrong prefix? we're done
                return false;
            }
        }

        if (count($segments) < 2) {
            // assumes all resources have a component and class name, minimum
            return false;
        }

        $final     = array_pop($segments);
        $component = $prefix;
        $lastMatch = false;
        do {
            $segment    = array_shift($segments);
            $component .= empty($component) ? $segment : '_' . $segment;
            if (isset($this->_components[$component])) {
                $lastMatch = $component;
            }
        } while (count($segments));

        if (!$lastMatch) {
            return false;
        }

        $final = substr($class, strlen($lastMatch) + 1);
        $path = $this->_components[$lastMatch];
        $classPath = $path . '/' . str_replace('_', '/', $final) . '.php';

        if (\Zend\Loader::isReadable($classPath)) {
            return $classPath;
        }

        return false;
    }

    /**
     * Attempt to autoload a class
     *
     * @param  string $class
     * @return mixed False if not matched, otherwise result if include operation
     */
    public function autoload($class)
    {
        $classPath = $this->getClassPath($class);
        if (false !== $classPath) {
            return include $classPath;
        }
        return false;
    }

    /**
     * Register with spl_autoload registry
     * 
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Set class state from options
     *
     * @param  array $options
     * @throws Exception\InvalidArgumentExceptions
     * @return \Zend\Loader\Autoloader\Resource
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !($options instanceof \Traversable)) {
            throw new Exception\InvalidArgumentException('Options must be an array or Traversable');
        }

        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set namespace that this autoloader handles
     *
     * @param  string $namespace
     * @return ResourceAutoloader
     */
    public function setNamespace($namespace)
    {
        if (null === $namespace) {
            $this->_namespace = null;
            return $this;
        }

        $this->_namespace = rtrim((string) $namespace, '\\');
        $this->_namespace = rtrim($this->_namespace, '_');
        return $this;
    }

    /**
     * Get namespace this autoloader handles
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Set class prefix that this autoloader handles
     *
     * @param  string $prefix
     * @return ResourceAutoloader
     */
    public function setPrefix($prefix)
    {
        if (null === $prefix) {
            $this->_prefix = null;
            return $this;
        }

        $this->_prefix = rtrim((string) $prefix, '_');
        $this->_prefix = rtrim($this->_prefix, '\\');
        return $this;
    }

    /**
     * Get prefix this autoloader handles
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Set base path for this set of resources
     *
     * @param  string $path
     * @return Zend_Loader_Autoloader_Resource
     */
    public function setBasePath($path)
    {
        $this->_basePath = (string) $path;
        return $this;
    }

    /**
     * Get base path to this set of resources
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Add resource type
     *
     * @param  string $type identifier for the resource type being loaded
     * @param  string $path path relative to resource base path containing the resource types
     * @param  null|string $namespace sub-component namespace to append to base namespace that qualifies this resource type
     * @throws Exception\MissingResourceNamespaceException
     * @throws Exception\InvalidPathException
     * @return Zend_Loader_Autoloader_Resource
     */
    public function addResourceType($type, $path, $namespace = null)
    {
        $type = strtolower($type);
        if (!isset($this->_resourceTypes[$type])) {
            if (null === $namespace) {
                throw new Exception\MissingResourceNamespaceException('Initial definition of a resource type must include a namespace');
            }
            if (null !== $this->getNamespace()) {
                $this->_addNamespaceResource($type, $namespace);
            } else {
                $this->_addPrefixResource($type, $namespace);
            }
        }
        if (!is_string($path)) {
            throw new Exception\InvalidPathException('Invalid path specification provided; must be string');
        }
        $this->_resourceTypes[$type]['path'] = $this->getBasePath() . '/' . rtrim($path, '\/');

        $component = $this->_resourceTypes[$type]['namespace'];
        $this->_components[$component] = $this->_resourceTypes[$type]['path'];
        return $this;
    }

    /**
     * Add a resource type using PHP namespaces
     * 
     * @param  string $type 
     * @param  string $namespace 
     * @return void
     */
    protected function _addNamespaceResource($type, $namespace)
    {
        $namespaceTopLevel = $this->getNamespace();
        $namespace = ucfirst(trim($namespace, '\\'));
        $this->_resourceTypes[$type] = array(
            'namespace' => empty($namespaceTopLevel) ? $namespace : $namespaceTopLevel . '\\' . $namespace,
        );
    }

    /**
     * Add a resource type using vendor prefix
     * 
     * @param  string $type 
     * @param  string $prefix 
     * @return void
     */
    protected function _addPrefixResource($type, $prefix)
    {
        $prefixTopLevel = $this->getPrefix();
        $prefix = ucfirst(trim($prefix, '_'));
        $this->_resourceTypes[$type] = array(
            'namespace' => empty($prefixTopLevel) ? $prefix : $prefixTopLevel . '_' . $prefix,
        );
    }

    /**
     * Add multiple resources at once
     *
     * $types should be an associative array of resource type => specification
     * pairs. Each specification should be an associative array containing
     * minimally the 'path' key (specifying the path relative to the resource
     * base path) and optionally the 'namespace' key (indicating the subcomponent
     * namespace to append to the resource namespace).
     *
     * As an example:
     * <code>
     * $loader->addResourceTypes(array(
     *     'model' => array(
     *         'path'      => 'models',
     *         'namespace' => 'Model',
     *     ),
     *     'form' => array(
     *         'path'      => 'forms',
     *         'namespace' => 'Form',
     *     ),
     * ));
     * </code>
     *
     * @param  array $types
     * @throws Exception\InvalidArgumentException
     * @return \Zend\Loader\Autoloader\Resource
     */
    public function addResourceTypes(array $types)
    {
        foreach ($types as $type => $spec) {
            if (!is_array($spec)) {
                throw new Exception\InvalidArgumentException('addResourceTypes() expects an array of arrays');
            }
            if (!isset($spec['path'])) {
                throw new Exception\InvalidArgumentException('addResourceTypes() expects each array to include a paths element');
            }
            $paths  = $spec['path'];
            $namespace = null;
            if (isset($spec['namespace'])) {
                $namespace = $spec['namespace'];
            }
            $this->addResourceType($type, $paths, $namespace);
        }
        return $this;
    }

    /**
     * Overwrite existing and set multiple resource types at once
     *
     * @see    Zend_Loader_Autoloader_Resource::addResourceTypes()
     * @param  array $types
     * @return Zend_Loader_Autoloader_Resource
     */
    public function setResourceTypes(array $types)
    {
        $this->clearResourceTypes();
        return $this->addResourceTypes($types);
    }

    /**
     * Retrieve resource type mappings
     *
     * @return array
     */
    public function getResourceTypes()
    {
        return $this->_resourceTypes;
    }

    /**
     * Is the requested resource type defined?
     *
     * @param  string $type
     * @return bool
     */
    public function hasResourceType($type)
    {
        return isset($this->_resourceTypes[$type]);
    }

    /**
     * Remove the requested resource type
     *
     * @param  string $type
     * @return Zend_Loader_Autoloader_Resource
     */
    public function removeResourceType($type)
    {
        if ($this->hasResourceType($type)) {
            $namespace = $this->_resourceTypes[$type]['namespace'];
            unset($this->_components[$namespace]);
            unset($this->_resourceTypes[$type]);
        }
        return $this;
    }

    /**
     * Clear all resource types
     *
     * @return Zend_Loader_Autoloader_Resource
     */
    public function clearResourceTypes()
    {
        $this->_resourceTypes = array();
        $this->_components    = array();
        return $this;
    }

    /**
     * Set default resource type to use when calling load()
     *
     * @param  string $type
     * @return Zend_Loader_Autoloader_Resource
     */
    public function setDefaultResourceType($type)
    {
        if ($this->hasResourceType($type)) {
            $this->_defaultResourceType = $type;
        }
        return $this;
    }

    /**
     * Get default resource type to use when calling load()
     *
     * @return string|null
     */
    public function getDefaultResourceType()
    {
        return $this->_defaultResourceType;
    }

    /**
     * Object registry and factory
     *
     * Loads the requested resource of type $type (or uses the default resource
     * type if none provided). If the resource has been loaded previously,
     * returns the previous instance; otherwise, instantiates it.
     *
     * @param  string $resource
     * @param  string $type
     * @return object
     * @throws Exception\InvalidArgumentException if resource type not specified or invalid
     */
    public function load($resource, $type = null)
    {
        if (null === $type) {
            $type = $this->getDefaultResourceType();
            if (empty($type)) {
                throw new Exception\InvalidArgumentException('No resource type specified');
            }
        }
        if (!$this->hasResourceType($type)) {
            throw new Exception\InvalidArgumentException('Invalid resource type specified');
        }
        $namespace = $this->_resourceTypes[$type]['namespace'];
        if (null !== $this->getNamespace()) {
            $class     = $namespace . '\\' . ucfirst($resource);
        } elseif (null !== $this->getPrefix()) {
            $class     = $namespace . '_' . ucfirst($resource);
        }

        if (!isset($this->_resources[$class])) {
            $this->_resources[$class] = new $class;
        }
        return $this->_resources[$class];
    }
}
