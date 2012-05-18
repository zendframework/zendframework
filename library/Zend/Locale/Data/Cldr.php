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

namespace Zend\Locale\Data;

use Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter,
    Zend\Locale\Locale,
    Zend\Locale\Exception;

/**
 * Locale data provider, handles CLDR
 *
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Data
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cldr extends AbstractLocale
{
    /**
     * Internal path to CLDR resources
     */
    protected static $_path;

    /**
     * Internal return value
     */
    protected static $_result = array();

    /**
     * Locale files
     *
     * @var resource
     */
    private static $_ldml = array();

    /**
     * List of values which are collected
     *
     * @var array
     */
    private static $_list = array();

    /**
     * Read the content from locale
     *
     * Can be called like:
     * <ldml>
     *     <delimiter>test</delimiter>
     *     <second type='myone'>content</second>
     *     <second type='mysecond'>content2</second>
     *     <third type='mythird' />
     * </ldml>
     *
     * Case 1: _readFile('ar','/ldml/delimiter')             -> returns [] = test
     * Case 1: _readFile('ar','/ldml/second[@type=myone]')   -> returns [] = content
     * Case 2: _readFile('ar','/ldml/second','type')         -> returns [myone] = content; [mysecond] = content2
     * Case 3: _readFile('ar','/ldml/delimiter',,'right')    -> returns [right] = test
     * Case 4: _readFile('ar','/ldml/third','type','myone')  -> returns [myone] = mythird
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $attribute
     * @param  string $value
     * @access private
     * @return array
     */
    private static function _readFile($locale, $path, $attribute, $value, $temp)
    {
        // without attribute - read all values
        // with attribute    - read only this value
        if (!empty(self::$_ldml[(string) $locale])) {

            $result = self::$_ldml[(string) $locale]->xpath($path);
            if (!empty($result)) {
                foreach ($result as &$found) {

                    if (empty($value)) {

                        if (empty($attribute)) {
                            // Case 1
                            $temp[] = (string) $found;
                        } else if (empty($temp[(string) $found[$attribute]])){
                            // Case 2
                            $temp[(string) $found[$attribute]] = (string) $found;
                        }

                    } else if (empty ($temp[$value])) {

                        if (empty($attribute)) {
                            // Case 3
                            $temp[$value] = (string) $found;
                        } else {
                            // Case 4
                            $temp[$value] = (string) $found[$attribute];
                        }

                    }
                }
            }
        }
        return $temp;
    }

    /**
     * Find possible routing to other path or locale
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $attribute
     * @param  string $value
     * @param  array  $temp
     * @access private
     */
    private static function _findRoute($locale, $path, $attribute, $value, &$temp)
    {
        // load locale file if not already in cache
        // needed for alias tag when referring to other locale
        if (empty(self::$_ldml[(string) $locale])) {
            $filename = self::getPath() . '/' . $locale . '.xml';
            if (!file_exists($filename)) {
                throw new Exception\InvalidArgumentException(
                  "Missing locale file '$filename'"
                );
            }

            self::$_ldml[(string) $locale] = simplexml_load_file($filename);
        }

        // search for 'alias' tag in the search path for redirection
        $search = '';
        $tok = strtok($path, '/');

        // parse the complete path
        if (!empty(self::$_ldml[(string) $locale])) {
            while ($tok !== false) {
                $search .=  '/' . $tok;
                if (strpos($search, '[@') !== false) {
                    while (strrpos($search, '[@') > strrpos($search, ']')) {
                        $tok = strtok('/');
                        if (empty($tok)) {
                            $search .= '/';
                        }
                        $search = $search . '/' . $tok;
                    }
                }
                $result = self::$_ldml[(string) $locale]->xpath($search . '/alias');

                // alias found
                if (!empty($result)) {

                    $source = $result[0]['source'];
                    $newpath = $result[0]['path'];

                    // new path - path //ldml is to ignore
                    if ($newpath != '//ldml') {
                        // other path - parse to make real path

                        while (substr($newpath,0,3) == '../') {
                            $newpath = substr($newpath, 3);
                            $search = substr($search, 0, strrpos($search, '/'));
                        }

                        // truncate ../ to realpath otherwise problems with alias
                        $path = $search . '/' . $newpath;
                        while (($tok = strtok('/'))!== false) {
                            $path = $path . '/' . $tok;
                        }
                    }

                    // reroute to other locale
                    if ($source != 'locale') {
                        $locale = $source;
                    }

                    $temp = self::_getFile($locale, $path, $attribute, $value, $temp);
                    return false;
                }

                $tok = strtok('/');
            }
        }
        return true;
    }

    /**
     * Read the right LDML file
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $attribute
     * @param  string $value
     * @access private
     */
    private static function _getFile($locale, $path, $attribute = false, $value = false, $temp = array())
    {
        $result = self::_findRoute($locale, $path, $attribute, $value, $temp);
        if ($result) {
            $temp = self::_readFile($locale, $path, $attribute, $value, $temp);
        }

        // parse required locales reversive
        // example: when given zh_Hans_CN
        // 1. -> zh_Hans_CN
        // 2. -> zh_Hans
        // 3. -> zh
        // 4. -> root
        if (($locale != 'main/root') && ($result)) {
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
            if (!empty($locale)) {
                $temp = self::_getFile($locale, $path, $attribute, $value, $temp);
            } else {
                $temp = self::_getFile('main/root', $path, $attribute, $value, $temp);
            }
        }
        return $temp;
    }

    /**
     * Find the details for supplemental calendar datas
     *
     * @param  string $locale Locale for Detaildata
     * @param  array  $list   List to search
     * @return string         Key for Detaildata
     */
    private static function _calendarDetail($locale, $list)
    {
        $ret = "001";
        foreach ($list as $key => $value) {
            if (strpos($locale, '_') !== false) {
                $locale = substr($locale, strpos($locale, '_') + 1);
            }
            if (strpos($key, $locale) !== false) {
                $ret = $key;
                break;
            }
        }
        return $ret;
    }

    /**
     * Read the LDML file, get a array of multipath defined value
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $value
     * @throws \Zend\Locale\Exception\InvalidArgumentException
     * @return array
     */
    public static function getList($locale, $path, $value = false)
    {
        $locale = self::_checkLocale($locale);

        if (!isset(self::$_cache) && !self::$_cacheDisabled) {
            self::$_cache = CacheFactory::factory(array(
                'adapter' => 'filesystem',
                'plugins' => array('serializer'),
            ));
        }

        $val = $value;
        if (is_array($value)) {
            $val = implode('_' , $value);
        }

        $val = urlencode($val);
        $id = strtr('Zend_LocaleL_' . $locale . '_' . $path . '_' . $val, array('-' => '_', '%' => '_', '+' => '_'));
        if (!self::$_cacheDisabled && ($result = self::$_cache->getItem($id))) {
            return $result;
        }

        $temp = array();
        switch(strtolower($path)) {
            case 'variant':
                $temp = self::_getFile('main/' . $locale, '/ldml/localeDisplayNames/variants/variant', 'type');
                break;

            case 'key':
                $temp = self::_getFile('main/' . $locale, '/ldml/localeDisplayNames/keys/key', 'type');
                break;

            case 'type':
                if (empty($type)) {
                    $temp = self::_getFile('main/' . $locale, '/ldml/localeDisplayNames/types/type', 'type');
                } else {
                    if (($value == 'calendar') or
                        ($value == 'collation') or
                        ($value == 'currency')) {
                        $temp = self::_getFile('main/' . $locale, '/ldml/localeDisplayNames/types/type[@key=\'' . $value . '\']', 'type');
                    } else {
                        $temp = self::_getFile('main/' . $locale, '/ldml/localeDisplayNames/types/type[@type=\'' . $value . '\']', 'type');
                    }
                }
                break;

            case 'layout':
                $temp  = self::_getFile('main/' . $locale, '/ldml/layout/orientation',                 'lines',      'lines');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/orientation',                 'characters', 'characters');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inList',                      '',           'inList');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'currency\']',  '',           'currency');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'dayWidth\']',  '',           'dayWidth');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'fields\']',    '',           'fields');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'keys\']',      '',           'keys');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'languages\']', '',           'languages');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'long\']',      '',           'long');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'measurementSystemNames\']', '', 'measurementSystemNames');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'monthWidth\']',   '',        'monthWidth');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'quarterWidth\']', '',        'quarterWidth');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'scripts\']',   '',           'scripts');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'territories\']',  '',        'territories');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'types\']',     '',           'types');
                $temp += self::_getFile('main/' . $locale, '/ldml/layout/inText[@type=\'variants\']',  '',           'variants');
                break;

            case 'characters':
                $temp  = self::_getFile('main/' . $locale, '/ldml/characters/exemplarCharacters',                           '', 'characters');
                $temp += self::_getFile('main/' . $locale, '/ldml/characters/exemplarCharacters[@type=\'auxiliary\']',      '', 'auxiliary');
                $temp += self::_getFile('main/' . $locale, '/ldml/characters/exemplarCharacters[@type=\'currencySymbol\']', '', 'currencySymbol');
                break;

            case 'delimiters':
                $temp  = self::_getFile('main/' . $locale, '/ldml/delimiters/quotationStart',          '', 'quoteStart');
                $temp += self::_getFile('main/' . $locale, '/ldml/delimiters/quotationEnd',            '', 'quoteEnd');
                $temp += self::_getFile('main/' . $locale, '/ldml/delimiters/alternateQuotationStart', '', 'quoteStartAlt');
                $temp += self::_getFile('main/' . $locale, '/ldml/delimiters/alternateQuotationEnd',   '', 'quoteEndAlt');
                break;

            case 'measurement':
                $temp  = self::_getFile('supplemental/supplementalData', '/supplementalData/measurementData/measurementSystem[@type=\'metric\']', 'territories', 'metric');
                $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/measurementData/measurementSystem[@type=\'US\']',     'territories', 'US');
                $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/measurementData/paperSize[@type=\'A4\']',             'territories', 'A4');
                $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/measurementData/paperSize[@type=\'US-Letter\']',      'territories', 'US-Letter');
                break;

            case 'months':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp  = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/default', 'choice', 'context');
                $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/default', 'choice', 'default');
                $temp['format']['abbreviated'] = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'abbreviated\']/month', 'type');
                $temp['format']['narrow']      = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'narrow\']/month', 'type');
                $temp['format']['wide']        = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'wide\']/month', 'type');
                $temp['stand-alone']['abbreviated']  = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'abbreviated\']/month', 'type');
                $temp['stand-alone']['narrow']       = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'narrow\']/month', 'type');
                $temp['stand-alone']['wide']         = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'wide\']/month', 'type');
                break;

            case 'month':
                if (empty($value)) {
                    $value = array("gregorian", "format", "wide");
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/months/monthContext[@type=\'' . $value[1] . '\']/monthWidth[@type=\'' . $value[2] . '\']/month', 'type');
                break;

            case 'days':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp  = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/default', 'choice', 'context');
                $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/default', 'choice', 'default');
                $temp['format']['abbreviated'] = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'abbreviated\']/day', 'type');
                $temp['format']['narrow']      = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'narrow\']/day', 'type');
                $temp['format']['wide']        = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'wide\']/day', 'type');
                $temp['stand-alone']['abbreviated']  = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'abbreviated\']/day', 'type');
                $temp['stand-alone']['narrow']       = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'narrow\']/day', 'type');
                $temp['stand-alone']['wide']         = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'wide\']/day', 'type');
                break;

            case 'day':
                if (empty($value)) {
                    $value = array("gregorian", "format", "wide");
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/days/dayContext[@type=\'' . $value[1] . '\']/dayWidth[@type=\'' . $value[2] . '\']/day', 'type');
                break;

            case 'week':
                $minDays   = self::_calendarDetail('main/' . $locale, self::_getFile('supplemental/supplementalData', '/supplementalData/weekData/minDays', 'territories'));
                $firstDay  = self::_calendarDetail('main/' . $locale, self::_getFile('supplemental/supplementalData', '/supplementalData/weekData/firstDay', 'territories'));
                $weekStart = self::_calendarDetail('main/' . $locale, self::_getFile('supplemental/supplementalData', '/supplementalData/weekData/weekendStart', 'territories'));
                $weekEnd   = self::_calendarDetail('main/' . $locale, self::_getFile('supplemental/supplementalData', '/supplementalData/weekData/weekendEnd', 'territories'));

                $temp  = self::_getFile('supplemental/supplementalData', "/supplementalData/weekData/minDays[@territories='" . $minDays . "']", 'count', 'minDays');
                $temp += self::_getFile('supplemental/supplementalData', "/supplementalData/weekData/firstDay[@territories='" . $firstDay . "']", 'day', 'firstDay');
                $temp += self::_getFile('supplemental/supplementalData', "/supplementalData/weekData/weekendStart[@territories='" . $weekStart . "']", 'day', 'weekendStart');
                $temp += self::_getFile('supplemental/supplementalData', "/supplementalData/weekData/weekendEnd[@territories='" . $weekEnd . "']", 'day', 'weekendEnd');
                break;

            case 'quarters':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp['format']['abbreviated'] = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'abbreviated\']/quarter', 'type');
                $temp['format']['narrow']      = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'narrow\']/quarter', 'type');
                $temp['format']['wide']        = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'wide\']/quarter', 'type');
                $temp['stand-alone']['abbreviated']  = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'abbreviated\']/quarter', 'type');
                $temp['stand-alone']['narrow']       = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'narrow\']/quarter', 'type');
                $temp['stand-alone']['wide']         = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'wide\']/quarter', 'type');
                break;

            case 'quarter':
                if (empty($value)) {
                    $value = array("gregorian", "format", "wide");
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/quarters/quarterContext[@type=\'' . $value[1] . '\']/quarterWidth[@type=\'' . $value[2] . '\']/quarter', 'type');
                break;

            case 'eras':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp['names']       = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraNames/era', 'type');
                $temp['abbreviated'] = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraAbbr/era', 'type');
                $temp['narrow']      = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraNarrow/era', 'type');
                break;

            case 'era':
                if (empty($value)) {
                    $value = array("gregorian", "Abbr");
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/eras/era' . $value[1] . '/era', 'type');
                break;

            case 'date':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp  = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'full\']/dateFormat/pattern', '', 'full');
                $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'long\']/dateFormat/pattern', '', 'long');
                $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'medium\']/dateFormat/pattern', '', 'medium');
                $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'short\']/dateFormat/pattern', '', 'short');
                break;

            case 'time':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp  = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'full\']/timeFormat/pattern', '', 'full');
                $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'long\']/timeFormat/pattern', '', 'long');
                $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'medium\']/timeFormat/pattern', '', 'medium');
                $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'short\']/timeFormat/pattern', '', 'short');
                break;

            case 'datetime':
                if (empty($value)) {
                    $value = "gregorian";
                }

                $timefull = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'full\']/timeFormat/pattern', '', 'full');
                $timelong = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'long\']/timeFormat/pattern', '', 'long');
                $timemedi = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'medium\']/timeFormat/pattern', '', 'medi');
                $timeshor = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'short\']/timeFormat/pattern', '', 'shor');

                $datefull = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'full\']/dateFormat/pattern', '', 'full');
                $datelong = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'long\']/dateFormat/pattern', '', 'long');
                $datemedi = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'medium\']/dateFormat/pattern', '', 'medi');
                $dateshor = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'short\']/dateFormat/pattern', '', 'shor');

                $full = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'full\']/dateTimeFormat/pattern', '', 'full');
                $long = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'long\']/dateTimeFormat/pattern', '', 'long');
                $medi = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'medium\']/dateTimeFormat/pattern', '', 'medi');
                $shor = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'short\']/dateTimeFormat/pattern', '', 'shor');

                $temp['full']   = str_replace(array('{0}', '{1}'), array($timefull['full'], $datefull['full']), $full['full']);
                $temp['long']   = str_replace(array('{0}', '{1}'), array($timelong['long'], $datelong['long']), $long['long']);
                $temp['medium'] = str_replace(array('{0}', '{1}'), array($timemedi['medi'], $datemedi['medi']), $medi['medi']);
                $temp['short']  = str_replace(array('{0}', '{1}'), array($timeshor['shor'], $dateshor['shor']), $shor['shor']);
                break;

            case 'dateitem':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $_temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/availableFormats/dateFormatItem', 'id');
                foreach($_temp as $key => $found) {
                    $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/availableFormats/dateFormatItem[@id=\'' . $key . '\']', '', $key);
                }
                break;

            case 'dateinterval':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $_temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/intervalFormats/intervalFormatItem', 'id');
                foreach($_temp as $key => $found) {
                    $temp[$key] = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/intervalFormats/intervalFormatItem[@id=\'' . $key . '\']/greatestDifference', 'id');
                }
                break;

            case 'field':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp2 = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/fields/field', 'type');
                foreach ($temp2 as $key => $keyvalue) {
                    $temp += self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/fields/field[@type=\'' . $key . '\']/displayName', '', $key);
                }
                break;

            case 'relative':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/fields/field/relative', 'type');
                break;

            case 'symbols':
                $temp  = self::_getFile('main/' . $locale, '/ldml/numbers/symbols/decimal',         '', 'decimal');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/group',           '', 'group');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/list',            '', 'list');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/percentSign',     '', 'percent');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/nativeZeroDigit', '', 'zero');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/patternDigit',    '', 'pattern');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/plusSign',        '', 'plus');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/minusSign',       '', 'minus');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/exponential',     '', 'exponent');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/perMille',        '', 'mille');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/infinity',        '', 'infinity');
                $temp += self::_getFile('main/' . $locale, '/ldml/numbers/symbols/nan',             '', 'nan');
                break;

            case 'nametocurrency':
                $_temp = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
                }
                break;

            case 'currencytoname':
                $_temp = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency', 'type');
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
                    if (!isset($val[$key])) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'currencysymbol':
                $_temp = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/symbol', '', $key);
                }
                break;

            case 'question':
                $temp  = self::_getFile('main/' . $locale, '/ldml/posix/messages/yesstr',  '', 'yes');
                $temp += self::_getFile('main/' . $locale, '/ldml/posix/messages/nostr',   '', 'no');
                break;

            case 'currencyfraction':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/fractions/info', 'iso4217');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $key . '\']', 'digits', $key);
                }
                break;

            case 'currencyrounding':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/fractions/info', 'iso4217');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $key . '\']', 'rounding', $key);
                }
                break;

            case 'currencytoregion':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/region', 'iso3166');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
                }
                break;

            case 'regiontocurrency':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/region', 'iso3166');
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
                    if (!isset($val[$key])) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'regiontoterritory':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/territoryContainment/group', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
                }
                break;

            case 'territorytoregion':
                $_temp2 = self::_getFile('supplemental/supplementalData', '/supplementalData/territoryContainment/group', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplemental/supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
                }
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'scripttolanguage':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
                    if (empty($temp[$key])) {
                        unset($temp[$key]);
                    }
                }
                break;

            case 'languagetoscript':
                $_temp2 = self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
                }
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if (empty($found3)) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'territorytolanguage':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
                    if (empty($temp[$key])) {
                        unset($temp[$key]);
                    }
                }
                break;

            case 'languagetoterritory':
                $_temp2 = self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
                }
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if (empty($found3)) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'timezonetowindows':
                $_temp = self::_getFile('supplemental/windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone', 'other');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone[@other=\'' . $key . '\']', 'type', $key);
                }
                break;

            case 'windowstotimezone':
                $_temp = self::_getFile('supplemental/windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone[@type=\'' .$key . '\']', 'other', $key);
                }
                break;

            case 'territorytotimezone':
                $_temp = self::_getFile('supplemental/metaZones', '/supplementalData/metaZones/mapTimezones/mapZone', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/metaZones', '/supplementalData/metaZones/mapTimezones/mapZone[@type=\'' . $key . '\']', 'territory', $key);
                }
                break;

            case 'timezonetoterritory':
                $_temp = self::_getFile('supplemental/metaZones', '/supplementalData/metaZones/mapTimezones/mapZone', 'territory');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplemental/metaZones', '/supplementalData/metaZones/mapTimezones/mapZone[@territory=\'' . $key . '\']', 'type', $key);
                }
                break;

            case 'citytotimezone':
                $_temp = self::_getFile('main/' . $locale, '/ldml/dates/timeZoneNames/zone', 'type');
                foreach($_temp as $key => $found) {
                    $temp += self::_getFile('main/' . $locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
                }
                break;

            case 'timezonetocity':
                $_temp  = self::_getFile('main/' . $locale, '/ldml/dates/timeZoneNames/zone', 'type');
                $temp = array();
                foreach($_temp as $key => $found) {
                    $temp += self::_getFile('main/' . $locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
                    if (!empty($temp[$key])) {
                        $temp[$temp[$key]] = $key;
                    }
                    unset($temp[$key]);
                }
                break;

            case 'phonetoterritory':
                $_temp = self::_getFile('supplemental/telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
                }
                break;

            case 'territorytophone':
                $_temp = self::_getFile('supplemental/telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile('supplemental/telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
                    if (!isset($val[$key])) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'numerictoterritory':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes', 'type');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\'' . $key . '\']', 'numeric', $key);
                }
                break;

            case 'territorytonumeric':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes', 'numeric');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes[@numeric=\'' . $key . '\']', 'type', $key);
                }
                break;

            case 'alpha3toterritory':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes', 'type');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\'' . $key . '\']', 'alpha3', $key);
                }
                break;

            case 'territorytoalpha3':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes', 'alpha3');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes[@alpha3=\'' . $key . '\']', 'type', $key);
                }
                break;

            case 'postaltoterritory':
                $_temp = self::_getFile('supplemental/postalCodeData', '/supplementalData/postalCodeData/postCodeRegex', 'territoryId');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/postalCodeData', '/supplementalData/postalCodeData/postCodeRegex[@territoryId=\'' . $key . '\']', 'territoryId');
                }
                break;

            case 'numberingsystem':
                $_temp = self::_getFile('supplemental/numberingSystems', '/supplementalData/numberingSystems/numberingSystem', 'id');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/numberingSystems', '/supplementalData/numberingSystems/numberingSystem[@id=\'' . $key . '\']', 'digits', $key);
                    if (empty($temp[$key])) {
                        unset($temp[$key]);
                    }
                }
                break;

            case 'chartofallback':
                $_temp = self::_getFile('supplemental/characters', '/supplementalData/characters/character-fallback/character', 'value');
                foreach ($_temp as $key => $keyvalue) {
                    $temp2 = self::_getFile('supplemental/characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
                    $temp[current($temp2)] = $key;
                }
                break;

            case 'fallbacktochar':
                $_temp = self::_getFile('supplemental/characters', '/supplementalData/characters/character-fallback/character', 'value');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
                }
                break;

            case 'localeupgrade':
                $_temp = self::_getFile('supplemental/likelySubtags', '/supplementalData/likelySubtags/likelySubtag', 'from');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplemental/likelySubtags', '/supplementalData/likelySubtags/likelySubtag[@from=\'' . $key . '\']', 'to', $key);
                }
                break;

            case 'unit':
                $_temp = self::_getFile('main/' . $locale, '/ldml/units/unit', 'type');
                foreach($_temp as $key => $keyvalue) {
                    $_temp2 = self::_getFile('main/' . $locale, '/ldml/units/unit[@type=\'' . $key . '\']/unitPattern', 'count');
                    $temp[$key] = $_temp2;
                }
                break;

            default :
                throw new Exception\InvalidArgumentException(
                  "Unknown list ($path) for parsing locale data."
                );
                break;
        }

        if (isset(self::$_cache)) {
          if (self::$_cacheTags) {
                self::$_cache->setItem($id, $temp, array('tags' => array('Zend_Locale')));
          } else {
                self::$_cache->setItem($id, $temp);
          }
        }

        return $temp;
    }

    /**
     * Read the LDML file, get a single path defined value
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $value
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public static function getContent($locale, $path, $value = false)
    {
        $locale = self::_checkLocale($locale);

        if (!isset(self::$_cache) && !self::$_cacheDisabled) {
            self::$_cache = CacheFactory::factory(array(
                'adapter' => array(
                    'name' => 'Filesystem',
                ),
                'plugins' => array(
                    array(
                        'name' => 'serializer',
                        'options' => array(
                            'serializer' => 'php_serialize',
                        ),
                    ),
                ),
            ));
        }

        $val = $value;
        if (is_array($value)) {
            $val = implode('_' , $value);
        }
        $val = urlencode($val);
        $id = strtr('Zend_LocaleC_' . $locale . '_' . $path . '_' . $val, array('-' => '_', '%' => '_', '+' => '_'));
        if (!self::$_cacheDisabled && ($result = self::$_cache->getItem($id))) {
            return $result;
        }

        switch(strtolower($path)) {
            case 'variant':
                $temp = self::_getFile('main/' . $locale, '/ldml/localeDisplayNames/variants/variant[@type=\'' . $value . '\']', 'type');
                break;

            case 'key':
                $temp = self::_getFile('main/' . $locale, '/ldml/localeDisplayNames/keys/key[@type=\'' . $value . '\']', 'type');
                break;

            case 'defaultcalendar':
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/default', 'choice', 'default');
                break;

            case 'monthcontext':
                if (empty ($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/default', 'choice', 'context');
                break;

            case 'defaultmonth':
                if (empty ($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/default', 'choice', 'default');
                break;

            case 'month':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/months/monthContext[@type=\'' . $value[1] . '\']/monthWidth[@type=\'' . $value[2] . '\']/month[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'daycontext':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/default', 'choice', 'context');
                break;

            case 'defaultday':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/default', 'choice', 'default');
                break;

            case 'day':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/days/dayContext[@type=\'' . $value[1] . '\']/dayWidth[@type=\'' . $value[2] . '\']/day[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'quarter':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/quarters/quarterContext[@type=\'' . $value[1] . '\']/quarterWidth[@type=\'' . $value[2] . '\']/quarter[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'am':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/am', '', 'am');
                break;

            case 'pm':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/pm', '', 'pm');
                break;

            case 'era':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "Abbr", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/eras/era' . $value[1] . '/era[@type=\'' . $value[2] . '\']', 'type');
                break;

            case 'defaultdate':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/default', 'choice', 'default');
                break;

            case 'date':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateFormats/dateFormatLength[@type=\'' . $value[1] . '\']/dateFormat/pattern', '', 'pattern');
                break;

            case 'defaulttime':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/default', 'choice', 'default');
                break;

            case 'time':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/timeFormats/timeFormatLength[@type=\'' . $value[1] . '\']/timeFormat/pattern', '', 'pattern');
                break;

            case 'datetime':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }

                $date     = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateFormats/dateFormatLength[@type=\'' . $value[1] . '\']/dateFormat/pattern', '', 'pattern');
                $time     = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/timeFormats/timeFormatLength[@type=\'' . $value[1] . '\']/timeFormat/pattern', '', 'pattern');
                $datetime = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'' . $value[1] . '\']/dateTimeFormat/pattern', '', 'pattern');
                $temp = str_replace(array('{0}', '{1}'), array(current($time), current($date)), current($datetime));
                break;

            case 'dateitem':
                if (empty($value)) {
                    $value = array("gregorian", "yyMMdd");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/availableFormats/dateFormatItem[@id=\'' . $value[1] . '\']', '');
                break;

            case 'dateinterval':
                if (empty($value)) {
                    $value = array("gregorian", "yMd", "y");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp, $temp[0]);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/intervalFormats/intervalFormatItem[@id=\'' . $value[1] . '\']/greatestDifference[@id=\'' . $value[2] . '\']', '');
                break;

            case 'field':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/fields/field[@type=\'' . $value[1] . '\']/displayName', '', $value[1]);
                break;

            case 'relative':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/fields/field/relative[@type=\'' . $value[1] . '\']', '', $value[1]);
                break;

            case 'decimalnumber':
                $temp = self::_getFile('main/' . $locale, '/ldml/numbers/decimalFormats/decimalFormatLength/decimalFormat/pattern', '', 'default');
                break;

            case 'scientificnumber':
                $temp = self::_getFile('main/' . $locale, '/ldml/numbers/scientificFormats/scientificFormatLength/scientificFormat/pattern', '', 'default');
                break;

            case 'percentnumber':
                $temp = self::_getFile('main/' . $locale, '/ldml/numbers/percentFormats/percentFormatLength/percentFormat/pattern', '', 'default');
                break;

            case 'currencynumber':
                $temp = self::_getFile('main/' . $locale, '/ldml/numbers/currencyFormats/currencyFormatLength/currencyFormat/pattern', '', 'default');
                break;

            case 'nametocurrency':
                $temp = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/displayName', '', $value);
                break;

            case 'currencytoname':
                $temp = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/displayName', '', $value);
                $_temp = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency', 'type');
                $temp = array();
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
                    if (!isset($val[$key]) or ($val[$key] != $value)) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'currencysymbol':
                $temp = self::_getFile('main/' . $locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/symbol', '', $value);
                break;

            case 'question':
                $temp = self::_getFile('main/' . $locale, '/ldml/posix/messages/' . $value . 'str',  '', $value);
                break;

            case 'currencyfraction':
                if (empty($value)) {
                    $value = "DEFAULT";
                }
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $value . '\']', 'digits', 'digits');
                break;

            case 'currencyrounding':
                if (empty($value)) {
                    $value = "DEFAULT";
                }
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $value . '\']', 'rounding', 'rounding');
                break;

            case 'currencytoregion':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $value . '\']/currency', 'iso4217', $value);
                break;

            case 'regiontocurrency':
                $_temp = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/region', 'iso3166');
                $temp = array();
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile('supplemental/supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
                    if (!isset($val[$key]) or ($val[$key] != $value)) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'regiontoterritory':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $value . '\']', 'contains', $value);
                break;

            case 'territorytoregion':
                $_temp2 = self::_getFile('supplemental/supplementalData', '/supplementalData/territoryContainment/group', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplemental/supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'scripttolanguage':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language[@type=\'' . $value . '\']', 'scripts', $value);
                break;

            case 'languagetoscript':
                $_temp2 = self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'territorytolanguage':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language[@type=\'' . $value . '\']', 'territories', $value);
                break;

            case 'languagetoterritory':
                $_temp2 = self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplemental/supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'timezonetowindows':
                $temp = self::_getFile('supplemental/windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone[@other=\''.$value.'\']', 'type', $value);
                break;

            case 'windowstotimezone':
                $temp = self::_getFile('supplemental/windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone[@type=\''.$value.'\']', 'other', $value);
                break;

            case 'territorytotimezone':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/timezoneData/zoneFormatting/zoneItem[@type=\'' . $value . '\']', 'territory', $value);
                break;

            case 'timezonetoterritory':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/timezoneData/zoneFormatting/zoneItem[@territory=\'' . $value . '\']', 'type', $value);
                break;

            case 'citytotimezone':
                $temp = self::_getFile('main/' . $locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $value . '\']/exemplarCity', '', $value);
                break;

            case 'timezonetocity':
                $_temp  = self::_getFile('main/' . $locale, '/ldml/dates/timeZoneNames/zone', 'type');
                $temp = array();
                foreach($_temp as $key => $found) {
                    $temp += self::_getFile('main/' . $locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
                    if (!empty($temp[$key])) {
                        if ($temp[$key] == $value) {
                            $temp[$temp[$key]] = $key;
                        }
                    }
                    unset($temp[$key]);
                }
                break;

            case 'phonetoterritory':
                $temp = self::_getFile('supplemental/telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $value . '\']/telephoneCountryCode', 'code', $value);
                break;

            case 'territorytophone':
                $_temp2 = self::_getFile('supplemental/telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplemental/telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'numerictoterritory':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\''.$value.'\']', 'numeric', $value);
                break;

            case 'territorytonumeric':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes[@numeric=\''.$value.'\']', 'type', $value);
                break;

            case 'alpha3toterritory':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\''.$value.'\']', 'alpha3', $value);
                break;

            case 'territorytoalpha3':
                $temp = self::_getFile('supplemental/supplementalData', '/supplementalData/codeMappings/territoryCodes[@alpha3=\''.$value.'\']', 'type', $value);
                break;

            case 'postaltoterritory':
                $temp = self::_getFile('supplemental/postalCodeData', '/supplementalData/postalCodeData/postCodeRegex[@territoryId=\'' . $value . '\']', 'territoryId');
                break;

            case 'numberingsystem':
                $temp = self::_getFile('supplemental/numberingSystems', '/supplementalData/numberingSystems/numberingSystem[@id=\'' . strtolower($value) . '\']', 'digits', $value);
                break;

            case 'chartofallback':
                $_temp = self::_getFile('supplemental/characters', '/supplementalData/characters/character-fallback/character', 'value');
                foreach ($_temp as $key => $keyvalue) {
                    $temp2 = self::_getFile('supplemental/characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
                    if (current($temp2) == $value) {
                        $temp = $key;
                    }
                }
                break;

                $temp = self::_getFile('supplemental/characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $value . '\']/substitute', '', $value);
                break;

            case 'fallbacktochar':
                $temp = self::_getFile('supplemental/characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $value . '\']/substitute', '');
                break;

            case 'localeupgrade':
                $temp = self::_getFile('supplemental/likelySubtags', '/supplementalData/likelySubtags/likelySubtag[@from=\'' . $value . '\']', 'to', $value);
                break;

            case 'unit':
                $temp = self::_getFile('main/' . $locale, '/ldml/units/unit[@type=\'' . $value[0] . '\']/unitPattern[@count=\'' . $value[1] . '\']', '');
                break;

            default :
                throw new Exception\InvalidArgumentException(
                  "Unknown detail ($path) for parsing locale data."
                );
                break;
        }

        if (is_array($temp)) {
            $temp = current($temp);
        }

        if (self::$_cacheTags) {
            self::$_cache->setItem($id, $temp, array('tags' => array('Zend_Locale')));
      } else {
            self::$_cache->setItem($id, $temp);
      }

        return $temp;
    }

    /**
     * Internal function for checking the locale
     *
     * @param string|Locale $locale Locale to check
     * @return string
     */
    protected static function _checkLocale($locale)
    {
        if (empty($locale)) {
            $locale = new Locale();
        }

        if (!(Locale::isLocale((string) $locale))) {
            throw new Exception\InvalidArgumentException(
              "Locale (" . (string) $locale . ") is no known locale"
            );
        }

        return (string) $locale;
    }

    /**
     * Returns the path to CLDR
     *
     * @return string
     */
    public static function getPath()
    {
        if (empty(self::$_path)) {
            self::setDefaultPath();
        }

        return self::$_path;
    }

    /**
     * Sets the path to CLDR
     *
     * @param  string $path Path to CLDR files
     * @throws Exception\UnexpectedValueException When CLDR files can not be found
     * @return void
     */
    public static function setPath($path)
    {
        if (!is_dir($path)) {
            throw new Exception\UnexpectedValueException('The given path needs to be a directory');
        }

        if (!file_exists($path . '/main/root.xml')) {
            throw new Exception\UnexpectedValueException('Unable to find locale files within the given path');
        }

        if (!file_exists($path . '/supplemental/supplementalData.xml')) {
            throw new Exception\UnexpectedValueException('Unable to find supplemental files within the given path');
        }

        self::$_path = $path;
    }

    protected static function setDefaultPath()
    {
        self::setPath(__DIR__ . '/../../../../resources/cldr');
    }

    /**
     * Sets the default cache
     *
     * @return void
     */
    protected static function setDefaultCache()
    {
        if (!is_dir(self::getPath() . '/cache')) {
            mkdir(self::getPath() . '/cache', 0, true);
        }

        if (!is_dir(self::getPath() . '/cache')) {
            // caching impossible... throw notice
            return false;
        }

        self::setCache(CacheFactory::factory(array(
            'adapter' => array(
                'name' => 'Filesystem',
                'options' => array(
                    'cache_dir'         => self::getPath() . '/cache',
                    'ttl'               => 0,
                    'read_control'      => true,
                    'read_control_algo' => 'strlen',
                ),
            ),
            'plugins' => array(
                array(
                    'name' => 'serializer',
                    'options' => array(
                        'serializer' => 'php_serialize',
                    ),
                ),
                array(
                    'name' => 'clear_by_factor',
                    'options' => array(
                        'clearing_factor' => 0,
                    ),
                ),
            )
        )));

        self::_getTagSupportForCache();
    }

    /**
     * Internal method to read CLDR details
     *
     * @param  string $file
     * @param  string $keyPath
     * @param  string $keyAttrib
     * @param  string $valuePath
     * @param  string $valueAttrib
     * @return string|array
     */
    protected static function readCldrDetail($filePath, $locale, $keyPath, $keyAttrib, $valuePath, $valueAttrib)
    {
        if (!file_exists($filePath . '/' . $locale . '.xml')) {
            // TODO: Throw warning on non-existing locale file
        }

        $file   = simplexml_load_file($filePath . '/' . $locale . '.xml');
        $result = $file->xpath($keyPath);
        if (!empty($result)) {
            foreach($result as $element) {
                if ($keyAttrib === null) {
                    $keys[] = (string) $element;
                } else {
                    $keys[] = (string) $element[$keyAttrib];
                }
            }

            $result = $file->xpath($valuePath);
            foreach($result as $element) {
                if ($valueAttrib === null) {
                    $values[] = (string) $element;
                } else {
                    $values[] = (string) $element[$valueAttrib];
                }
            }

            foreach($keys as $index => $key) {
                if (!array_key_exists($key, self::$_result)) {
                    self::$_result[$key] = $values[$index];
                }
            }
            self::$_result = self::$_result + array_combine($keys, $values);
        } else {
            $result = $file->xpath($keyPath . '/alias');
            if (!empty($result)) {
                $source = $result[0]['source'];
                $path   = $result[0]['path'];

                if ($source !== 'locale') {
                    $locale = $source;
                }

                self::readCldrFile($filePath, $locale, $keyPath . '/' . $source, $keyAttrib, $valuePath, $valueAttrib);
            }
        }
    }

    /**
     * Internal method to read CLDR files by defined inheritance
     * and return an array with the requested informations
     *
     * @param  string $filePath
     * @param  string $locale
     * @param  string $keyPath
     * @param  string $keyAttrib
     * @param  string $valuePath
     * @param  string $valueAttrib
     * @throws Exception\UnexpectedValueException When filepath is no directory
     * @return string|array
     */
    protected static function readCldrFile($filePath, $locale, $keyPath, $keyAttrib, $valuePath, $valueAttrib)
    {
        self::readCldrDetail($filePath, $locale, $keyPath, $keyAttrib, $valuePath, $valueAttrib);

        if ($locale !== 'root') {
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));

            if (empty($locale)) {
                $locale = 'root';
            }

            self::readCldrFile($filePath, $locale, $keyPath, $keyAttrib, $valuePath, $valueAttrib);
        }
    }

    /**
     * Returns informations from CLDR
     *
     * @param  string $filePath
     * @param  string $locale
     * @param  string $cacheId
     * @param  string $keyPath
     * @param  string $keyAttrib
     * @param  string $valuePath
     * @param  string $valueAttrib
     * @param  string $detail
     * @throws Exception\UnexpectedValueException When filepath is no directory
     * @return string|array
     */
    protected static function readCldr($filePath, $locale, $cacheId, $keyPath, $keyAttrib, $valuePath, $valueAttrib, $detail)
    {
        $locale = self::_checkLocale($locale);

        if (self::getCache() === null && !self::isCacheDisabled()) {
            self::setDefaultCache();
        }

        if (!is_dir($filePath)) {
            throw new Exception\UnexpectedValueException('The given path needs to be a directory');
        }

        if (self::getCache() !== null && !self::isCacheDisabled()) {
            if ($result = self::getCache()->getItem($cacheId)) {
                if ($detail !== null) {
                    return $result[$detail];
                }

                return $result;
            }
        }

        self::$_result = array();
        self::readCldrFile($filePath, $locale, $keyPath, $keyAttrib, $valuePath, $valueAttrib);
        ksort(self::$_result);

        if (self::hasCacheTagSupport()) {
            self::getCache()->setItem($cacheId, self::$_result, array('tags' => array('Zend_Locale')));
        } elseif (self::hasCache()) {
            self::getCache()->setItem($cacheId, self::$_result);
        }

        if ($detail !== null) {
            return self::$_result[$detail];
        }

        return self::$_result;
    }

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
        return self::getDisplayData($locale, 'CldrLanguage', 'languages/language', $invert, $detail);
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
        return self::getDisplayData($locale, 'CldrScript', 'scripts/script', $invert, $detail);
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
        return self::getDisplayData($locale, 'CldrTerritory', 'territories/territory', $invert, $detail);
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
        return self::getDisplayData($locale, 'CldrVariant', 'variants/variant', $invert, $detail);
    }

    /**
     * Helper method for reading display data.
     * Used by self::getDisplay*() methods.
     *
     * @param string    $locale Normalized locale
     * @param string    $cacheId
     * @param string    $path
     * @param boolean   $invert
     * @param string    $detail
     * @return array
     */
    protected static function getDisplayData($locale, $cacheId, $path, $invert, $detail)
    {
        $path = '//ldml/localeDisplayNames/' . $path;
        $type1 = 'type';
        $type2 = null;
        if ($invert) {
            $type1 = null;
            $type2 = 'type';
            $cacheId = $cacheId . '1' . $locale;
        } else {
            $cacheId = $cacheId . '0' . $locale;
        }

        return self::readCldr(
            self::getPath() . '/main', (string) $locale,
            $cacheId, $path, $type1, $path, $type2, $detail
        );
    }

// Formatierungen in Klassen integrieren
// Mathklassen überarbeiten
// Get SHORT types

// getLocaleDisplayPattern
// getKey
// getType (Key)
// getTransformName
// getMeasurementSystemName
// getCodePattern
// getExemplarCharacter
// getDelimiter
// getDateFormat (calendar)
// getDateTimeFormat (calendar) - length
// getMonth (calendar, context, width) -
// getDay (calendar, context, width)
//
}
