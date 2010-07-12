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
     * Default Namespaces
     *
     * @var array
     */
    protected static $_defaultNamespaces = array();

    /**
     * Returns the set default namespaces
     *
     * @return array
     */
    public static function getDefaultNamespaces()
    {
        return self::$_defaultNamespaces;
    }

    /**
     * Sets new default namespaces
     *
     * @param array|string $namespace
     * @return null
     */
    public static function setDefaultNamespaces($namespace)
    {
        if (!is_array($namespace)) {
            $namespace = array((string) $namespace);
        }

        self::$_defaultNamespaces = $namespace;
    }

    /**
     * Adds a new default namespace
     *
     * @param array|string $namespace
     * @return null
     */
    public static function addDefaultNamespaces($namespace)
    {
        if (!is_array($namespace)) {
            $namespace = array((string) $namespace);
        }

        self::$_defaultNamespaces = array_unique(array_merge(self::$_defaultNamespaces, $namespace));
    }

    /**
     * Returns true when defaultNamespaces are set
     *
     * @return boolean
     */
    public static function hasDefaultNamespaces()
    {
        return (!empty(self::$_defaultNamespaces));
    }

    /**
     * Returns the maximum allowed message length
     *
     * @return integer
     */
    public static function getMessageLength()
    {
        return AbstractValidator::getMessageLength();
    }

    /**
     * Sets the maximum allowed message length
     *
     * @param integer $length
     */
    public static function setMessageLength($length = -1)
    {
        AbstractValidator::setMessageLength($length);
    }
    
    /**
     * @param  mixed    $value
     * @param  string   $classBaseName
     * @param  array    $args          OPTIONAL
     * @param  mixed    $namespaces    OPTIONAL
     * @return boolean
     * @throws \Zend\Validator\Exception
     */
    public static function execute($value, $classBaseName, array $args = array(), $namespaces = array())
    {
        $namespaces = array_merge((array) $namespaces, self::$_defaultNamespaces, array('Zend\Validator'));
        $className  = ucfirst($classBaseName);
        try {
            if (!class_exists($className, false)) {
                foreach($namespaces as $namespace) {
                    $class = $namespace . '\\' . $className;
                    $file  = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
                    if (\Zend\Loader::isReadable($file)) {
                        \Zend\Loader::loadClass($class);
                        $className = $class;
                        break;
                    }
                }
            }

            $class = new \ReflectionClass($className);
            if ($class->implementsInterface('Zend\Validator\Validator')) {
                if ($class->hasMethod('__construct')) {
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
        } catch (Exception $ze) {
            // if there is an exception while validating throw it
            throw $ze;
        } catch (\Exception $e) {
            // fallthrough and continue for missing validation classes
        }

        throw new Exception("Validate class not found from basename '$classBaseName'");
    }
}
