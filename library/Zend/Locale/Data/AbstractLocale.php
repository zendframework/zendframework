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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Locale\Data;

use Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\Locale\Locale,
    Zend\Locale\Exception\InvalidArgumentException,
    Zend\Locale\Exception\UnsupportedMethodException;

/**
 * Locale data reader, handles the CLDR
 *
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Data
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractLocale
{
    /**
     * Internal cache for ldml values
     *
     * @var CacheStorage
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
     * Returns the set cache
     *
     * @return CacheStorage The set cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Set a cache for Zend_Locale_Data
     *
     * @param CacheStorage $cache A cache frontend
     */
    public static function setCache(CacheStorage $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        if (self::$_cache instanceof CacheStorage) {
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
        throw new UnsupportedMethodException('This implementation does not support the selected locale information');
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
        throw new UnsupportedMethodException('This implementation does not support the selected locale information');
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
        throw new UnsupportedMethodException('This implementation does not support the selected locale information');
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
        throw new UnsupportedMethodException('This implementation does not support the selected locale information');
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
