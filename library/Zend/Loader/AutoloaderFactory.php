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

require_once __DIR__ . '/SplAutoloader.php';

/**
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AutoloaderFactory
{
    /**
     * @var array All autoloaders registered using the factory
     */
    protected static $loaders = array();

    /**
     * @var StandardAutoloader StandardAutoloader instance for resolving 
     * autoloader classes via the include_path
     */
    protected static $standardAutoloader;

    /**
     * Factory for autoloaders
     *
     * Options should be an array or Traversable object of the following structure:
     * <code>
     * array(
     *     '<autoloader class name>' => $autoloaderOptions,
     * )
     * </code>
     *
     * The factory will then loop through and instantiate each autoloader with 
     * the specified options, and register each with the spl_autoloader.
     *
     * You may retrieve the concrete autoloader instances later using 
     * {@link getRegisteredAutoloaders()}.
     *
     * Note that the class names must be resolvable on the include_path or via
     * the Zend library, using PSR-0 rules (unless the class has already been 
     * loaded).
     * 
     * @param  array|Traversable $options 
     * @return void
     * @throws Exception\InvalidArgumentException for invalid options
     * @throws Exception\InvalidArgumentException for unloadable autoloader classes
     * @throws Exception\DomainException for autoloader classes not implementing SplAutoloader
     */
    public static function factory($options)
    {
        if (!is_array($options) && !($options instanceof \Traversable)) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Options provided must be an array or Traversable');
        }

        foreach ($options as $class => $options) {
            if (!isset(static::$loaders[$class])) {
                $autoloader = static::getStandardAutoloader();
                if (!class_exists($class) && !$autoloader->autoload($class)) {
                    require_once 'Exception/InvalidArgumentException.php';
                    throw new Exception\InvalidArgumentException(sprintf('Autoloader class "%s" not loaded', $class));
                }
                if ($class === 'Zend\Loader\StandardAutoloader') {
                    $autoloader->setOptions($options);
                } else {
                    $autoloader = new $class($options);
                }
                $autoloader->register();
                static::$loaders[$class] = $autoloader;
            } else {
                static::$loaders[$class]->setOptions($options);
            }
        }
    }

    /**
     * Get an list of all autoloaders registered with the factory
     *
     * Returns an array of autoloader instances.
     * 
     * @return array
     */
    public static function getRegisteredAutoloaders()
    {
        return static::$loaders;
    }

    /**
     * Retrieves an autoloader by class name 
     * 
     * @param string $class 
     * @return SplAutoloader
     * @throws Exception\InvalidArgumentException for non-registered class
     */
    public static function getRegisteredAutoloader($class)
    {
        if (!isset(static::$loaders[$class])) {
            require_once 'Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException(sprintf('Autoloader class "%s" not loaded', $class));
        }
        return static::$loaders[$class];
    }

    /**
     * Unregisters all autoloaders that have been registered via the factory. 
     * This will NOT unregister autoloaders registered outside of the fctory.
     * 
     * @return void
     */
    public static function unregisterAutoloaders()
    {
        foreach (static::getRegisteredAutoloaders() as $class => $autoloader) {
            spl_autoload_unregister(array($autoloader, 'autoload'));
            unset(static::$loaders[$class]);
        }
    }

    /**
     * Unregister a single autoloader by class name
     * 
     * @param  string $autoloaderClass 
     * @return bool
     */
    public static function unregisterAutoloader($autoloaderClass)
    {
        if (!isset(static::$loaders[$autoloaderClass])) {
            return false;
        }

        $autoloader = static::$loaders[$autoloaderClass];
        spl_autoload_unregister(array($autoloader, 'autoload'));
        unset(static::$loaders[$autoloaderClass]);
        return true;
    }

    /**
     * Get an instance of the standard autoloader
     *
     * Used to attempt to resolve autoloader classes, using the 
     * StandardAutoloader. The instance is marked as a fallback autoloader, to 
     * allow resolving autoloaders not under the "Zend" namespace.
     * 
     * @return SplAutoloader
     */
    protected static function getStandardAutoloader()
    {
        if (null !== static::$standardAutoloader) {
            return static::$standardAutoloader;
        }

        require_once __DIR__ . '/StandardAutoloader.php';
        $loader = new StandardAutoloader();
        $loader->setFallbackAutoloader(true);
        static::$standardAutoloader = $loader;
        return static::$standardAutoloader;
    }
}
