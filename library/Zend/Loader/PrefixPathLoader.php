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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Loader;

use Zend\Stdlib\ArrayStack,
    Zend\Stdlib\SplStack,
    SplDoublyLinkedList,
    SplFileInfo;

/**
 * Prefix/Path plugin loader
 *
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PrefixPathLoader implements ShortNameLocater, PrefixPathMapper
{
    /**
     * Instance loaded plugin paths
     *
     * @var array
     */
    protected $loadedPluginPaths = array();

    /**
     * Instance loaded plugins
     *
     * @var array
     */
    protected $loadedPlugins = array();

    /**
     * Instance registry property
     *
     * @var array
     */
    protected $prefixPaths = array();

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
        $this->prefixPaths = new ArrayStack();

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure the prefix path plugin loader
     *
     * Proxies to {@link addPrefixPaths()}.
     * 
     * @param  array|Traversable $options 
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
        foreach ($prefixPaths as $prefix => $paths) {
            if (is_array($paths) || $paths instanceof \Traversable) {
                foreach ($paths as $path) {
                    $this->addPrefixPath($prefix, $path);
                }
            } else {
                $this->addPrefixPath($prefix, $paths);
            }
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
        return isset($this->loadedPlugins[$name]);
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

        if (isset($this->loadedPlugins[$name])) {
            return $this->loadedPlugins[$name];
        }

        return false;
    }

    /**
     * Get path to plugin class
     *
     * @param  mixed $name
     * @return string|false False if not found
     */
    public function getClassPath($name)
    {
        $name = $this->formatName($name);

        if (!empty($this->loadedPluginPaths[$name])) {
            return $this->loadedPluginPaths[$name];
        }

        if ($this->isLoaded($name)) {
            $class = $this->getClassName($name);
            $r     = new \ReflectionClass($class);
            $path  = $r->getFileName();

            $this->loadedPluginPaths[$name] = $path;
            return $path;
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
            $className = $prefix . $name;

            if (class_exists($className)) {
                $found = true;
                break;
            }

            foreach ($paths as $path) {
                $loadFile = new SplFileInfo($path . $classFile);
                if ($loadFile->isFile() && $loadFile->isReadable()) {
                    include_once $loadFile->getPathName();
                    if (class_exists($className, false)) {
                        $found = true;
                        break 2;
                    }
                }
            }
        }

        if (!$found) {
            return false;
       }

        $this->loadedPlugins[$name] = $className;

        return $className;
    }

    /**
     * Set path to class file cache
     *
     * Specify a path to a file that will add include_once statements for each
     * plugin class loaded. This is an opt-in feature for performance purposes.
     *
     * @param  string $file
     * @return void
     * @throws \Zend\Loader\Exception\InvalidArgumentException if file is not writeable or path does not exist
     */
    public static function setIncludeFileCache($file)
    {
        if (null === $file) {
            self::$_includeFileCache = null;
            return;
        }

        if (!file_exists($file) && !file_exists(dirname($file))) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Specified file does not exist and/or directory does not exist ("%s")', 
                $file
            ));
        }
        if (file_exists($file) && !is_writable($file)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Specified file is not writeable ("%s")', 
                $file
            ));
        }
        if (!file_exists($file) && file_exists(dirname($file)) && !is_writable(dirname($file))) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Specified file is not writeable ("%s")', 
                $file
            ));
        }

        self::$_includeFileCache = $file;
    }

    /**
     * Retrieve class file cache path
     *
     * @return string|null
     */
    public static function getIncludeFileCache()
    {
        return self::$_includeFileCache;
    }

    /**
     * Append an include_once statement to the class file cache
     *
     * @param  string $incFile
     * @return void
     */
    protected static function appendIncFile($incFile)
    {
        if (!file_exists(self::$_includeFileCache)) {
            $file = '<?php';
        } else {
            $file = file_get_contents(self::$_includeFileCache);
        }
        if (!strstr($file, $incFile)) {
            $file .= "\ninclude_once '$incFile';";
            file_put_contents(self::$_includeFileCache, $file);
        }
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
        if($prefix == "") {
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
