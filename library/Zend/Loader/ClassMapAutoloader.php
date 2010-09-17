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

// Grab SplAutoloader interface
require_once __DIR__ . '/SplAutoloader.php';

/**
 * Class-map autoloader
 *
 * Utilizes class-map files to lookup classfile locations.
 * 
 * @catebory   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class ClassMapAutoloader implements SplAutoloader
{
    /**
     * Registry of map files that have already been loaded
     * @var array
     */
    protected $mapsLoaded = array();

    /**
     * Class name/filename map
     * @var array
     */
    protected $map = array();

    /**
     * Constructor
     *
     * Create a new instance, and optionally configure the autoloader.
     * 
     * @param  null|array|Traversable $options 
     * @return void
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure the autoloader
     *
     * Proxies to {@link registerAutoloadMaps()}.
     * 
     * @param  array|Traversable $options 
     * @return ClassMapAutoloader
     */
    public function setOptions($options)
    {
        $this->registerAutoloadMaps($options);
        return $this;
    }

    /**
     * Register an autoload map file
     *
     * An autoload map file should return an associative array containing 
     * classname/file pairs.
     * 
     * @param  string $location 
     * @return ClassMapAutoloader
     */
    public function registerAutoloadMap($location)
    {
        if (!file_exists($location)) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Map file provided does not exist');
        }

        $location = realpath($location);

        if (in_array($location, $this->mapsLoaded)) {
            // Already loaded this map
            return $this;
        }

        $map = include $location;

        if (!is_array($map)) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Map file provided does not return a map');
        }

        $this->map = array_merge($this->map, $map);
        $this->mapsLoaded[] = $location;

        return $this;
    }

    /**
     * Register many autoload maps at once
     * 
     * @param  array $locations 
     * @return ClassMapAutoloader
     */
    public function registerAutoloadMaps($locations)
    {
        if (!is_array($locations) && !($locations instanceof \Traversable)) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception/InvalidArgumentException('Map list must be an array or implement Traversable');
        }
        foreach ($locations as $location) {
            $this->registerAutoloadMap($location);
        }
        return $this;
    }

    /**
     * Defined by Autoloadable
     * 
     * @param  string $class 
     * @return void
     */
    public function autoload($class)
    {
        if (array_key_exists($class, $this->map)) {
            include $this->map[$class];
        }
    }

    /**
     * Register the autoloader with spl_autoload registry
     * 
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }
}
