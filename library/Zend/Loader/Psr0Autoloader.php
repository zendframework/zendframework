<?php

/** @namespace */
namespace Zend\Loader;

// Grab Autoloadable interface
require_once __DIR__ . '/Autoloadable.php';

/**
 * PSR-0 compliant autoloader
 *
 * Allows autoloading both namespaced and vendor-prefixed classes.
 * 
 * @package    Zend_Loader
 * @license New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Psr0Autoloader implements Autoloadable
{
    const NS_SEPARATOR     = '\\';
    const PREFIX_SEPARATOR = '_';
    const LOAD_NS          = 'namespaces';
    const LOAD_PREFIX      = 'prefixes';

    /**
     * @var array Namespace/directory pairs to search
     */
    protected $namespaces = array();

    /**
     * @var array Prefix/directory pairs to search
     */
    protected $prefixes = array();

    /**
     * Constructor
     * 
     * @param  array|null $options 
     * @return void
     */
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            foreach ($options as $type => $pairs) {
                switch ($type) {
                    case self::LOAD_NS:
                        if (is_array($pairs)) {
                            $this->registerNamespaces($pairs);
                        }
                        break;
                    case self::LOAD_PREFIX:
                        if (is_array($pairs)) {
                            $this->registerPrefixes($pairs);
                        }
                        break;
                }
            }
        }
    }

    /**
     * Register a namespace/directory pair
     * 
     * @param  string $namespace 
     * @param  string $directory 
     * @return Psr0Autoloader
     */
    public function registerNamespace($namespace, $directory)
    {
        $this->namespaces[$namespace] = $directory;
        return $this;
    }

    /**
     * Register many namespace/directory pairs at once
     * 
     * @param  array $namespaces 
     * @return Psr0Autoloader
     */
    public function registerNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace => $directory) {
            $this->registerNamespace($namespace, $directory);
        }
        return $this;
    }

    /**
     * Register a prefix/directory pair
     * 
     * @param  string $prefix 
     * @param  string $directory 
     * @return Psr0Autoloader
     */
    public function registerPrefix($prefix, $directory)
    {
        $this->prefixes[$prefix] = $directory;
        return $this;
    }

    /**
     * Register many namespace/directory pairs at once
     * 
     * @param  array $prefixes 
     * @return Psr0Autoloader
     */
    public function registerPrefixes(array $prefixes)
    {
        foreach ($prefixes as $prefix => $directory) {
            $this->registerPrefix($prefix, $directory);
        }
        return $this;
    }

    /**
     * Register the autoloader with spl_autoload
     * 
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Defined by Autoloadable; autoload a class
     * 
     * @param  string $class 
     * @return void
     */
    public function autoload($class)
    {
        if (false !== strpos($class, self::NS_SEPARATOR)) {
            return $this->loadClass($class, self::LOAD_NS);
        }
        if (false !== strpos($class, self::PREFIX_SEPARATOR)) {
            return $this->loadClass($class, self::LOAD_PREFIX);
        }
        // Refuse to load classes without a prefix or namespace!
    }

    /**
     * Transform the class name to a filename
     * 
     * @param  string $class 
     * @param  string $directory 
     * @return string
     */
    protected function transformClassNameToFilename($class, $directory)
    {
        return $directory
            . str_replace(
                array(self::NS_SEPARATOR, self::PREFIX_SEPARATOR), 
                DIRECTORY_SEPARATOR, 
                $class
            )
            . '.php';
    }

    /**
     * Load a class, based on its type (namespaced or prefixed)
     * 
     * @param  string $class 
     * @param  string $type 
     * @return void
     */
    protected function loadClass($class, $type)
    {
        if (!in_array($type, array(self::LOAD_NS, self::LOAD_PREFIX))) {
            require_once __DIR__ . '/InvalidArgumentException.php';
            throw new InvalidArgumentException();
        }

        foreach ($this->$type as $leader => $path) {
            if (0 === strpos($class, $leader)) {
                // Trim off leader (namespace or prefix)
                $trimmedClass = substr($class, strlen($leader));

                // create filename
                $filename = $this->transformClassNameToFilename($trimmedClass, $path);
                if (file_exists($filename)) {
                    require_once $filename;
                }
                return;
            }
        }
    }
}
