<?php
/** @namespace */
namespace Zend\Loader;

// Grab Autoloadable interface
require_once __DIR__ . '/Autoloadable.php';

/**
 * Class-map autoloader
 *
 * Utilizes class-map files to lookup classfile locations.
 * 
 * @package    Zend_Loader
 * @license New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class ClassMapAutoloader implements Autoloadable
{
    protected $mapsLoaded = array();

    protected $map = array();

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
            require_once __DIR__ . '/InvalidArgumentException.php';
            throw new InvalidArgumentException('Map file provided does not exist');
        }

        $location = realpath($location);

        if (in_array($location, $this->mapsLoaded)) {
            // Already loaded this map
            return $this;
        }

        $map = include $location;

        if (!is_array($map)) {
            require_once __DIR__ . '/InvalidArgumentException.php';
            throw new InvalidArgumentException('Map file provided does not return a map');
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
    public function registerAutoloadMaps(array $locations)
    {
        foreach ($locations as $location) {
            $this->registerAutoloadMap($location);
        }
        return $this;
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

    /**
     * Defined by Autoloadable
     * 
     * @param  string $class 
     * @return void
     */
    public function autoload($class)
    {
        if (array_key_exists($class, $this->map)) {
            require_once $this->map[$class];
        }
    }
}
