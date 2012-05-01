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
 * @package    Zend_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Translator;

use Traversable,
    Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter,
    Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Translator
{

    /**
     * Adapter names constants
     */
    const AN_ARRAY   = 'ArrayAdapter';
    const AN_CSV     = 'Csv';
    const AN_GETTEXT = 'Gettext';
    const AN_INI     = 'Ini';
    const AN_QT      = 'Qt';
    const AN_TBX     = 'Tbx';
    const AN_TMX     = 'Tmx';
    const AN_XLIFF   = 'Xliff';
    const AN_XMLTM   = 'XmlTm';

    const LOCALE_DIRECTORY = 'directory';
    const LOCALE_FILENAME  = 'filename';

    /**
     * Adapter
     *
     * @var Adapter\AbstractAdapter
     */
    private $_adapter;

    /**
     * Generates the standard translation object
     *
     * @param  array|Traversable $options Options to use
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['adapter'] = array_shift($args);
            if (!empty($args)) {
                $options['content'] = array_shift($args);
            }

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } else if (!is_array($options)) {
            $options = array('adapter' => $options);
        }

        $this->setAdapter($options);
    }

    /**
     * Sets a new adapter
     *
     * @param  array|Traversable $options Options to use
     * @throws Exception\InvalidArgumentException
     */
    public function setAdapter($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['adapter'] = array_shift($args);
            if (!empty($args)) {
                $options['content'] = array_shift($args);
            }

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } elseif (!is_array($options)) {
            $options = array('adapter' => $options);
        }

        if (empty($options['adapter'])) {
            throw new Exception\InvalidArgumentException("No adapter given");
        }

        if (class_exists('Zend\Translator\Adapter\\' . ucfirst($options['adapter']))) {
            $options['adapter'] = 'Zend\Translator\Adapter\\' . ucfirst($options['adapter']);
        } elseif (!class_exists($options['adapter'])) {
            throw new Exception\InvalidArgumentException("Adapter " . $options['adapter'] . " does not exist and cannot be loaded");
        }

        if (array_key_exists('cache', $options)) {
            Adapter\AbstractAdapter::setCache($options['cache']);
        }

        $adapter = $options['adapter'];
        unset($options['adapter']);
        $this->_adapter = new $adapter($options);
        if (!$this->_adapter instanceof Adapter\AbstractAdapter) {
            throw new Exception\InvalidArgumentException("Adapter " . $adapter . " does not extend Zend\Translator\Adapter\AbstractAdapter");
        }
    }

    /**
     * Returns the adapters name and it's options
     *
     * @return Adapter\AbstractAdapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Returns the set cache
     *
     * @return CacheAdapter The set cache
     */
    public static function getCache()
    {
        return Adapter\AbstractAdapter::getCache();
    }

    /**
     * Sets a cache for all instances of Zend_Translator
     *
     * @param  CacheAdapter $cache Cache to store to
     * @return void
     */
    public static function setCache(CacheAdapter $cache)
    {
        Adapter\AbstractAdapter::setCache($cache);
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        return Adapter\AbstractAdapter::hasCache();
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        Adapter\AbstractAdapter::removeCache();
    }

    /**
     * Clears all set cache data
     *
     * @param string $tag Tag to clear when the default tag name is not used
     * @return void
     */
    public static function clearCache($tag = null)
    {
        Adapter\AbstractAdapter::clearCache($tag);
    }

    /**
     * Calls all methods from the adapter
     * @throws Exception\BadMethodCallException
     */
    public function __call($method, array $options)
    {
        if (!method_exists($this->_adapter, $method)) {
            throw new Exception\BadMethodCallException("Unknown method '" . $method . "' called!");
        }

        return call_user_func_array(array($this->_adapter, $method), $options);
    }
}
