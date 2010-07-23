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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Validator;

use Zend\Loader;

/**
 * @uses       \Zend\Loader
 * @uses       \Zend\Validator\AbstractValidator
 * @uses       \Zend\Validator\Exception
 * @uses       \Zend\Validator\Validator
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticValidator
{
    /**
     * @var Zend\Loader\ShortNameLocater
     */
    protected static $_pluginLoader;

    /**
     * Set plugin loader to use for locating validators
     * 
     * @param  Loader\ShortNameLocater|null $loader 
     * @return void
     */
    public static function setPluginLoader(Loader\ShortNameLocater $loader = null)
    {
        self::$_pluginLoader = $loader;
    }

    /**
     * Get plugin loader for locating validators
     * 
     * @return Loader\ShortNameLocater
     */
    public static function getPluginLoader()
    {
        if (null === self::$_pluginLoader) {
            static::setPluginLoader(new Loader\PluginLoader(array(
                'Zend\Validator' => 'Zend/Validator',
            )));
        }
        return self::$_pluginLoader;
    }

    /**
     * @param  mixed    $value
     * @param  string   $classBaseName
     * @param  array    $args          OPTIONAL
     * @return boolean
     * @throws \Zend\Validator\Exception
     */
    public static function execute($value, $classBaseName, array $args = array())
    {
        $loader = static::getPluginLoader();
        if (!class_exists($classBaseName)) {
            try {
                $className  = $loader->load($classBaseName);
            } catch (Loader\Exception $e) {
                throw new Exception("Validator class not found from basename '$classBaseName'", null, $e);
            }
        } else {
            $className = $classBaseName;
        }

        $class = new \ReflectionClass($className);
        if (!$class->implementsInterface('Zend\Validator\Validator')) {
            throw new Exception("Validator class not found from basename '$classBaseName'");
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

        return $object->isValid($value);
    }
}
