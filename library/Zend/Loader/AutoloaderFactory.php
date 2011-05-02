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
class AutoloaderFactory
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
     * Not meant to be instantiable
     * 
     * @return void
     */
    private function __construct()
    {
    }

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

        foreach ($options as $class => $opts) {
            if (!class_exists($class)) {
                $autoloader = self::getStandardAutoloader();
                if (!class_exists($class) && !$autoloader->autoload($class)) {
                    require_once 'Exception/InvalidArgumentException.php';
                    throw new Exception\InvalidArgumentException(sprintf('Autoloader class "%s" not loaded', $class));
                }
            }
            $loader = new $class($opts);
            if (!$loader instanceof SplAutoloader) {
                require_once 'Exception/DomainException.php';
                throw new Exception\DomainException(sprintf('Autoloader class "%s" does not implement Zend\Loader\SplAutoloader', $class));
            }
            $loader->register();
            self::$loaders[] = new $loader;
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
        if (null !== self::$standardAutoloader) {
            return self::$standardAutoloader;
        }

        require_once __DIR__ . '/StandardAutoloader.php';
        $loader = new StandardAutoloader();
        $loader->setFallbackAutoloader(true);
        self::$standardAutoloader = $loader;
        return self::$standardAutoloader;
    }
}
