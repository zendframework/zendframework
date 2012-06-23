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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Loader;

use Zend\Stdlib\ArrayStack;
use Zend\Stdlib\SplStack;
use SplDoublyLinkedList;
use SplFileInfo;

/**
 * Prefix/Path plugin loader
 *
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PrefixPathLoader implements ShortNameLocator, PrefixPathMapper
{
    /**
     * Map of class names to files
     * @var array
     */
    protected $classMap = array();

    /**
     * Map of loaded plugins to class names
     *
     * @var array
     */
    protected $pluginMap = array();

    /**
     * Instance registry property
     *
     * @var ArrayStack
     */
    protected $prefixPaths;

    /**
     * Global static overrides to merge at instantiation
     *
     * @var array
     */
    protected static $staticPaths = array();

    /**
     * Constructor
     *
     * Options are passed to {@link setOptions()}
     *
     * @param  array $options
     * @return void
     */
    public function __construct($options = null)
    {
        // Allow extending classes to pre-set the prefix paths
        if (is_array($this->prefixPaths)) {
            // If prefixPaths is an array, pass the array to addPrefixPaths() 
            // after first setting the property to an ArrayStack
            $prefixPaths = $this->prefixPaths;
            $this->prefixPaths = new ArrayStack();
            $this->addPrefixPaths($prefixPaths);
        } elseif (!$this->prefixPaths instanceof ArrayStack) {
            // If we don't have an array stack, fix that!
            $this->prefixPaths = new ArrayStack();
        }

        // Merge in static paths
        if (!empty(static::$staticPaths)) {
            $this->addPrefixPaths(static::$staticPaths);
        }

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Add global static paths to merge at instantiation
     * 
     * @param  null|array|Traversable $paths 
     * @return void
     */
    public static function addStaticPaths($paths)
    {
        if (null === $paths) {
            static::$staticPaths = array();
            return;
        }

        if (!is_array($paths) && !$paths instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected a null value, array, or Traversable object, received %s',
                (is_object($paths) ? get_class($paths) : gettype($paths))
            ));
        }

        foreach ($paths as $spec) {
            if (!is_array($spec) && !is_object($spec)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'At least one item in the paths is not an array or object (received %s); aborting population of static prefix path map',
                    (is_object($spec) ? get_class($spec) : gettype($spec))
                ));
            }
            static::$staticPaths[] = $spec;
        }
    }

    /**
     * Configure the prefix path plugin loader
     *
     * Proxies to {@link addPrefixPaths()}.
     * 
     * @param  array|\Traversable $options
     * @return PrefixPathLoader
     */
    public function setOptions($options)
    {
        $this->addPrefixPaths($options);
        return $this;
    }

    /**
     * Add prefixed paths to the registry of paths
     *
     * @param string $prefix
     * @param string $path
     * @param  bool $namespaced Whether the paths are namespaced or prefixed; namespaced by default
     * @return \Zend\Loader\PrefixPathLoader
     */
    public function addPrefixPath($prefix, $path, $namespaced = true)
    {
        if (!is_string($prefix) || !is_string($path)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected strings for prefix and path; received %s and %s, respectively',
                (is_object($prefix) ? get_class($prefix) : gettype($prefix)),
                (is_object($path)   ? get_class($path)   : gettype($path))
            ));
        }

        $prefix = $this->formatPrefix($prefix, $namespaced);
        $path   = $this->formatPath($path);

        if (!isset($this->prefixPaths[$prefix])) {
            $this->prefixPaths[$prefix] = new SplStack;
        }
        if (!in_array($path, $this->prefixPaths[$prefix]->toArray())) {
            $this->prefixPaths[$prefix][] = $path;
        }

        return $this;
    }

    /**
     * Add many prefix paths at once
     *
     * Accepts an array or Traversable object of prefix (or namspace) / path 
     * pairs. The path may either be a string path, or an array or Traversable
     * object with many paths to associate with this prefix. If adding many 
     * paths at once, please remember that the prefix/path pairs act as a LIFO 
     * stack, as does the stack of paths associated with any given prefix.
     * 
     * @param  array|Traversable $prefixPaths 
     * @return PrefixPathLoader
     */
    public function addPrefixPaths($prefixPaths)
    {
        if (!is_array($prefixPaths) && !$prefixPaths instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array or Traversable object; received %s', 
                (is_object($prefixPaths) ? get_class($prefixPaths) : gettype($prefixPaths))
            ));
        }
        foreach ($prefixPaths as $prefix => $spec) {
            if (is_object($spec)) {
                $prefix     = $spec->prefix ?: $prefix;
                $path       = $spec->path   ?: false;
                $namespaced = isset($spec->namespaced) ? (bool) $spec->namespaced : true;
            } elseif (is_array($spec)) {
                $prefix     = $spec['prefix'] ?: $prefix;
                $path       = $spec['path']   ?: false;
                $namespaced = isset($spec['namespaced']) ? (bool) $spec['namespaced'] : true;
            } elseif (is_string($spec)) {
                $path       = $spec;
                $namespaced = strstr($prefix, '_') ? false : true;
            } else {
                throw new Exception\InvalidArgumentException(
                    'Invalid prefix path array specification; must be an array or object'
                );
            }
            if (!$prefix || !$path) {
                throw new Exception\InvalidArgumentException(
                    'Invalid prefix path object specification; missing either prefix or path'
                );
            }
            $this->addPrefixPath($prefix, $path, $namespaced);
        }
        return $this;
    }

    /**
     * Get path stack
     *
     * @param  string $prefix
     * @return false|ArrayStack|SplStack False if prefix does not exist, 
     * SplStack otherwise; if no prefix provide, ArrayStack of prefix/SplStack 
     * pairs
     */
    public function getPaths($prefix = null)
    {
        if ((null !== $prefix) && is_string($prefix)) {
            $prefix = $this->formatPrefix($prefix);

            if (isset($this->prefixPaths[$prefix])) {
                return $this->prefixPaths[$prefix];
            }

            return false;
        }

        return $this->prefixPaths;
    }

    /**
     * Clear path stack
     *
     * Clears path stack for a single prefix, or all prefixes.
     *
     * @param  string $prefix
     * @return bool False only if $prefix does not exist
     */
    public function clearPaths($prefix = null)
    {
        if ((null !== $prefix) && is_string($prefix)) {
            $prefix = $this->formatPrefix($prefix);

            if (isset($this->prefixPaths[$prefix])) {
                unset($this->prefixPaths[$prefix]);
                return true;
            }

            return false;
        }

        $this->prefixPaths = new ArrayStack();

        return true;
    }

    /**
     * Remove a prefix (or prefixed-path) from the registry
     *
     * @param  string $prefix
     * @param  string $path
     * @return Zend\Loader\PrefixPathLoader
     */
    public function removePrefixPath($prefix, $path)
    {
        $prefix   = $this->formatPrefix($prefix);
        $path     = $this->formatPath($path);
        $registry = $this->prefixPaths;

        if (!isset($registry[$prefix])) {
            return false;
        }


        // Find prefix path in stack
        $index = false;
        $stack = $registry[$prefix];
        foreach ($stack as $idx => $test) {
            if ($test == $path) {
                $index = $idx;
                break;
            }
        }

        if (false === $index) {
            return false;
        }

        // Re-calculate index, since this is a stack
        $index = count($stack) - $index - 1;
        unset($stack[$index]);

        // If stack is now empty, remove prefix from ArrayStack
        if (0 === count($stack)) {
            unset($registry[$prefix]);
        }

        return true;
    }

    /**
     * Whether or not a Plugin by a specific name is loaded
     *
     * @param string $name
     * @return \Zend\Loader\PrefixPathLoader
     */
    public function isLoaded($name)
    {
        $name = $this->formatName($name);
        return isset($this->pluginMap[$name]);
    }

    /**
     * Return full class name for a named plugin
     *
     * @param string $name
     * @return string|false False if class not found, class name otherwise
     */
    public function getClassName($name)
    {
        $name = $this->formatName($name);

        if (isset($this->pluginMap[$name])) {
            return $this->pluginMap[$name];
        }

        return false;
    }

    /**
     * Load a plugin via the name provided
     *
     * @param  string $name
     * @return string|false Class name of loaded class; false if no class found
     */
    public function load($name)
    {
        $name = $this->formatName($name);
        if ($this->isLoaded($name)) {
            return $this->getClassName($name);
        }

        $found     = false;
        $classFile = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $name) . '.php';
        foreach ($this->prefixPaths as $prefix => $paths) {
            // Initialize file and class variables
            $loadFile  = false;
            $className = $prefix . $name;

            if (class_exists($className)) {
                // Class already loaded or autoloaded; done
                $found = true;
                break;
            }

            // Search path stack
            foreach ($paths as $path) {
                // Is the class file readable?
                $loadFile = new SplFileInfo($path . $classFile);
                if ($loadFile->isFile() && $loadFile->isReadable()) {
                    // File is readable, let's load and check for the class
                    include_once $loadFile->getPathName();
                    if (class_exists($className, false)) {
                        // Found!
                        $found = true;
                        break 2;
                    }
                }
                // Not found, so reset path holder
                $loadFile = false;
            }
        }

        // Plugin class not found -- return early
        if (!$found) {
            return false;
        }

        // Get class file for class map
        $fileName = null;
        if ($loadFile) {
            // We have a populated file object from searching
            $fileName = $loadFile->getPathName();
        } else {
            // Class was already loaded or autoloaded
            $r = new \ReflectionClass($className);
            $fileName = $r->getFileName();
        }

        // Seed plugin map and class map
        $this->pluginMap[$name]     = $className;
        $this->classMap[$className] = $fileName;

        return $className;
    }

    /**
     * Get plugin map
     *
     * Returns an array of plugin name/class name pairs, suitable for seeding
     * a PluginClassLoader instance.
     * 
     * @return array
     */
    public function getPluginMap()
    {
        return $this->pluginMap;
    }

    /**
     * Get class map
     *
     * Returns an array of class name/file name pairs, suitable for seeding
     * a ClassMapAutoloader instance. Note: filenames will be absolute paths
     * based on the operating system on which the class map is retrieved. You
     * may need to alter the paths to be relative to any filesystem.
     * 
     * @return array
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * Normalize plugin name
     *
     * @param  string $name
     * @return string
     */
    protected function formatName($name)
    {
        return ucfirst((string) $name);
    }

    /**
     * Format prefix for internal use
     *
     * @param  string $prefix
     * @param  bool $namespaced Whether the paths are namespaced or prefixed; 
     * namespaced by default
     * @return string
     */
    protected function formatPrefix($prefix, $namespaced = true)
    {
        if ($prefix == "") {
            return $prefix;
        }

        switch ((bool) $namespaced) {
            case true:
                $last = strlen($prefix) - 1;
                if ($prefix{$last} == '\\') {
                    return $prefix;
                }

                return $prefix . '\\';
            case false:
                $last = strlen($prefix) - 1;
                if ($prefix{$last} == '_') {
                    return $prefix;
                }

                return $prefix . '_';
            default:
                // do nothing; unknown value
        }
    }

    /**
     * Format a path for comparisons
     *
     * Strips trailing directory separator(s), if any, and then appends 
     * system directory separator.
     * 
     * @param  string $path 
     * @return string
     */
    protected function formatPath($path)
    {
        $path  = rtrim($path, '/');
        $path  = rtrim($path, '\\');
        $path .= DIRECTORY_SEPARATOR;
        return $path;
    }
}
