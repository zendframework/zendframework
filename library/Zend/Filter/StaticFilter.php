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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Filter;

use Zend\Loader;

/**
 * @uses       \Zend\Filter\Exception
 * @uses       \Zend\Filter\Filter
 * @uses       \Zend\Loader
 * @uses       \Zend\Loader\Exception
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticFilter
{
    /**
     * @var Loader\PrefixPathMapper
     */
    protected static $_pluginLoader;

    /**
     * Set plugin loader for resolving filter classes
     * 
     * @param  Loader\ShortNameLocater $loader 
     * @return void
     */
    public static function setPluginLoader(Loader\ShortNameLocater $loader = null)
    {
        self::$_pluginLoader = $loader;
    }

    /**
     * Get plugin loader for resolving filter classes
     * 
     * @return Loader\ShortNameLocater
     */
    public static function getPluginLoader()
    {
        if (null === self::$_pluginLoader) {
            static::setPluginLoader(new Loader\PluginLoader(array(
                'Zend\Filter' => 'Zend/Filter',
            )));
        }
        return self::$_pluginLoader;
    }

    /**
     * Returns a value filtered through a specified filter class, without requiring separate
     * instantiation of the filter object.
     *
     * The first argument of this method is a data input value, that you would have filtered.
     * The second argument is a string, which corresponds to the basename of the filter class,
     * relative to the Zend_Filter namespace. This method automatically loads the class,
     * creates an instance, and applies the filter() method to the data input. You can also pass
     * an array of constructor arguments, if they are needed for the filter class.
     *
     * @param  mixed        $value
     * @param  string       $classBaseName
     * @param  array        $args          OPTIONAL
     * @param  array|string $namespaces    OPTIONAL
     * @return mixed
     * @throws \Zend\Filter\Exception
     */
    public static function execute($value, $classBaseName, array $args = array(), $namespaces = array())
    {
        $loader = static::getPluginLoader();
        if (!class_exists($classBaseName)) {
            try {
                $className  = $loader->load($classBaseName);
            } catch (Loader\Exception $e) {
                throw new Exception("Filter class not found from basename '$classBaseName'", null, $e);
            }
        } else {
            $className = $classBaseName;
        }

        $class = new \ReflectionClass($className);
        if (!$class->implementsInterface('Zend\Filter\Filter')) {
            throw new Exception("Filter class not found from basename '$classBaseName'");
        }

        if ((0 < count($args)) && $class->hasMethod('__construct')) {
            $keys    = array_keys($args);
            $numeric = false;
            foreach($keys as $key) {
                if (is_numeric($key)) {
                    $numeric = true;
                    break;
                }
            }

            if ($numeric) {
                $object = $class->newInstanceArgs($args);
            } else {
                $object = $class->newInstance($args);
            }
        } else {
            $object = $class->newInstance();
        }

        return $object->filter($value);
    }
}
