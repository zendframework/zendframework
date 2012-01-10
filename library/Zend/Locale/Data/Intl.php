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
 * @subpackage Data
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Locale\Data;

use Zend\Locale\Locale as ZFLocale;

/**
 * Locale data provider, handles INTL
 *
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Data
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Intl extends AbstractLocale
{
    /**
     * Returns detailed informations from the language table
     * If no detail is given a complete table is returned
     *
     * @param string  $locale Normalized locale
     * @param boolean $invert Invert output of the data
     * @param string|array $detail Detail to return information for
     * @return array
     */
    public static function getDisplayLanguage($locale, $invert = false, $detail = null)
    {
        if ($detail !== null) {
            return Locale::getDisplayLanguage($locale);
        } else {
            $list = ZFLocale::getLocaleList();
            foreach($list as $key => $value) {
                $list[$key] = Locale::getDisplayLanguage($key);
            }

            if ($invert) {
                array_flip($list);
            }

            return $list;
        }
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
        if ($detail !== null) {
            return Locale::getDisplayScript($locale);
        } else {
            $list = ZFLocale::getLocaleList();
            foreach($list as $key => $value) {
                $list[$key] = Locale::getDisplayScript($key);
            }

            if ($invert) {
                array_flip($list);
            }

            return $list;
        }
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
        if ($detail !== null) {
            return Locale::getDisplayRegion($locale);
        } else {
            $list = ZFLocale::getLocaleList();
            foreach($list as $key => $value) {
                $list[$key] = Locale::getDisplayRegion($key);
            }

            if ($invert) {
                array_flip($list);
            }

            return $list;
        }
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
        if ($detail !== null) {
            return Locale::getDisplayVariant($locale);
        } else {
            $list = ZFLocale::getLocaleList();
            foreach($list as $key => $value) {
                $list[$key] = Locale::getDisplayVariant($key);
            }

            if ($invert) {
                array_flip($list);
            }

            return $list;
        }
    }
}
