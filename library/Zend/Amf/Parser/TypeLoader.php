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
 * @package    Zend_Amf
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Amf\Parser;

use Zend\Loader\PluginBroker;

/**
 * Loads a local class and executes the instantiation of that class.
 *
 * @todo       PHP 5.3 can drastically change this class w/ namespace and the new call_user_func w/ namespace
 * @uses       Zend\Amf\Exception
 * @uses       Zend\Amf\Value\Messaging\AcknowledgeMessage
 * @uses       Zend\Amf\Value\Messaging\AsyncMessage
 * @uses       Zend\Amf\Value\Messaging\CommandMessage
 * @uses       Zend\Amf\Value\Messaging\ErrorMessage
 * @uses       Zend\Amf\Value\Messaging\RemotingMessage
 * @package    Zend_Amf
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class TypeLoader
{
    /**
     * @var string callback class
     */
    public static $callbackClass;

    /**
     * @var array AMF class map
     */
    public static $classMap = array (
        'flex.messaging.messages.AcknowledgeMessage' => 'Zend\\Amf\\Value\\Messaging\\AcknowledgeMessage',
        'flex.messaging.messages.ErrorMessage'       => 'Zend\\Amf\\Value\\Messaging\\AsyncMessage',
        'flex.messaging.messages.CommandMessage'     => 'Zend\\Amf\\Value\\Messaging\\CommandMessage',
        'flex.messaging.messages.ErrorMessage'       => 'Zend\\Amf\\Value\\Messaging\\ErrorMessage',
        'flex.messaging.messages.RemotingMessage'    => 'Zend\\Amf\\Value\\Messaging\\RemotingMessage',
        'flex.messaging.io.ArrayCollection'          => 'Zend\\Amf\\Value\\Messaging\\ArrayCollection',
    );

    /**
     * @var array Default class map
     */
    protected static $_defaultClassMap = array(
        'flex.messaging.messages.AcknowledgeMessage' => 'Zend\\Amf\\Value\\Messaging\\AcknowledgeMessage',
        'flex.messaging.messages.ErrorMessage'       => 'Zend\\Amf\\Value\\Messaging\\AsyncMessage',
        'flex.messaging.messages.CommandMessage'     => 'Zend\\Amf\\Value\\Messaging\\CommandMessage',
        'flex.messaging.messages.ErrorMessage'       => 'Zend\\Amf\\Value\\Messaging\\ErrorMessage',
        'flex.messaging.messages.RemotingMessage'    => 'Zend\\Amf\\Value\\Messaging\\RemotingMessage',
        'flex.messaging.io.ArrayCollection'          => 'Zend\\Amf\\Value\\Messaging\\ArrayCollection',
    );

    /**
     * @var \Zend\Loader\PluginBroker
     */
    protected static $_resourceBroker = null;


    /**
     * Load the mapped class type into a callback.
     *
     * @param  string $className
     * @return object|false
     */
    public static function loadType($className)
    {
        $class    = self::getMappedClassName($className);
        if(!$class) {
            $class = str_replace('.', '\\', $className);
        }
        if (!class_exists($class)) {
            return "stdClass";
        }
        return $class;
    }

    /**
     * Looks up the supplied call name to its mapped class name
     *
     * @param  string $className
     * @return string
     */
    public static function getMappedClassName($className)
    {
        $mappedName = array_search($className, self::$classMap);

        if ($mappedName) {
            return $mappedName;
        }

        $mappedName = array_search($className, array_flip(self::$classMap));

        if ($mappedName) {
            return $mappedName;
        }

        return false;
    }

    /**
     * Map PHP class names to ActionScript class names
     *
     * Allows users to map the class names of there action script classes
     * to the equivelent php class name. Used in deserialization to load a class
     * and serialiation to set the class name of the returned object.
     *
     * @param  string $asClassName
     * @param  string $phpClassName
     * @return void
     */
    public static function setMapping($asClassName, $phpClassName)
    {
        self::$classMap[$asClassName] = $phpClassName;
    }

    /**
     * Reset type map
     *
     * @return void
     */
    public static function resetMap()
    {
        self::$classMap = self::$_defaultClassMap;
    }

    /**
     * Set loader for resource type handlers
     *
     * @param \Zend\Loader\PluginBroker $loader
     */
    public static function setResourceBroker(PluginBroker $broker)
    {
        self::$_resourceBroker = $broker;
    }

    /**
     * Get plugin class that handles this resource
     *
     * @param resource $resource Resource type
     * @return object Resource class
     */
    public static function getResourceParser($resource)
    {
        if (self::$_resourceBroker) {
            $type = preg_replace("/[^A-Za-z0-9_]/", " ", get_resource_type($resource));
            $type = str_replace(" ","", ucwords($type));
            return self::$_resourceBroker->load($type);
        }
        return false;
    }

    /**
     * Convert resource to a serializable object
     *
     * @param resource $resource
     * @return mixed
     */
    public static function handleResource($resource)
    {
        if (!self::$_resourceBroker) {
            throw new Exception\InvalidArgumentException('Unable to handle resources - resource plugin broker not set');
        }
        try {
            while (is_resource($resource)) {
                $parser   = self::getResourceParser($resource);
                $resource = $parser->parse($resource);
            }
            return $resource;
        } catch(Exception $e) {
            throw new Exception\InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        } catch(\Exception $e) {
            throw new Exception\RuntimeException('Can not serialize resource type: '. get_resource_type($resource), 0, $e);
        }
    }
}
