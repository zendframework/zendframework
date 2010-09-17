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
 * @subpackage Exception
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** @namespace */
namespace Zend\Loader;

// Grab SplAutolaoder interface
require_once __DIR__ . '/SplAutolaoder.php';

/**
 * PSR-0 compliant autoloader
 *
 * Allows autoloading both namespaced and vendor-prefixed classes.
 * 
 * @package    Zend_Loader
 * @license New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Psr0Autoloader implements SplAutoloader
{
    const NS_SEPARATOR     = '\\';
    const PREFIX_SEPARATOR = '_';
    const LOAD_NS          = 'namespaces';
    const LOAD_PREFIX      = 'prefixes';

    /**
     * @var array Namespace/directory pairs to search; ZF library added by default
     */
    protected $namespaces = array();

    /**
     * @var array Prefix/directory pairs to search
     */
    protected $prefixes = array();

    /**
     * Constructor
     * 
     * @param  null|array|Traversable $options 
     * @return void
     */
    public function __construct($options = null)
    {
        $this->registerNamespace('Zend', dirname(__DIR__));

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure autoloader
     *
     * Allows specifying both "namespace" and "prefix" pairs, using the 
     * following structure:
     * <code>
     * array(
     *     'namespaces' => array(
     *         'Zend'     => '/path/to/Zend/library',
     *         'Doctrine' => '/path/to/Doctrine/library',
     *     ),
     *     'prefixes' => array(
     *         'Phly_'     => '/path/to/Phly/library',
     *     ),
     * )
     * </code>
     * 
     * @param  array|Traversable $options 
     * @return Psr0Autoloader
     */
    public function setOptions($options)
    {
        if (is_array($options) && !($options instanceof \Traversable)) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Options must be either an array or Traversable');
        }

        foreach ($options as $type => $pairs) {
            switch ($type) {
                case self::LOAD_NS:
                    if (is_array($pairs) || $pairs instanceof \Traversable) {
                        $this->registerNamespaces($pairs);
                    }
                    break;
                case self::LOAD_PREFIX:
                    if (is_array($pairs) || $pairs instanceof \Traversable) {
                        $this->registerPrefixes($pairs);
                    }
                    break;
                default:
                    // ignore
            }
        }
        return $this;
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
        $namespace = rtrim($namespace, self::NS_SEPARATOR). self::NS_SEPARATOR;
        $this->namespaces[$namespace] = $this->normalizeDirectory($directory);
        return $this;
    }

    /**
     * Register many namespace/directory pairs at once
     * 
     * @param  array $namespaces 
     * @return Psr0Autoloader
     */
    public function registerNamespaces($namespaces)
    {
        if (!is_array($namespaces) && !$namespaces instanceof \Traversable) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Namespace pairs must be either an array or Traversable');
        }

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
        $prefix = rtrim($prefix, self::PREFIX_SEPARATOR). self::PREFIX_SEPARATOR;
        $this->prefixes[$prefix] = $this->normalizeDirectory($directory);
        return $this;
    }

    /**
     * Register many namespace/directory pairs at once
     * 
     * @param  array $prefixes 
     * @return Psr0Autoloader
     */
    public function registerPrefixes($prefixes)
    {
        if (!is_array($namespaces) && !$namespaces instanceof \Traversable) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Prefix pairs must be either an array or Traversable');
        }

        foreach ($prefixes as $prefix => $directory) {
            $this->registerPrefix($prefix, $directory);
        }
        return $this;
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
     * Register the autoloader with spl_autoload
     * 
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
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
            throw new Exception\InvalidArgumentException();
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

    /**
     * Normalize the directory to include a trailing directory separator
     * 
     * @param  string $directory 
     * @return string
     */
    protected function normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];
        if (in_array($last, array('/', '\\'))) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
            return $directory;
        }
        $directory .= DIRECTORY_SEPARATOR;
        return $directory;
    }

}
