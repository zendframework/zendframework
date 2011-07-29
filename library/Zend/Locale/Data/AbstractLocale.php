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
 * @package    Zend_Locale
 * @subpackage Cldr
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Locale\Data;

use Zend\Cache\Cache,
    Zend\Cache\Frontend as CacheFrontend,
    Zend\Locale\Locale,
    Zend\Locale\Exception\InvalidArgumentException;

/**
 * Locale data reader, handles the CLDR
 *
 * @uses       Zend\Cache\Cache
 * @uses       Zend\Locale
 * @uses       Zend\Locale\Exception\InvalidArgumentException
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Data
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractLocale
{
    /**
     * Internal cache for ldml values
     *
     * @var \Zend\Cache\Core
     * @access private
     */
    protected static $_cache = null;

    /**
     * Internal option, cache disabled
     *
     * @var    boolean
     * @access private
     */
    protected static $_cacheDisabled = false;

    /**
     * Internal value to remember if cache supports tags
     *
     * @var boolean
     */
    protected static $_cacheTags = false;

    /**
     * Returns the set cache
     *
     * @return \Zend\Cache\Core The set cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Set a cache for Zend_Locale_Data
     *
     * @param \Zend\Cache\Frontend $cache A cache frontend
     */
    public static function setCache(CacheFrontend $cache)
    {
        self::$_cache = $cache;
        self::_getTagSupportForCache();
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        if (self::$_cache !== null) {
            return true;
        }

        return false;
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        self::$_cache = null;
    }

    /**
     * Clears all set cache data
     *
     * @param string $tag Tag to clear when the default tag name is not used
     * @return void
     */
    public static function clearCache($tag = null)
    {
        if (self::$_cacheTags) {
            if ($tag == null) {
                $tag = 'Zend_Locale';
            }

            self::$_cache->clean(\Zend\Cache\Cache::CLEANING_MODE_MATCHING_TAG, array($tag));
        } else {
            self::$_cache->clean(\Zend\Cache\Cache::CLEANING_MODE_ALL);
        }
    }

    /**
     * Disables the cache
     *
     * @param boolean $flag
     */
    public static function disableCache($flag)
    {
        self::$_cacheDisabled = (boolean) $flag;
    }

    /**
     * Returns true when the cache is disabled
     *
     * @return boolean
     */
    public static function isCacheDisabled()
    {
        return self::$_cacheDisabled;
    }

    /**
     * Returns true when the actual set cache supports tags
     *
     * @return boolean
     */
    public static function hasCacheTagSupport()
    {
      return self::$_cacheTags;
    }

    /**
     * Internal method to check if the given cache supports tags
     *
     * @return false|string
     */
    protected static function _getTagSupportForCache()
    {
        $backend = self::$_cache->getBackend();
        if ($backend instanceof \Zend\Cache\Backend\ExtendedInterface) {
            $cacheOptions = $backend->getCapabilities();
            self::$_cacheTags = $cacheOptions['tags'];
        } else {
            self::$_cacheTags = false;
        }

        return self::$_cacheTags;
    }

    /**
     * Returns detailed informations from the language table
     * If no detail is given a complete table is returned
     *
     * @param string  $locale  Normalized locale
     * @param boolean $reverse Invert output of the data
     * @param string|array $detail Detail to return information for
     * @return array
     */
    public static function getDisplayLanguage($locale, $invert = false, $detail = null)
    {
        throw new UnsupportedMethod('This implementation does not support the selected locale information');
    }

    /**
     * Returns detailed informations from the script table
     * If no detail is given a complete table is returned
     *
     * @param string  $locale Normalized locale
     * @param boolean $invert Invert output of the data
     * @param string|array $detail Detail to return information for
     * @return array
     */
    public static function getDisplayScript($locale, $invert = false, $detail = null)
    {
        throw new UnsupportedMethod('This implementation does not support the selected locale information');
    }

    /**
     * Returns detailed informations from the territory table
     * If no detail is given a complete table is returned
     *
     * @param string  $locale Normalized locale
     * @param boolean $invert Invert output of the data
     * @param string|array $detail Detail to return information for
     * @return array
     */
    public static function getDisplayTerritory($locale, $invert = false, $detail = null)
    {
        throw new UnsupportedMethod('This implementation does not support the selected locale information');
    }

    /**
     * Returns detailed informations from the variant table
     * If no detail is given a complete table is returned
     *
     * @param string  $locale Normalized locale
     * @param boolean $invert Invert output of the data
     * @param string|array $detail Detail to return information for
     * @return array
     */
    public static function getDisplayVariant($locale, $invert = false, $detail = null)
    {
        throw new UnsupportedMethod('This implementation does not support the selected locale information');
    }

/**
 *   public static function toInteger();
 *   public static function toFloat();
 *   public static function toDecimal();
 *   public static function toScientific();
 *   public static function toCurrency();
 *   public static function toArray();
 *   public static function toDateString();
 */
}
