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
 * @package    Zend_Server
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Server;

/**
 * \Zend\Server\Cache: cache server definitions
 *
 * @uses       \Zend\Server\Definition
 * @category   Zend
 * @package    Zend_Server
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cache
{
    /**
     * @var array Methods to skip when caching server
     */
    protected static $_skipMethods = array();

    /**
     * Cache a file containing the dispatch list.
     *
     * Serializes the server definition stores the information
     * in $filename.
     *
     * Returns false on any error (typically, inability to write to file), true
     * on success.
     *
     * @param  string $filename
     * @param  \Zend\Server\Server $server
     * @return bool
     */
    public static function save($filename, Server $server)
    {
        if (!is_string($filename)
            || (!file_exists($filename) && !is_writable(dirname($filename))))
        {
            return false;
        }

        $methods = $server->getFunctions();

        if ($methods instanceof Definition) {
            $definition = new Definition();
            foreach ($methods as $method) {
                if (in_array($method->getName(), self::$_skipMethods)) {
                    continue;
                }
                $definition->addMethod($method);
            }
            $methods = $definition;
        }

        if (0 === @file_put_contents($filename, serialize($methods))) {
            return false;
        }

        return true;
    }

    /**
     * Load server definition from a file
     *
     * Unserializes a stored server definition from $filename. Returns false if
     * it fails in any way, true on success.
     *
     * Useful to prevent needing to build the server definition on each
     * request. Sample usage:
     *
     * <code>
     * if (!Zend\Server\Cache::get($filename, $server)) {
     *     require_once 'Some/Service/ServiceClass.php';
     *     require_once 'Another/Service/ServiceClass.php';
     *
     *     // Attach Some\Service\ServiceClass with namespace 'some'
     *     $server->attach('Some\Service\ServiceClass', 'some');
     *
     *     // Attach Another\Service\ServiceClass with namespace 'another'
     *     $server->attach('Another\Service\ServiceClass', 'another');
     *
     *     Zend\Server\Cache::save($filename, $server);
     * }
     *
     * $response = $server->handle();
     * echo $response;
     * </code>
     *
     * @param  string $filename
     * @param  \Zend\Server\Server $server
     * @return bool
     */
    public static function get($filename, Server $server)
    {
        if (!is_string($filename)
            || !file_exists($filename)
            || !is_readable($filename))
        {
            return false;
        }


        if (false === ($dispatch = @file_get_contents($filename))) {
            return false;
        }

        if (false === ($dispatchArray = @unserialize($dispatch))) {
            return false;
        }

        $server->loadFunctions($dispatchArray);

        return true;
    }

    /**
     * Remove a cache file
     *
     * @param  string $filename
     * @return boolean
     */
    public static function delete($filename)
    {
        if (is_string($filename) && file_exists($filename)) {
            unlink($filename);
            return true;
        }

        return false;
    }
}
