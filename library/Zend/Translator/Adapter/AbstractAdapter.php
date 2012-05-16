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
 * @subpackage Zend_Translator_Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Translator\Adapter;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use RecursiveDirectoryIterator,
    RecursiveIteratorIterator,
    RecursiveRegexIterator,
    Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter,
    Zend\Log,
    Zend\Locale,
    Zend\Translator,
    Zend\Translator\Plural,
    Zend\Translator\Exception;

/**
 * Abstract adapter class for each translation source adapter
 *
 * @category   Zend
 * @package    Zend_Translator
 * @subpackage Zend_Translator_Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAdapter
{
    /**
     * Shows if locale detection is in automatic level
     * @var boolean
     */
    private $_automatic = true;

    /**
     * Internal value to see already routed languages
     * @var array()
     */
    private $_routed = array();

    /**
     * Internal cache for all adapters
     * @var CacheAdapter
     */
    protected static $_cache     = null;

    /**
     * Internal value to remember if cache supports tags
     *
     * @var boolean
     */
    private static $_cacheTags = false;

    /**
     * Scans for the locale within the name of the directory
     * @constant integer
     */
    const LOCALE_DIRECTORY = 'directory';

    /**
     * Scans for the locale within the name of the file
     * @constant integer
     */
    const LOCALE_FILENAME  = 'filename';

    /**
     * Array with all options, each adapter can have own additional options
     *   'clear'           => when true, clears already loaded translations when adding new files
     *   'content'         => content to translate or file or directory with content
     *   'disableNotices'  => when true, omits notices from being displayed
     *   'ignore'          => a prefix for files and directories which are not being added
     *   'locale'          => the actual set locale to use
     *   'log'             => a instance of Zend_Log where logs are written to
     *   'logMessage'      => message to be logged
     *   'logPriority'     => priority which is used to write the log message
     *   'logUntranslated' => when true, untranslated messages are not logged
     *   'reload'          => reloads the cache by reading the content again
     *   'route'           => adds routing for for not found translations
     *   'routeHttp'       => when true, uses routing by HTTP_ACCEPT_HEADER
     *   'scan'            => searches for translation files using the LOCALE constants
     *   'tag'             => tag to use for the cache
     *
     * @var array
     */
    protected $_options = array(
        'clear'           => false,
        'content'         => null,
        'disableNotices'  => false,
        'ignore'          => '.',
        'locale'          => 'auto',
        'log'             => null,
        'logMessage'      => "Untranslated message within '%locale%': %message%",
        'logPriority'     => 5,
        'logUntranslated' => false,
        'reload'          => false,
        'route'           => null,
        'routeHttp'       => true,
        'scan'            => null,
        'tag'             => 'Zend_Translator'
    );

    /**
     * Translation table
     * @var array
     */
    protected $_translate = array();

    /**
     * Generates the adapter
     *
     * @param  array|Traversable $options Translation options for this adapter
     * @throws \Zend\Translator\Exception\InvalidArgumentException
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['content'] = array_shift($args);

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } else if (!is_array($options)) {
            $options = array('content' => $options);
        }

        if (array_key_exists('cache', $options)) {
            self::setCache($options['cache']);
            unset($options['cache']);
        }

        if (isset(self::$_cache)) {
            $id = 'Zend_Translator_' . $this->toString() . '_Options';
            $result = self::$_cache->getItem($id);
            if ($result) {
                $this->_options = $result;
            }
        }

        if (empty($options['locale']) || ($options['locale'] === "auto")) {
            $this->_automatic = true;
        } else {
            $this->_automatic = false;
        }

        $locale = null;
        if (!empty($options['locale'])) {
            $locale = $options['locale'];
            unset($options['locale']);
        }

        $this->setOptions($options);
        $options['locale'] = $locale;

        if (!empty($options['content'])) {
            $this->addTranslation($options);
        }

        if ($this->getLocale() !== (string) $options['locale']) {
            if (!empty($this->_options['route'])) {
                $locale = $options['locale'];
                if ($locale == 'auto') {
                    $locale = Locale\Locale::findLocale($locale);
                }

                while (true) {
                    if (!empty($this->_translate[$locale])) {
                        break;
                    }

                    if (empty($this->_options['route'][$locale])) {
                        break;
                    } else {
                        $locale = $this->_options['route'][$locale];
                    }
                }

                $this->setLocale($locale);
            } else {
                $this->setLocale($options['locale']);
            }
        }
    }

    /**
     * Add translations
     *
     * This may be a new language or additional content for an existing language
     * If the key 'clear' is true, then translations for the specified
     * language will be replaced and added otherwise
     *
     * @param  array|Traversable $options Options and translations to be added
     * @throws \Zend\Translator\Exception\InvalidArgumentException
     * @return \Zend\Translator\Adapter\AbstractAdapter Provides fluent interface
     */
    public function addTranslation($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } else if (func_num_args() > 1) {
            $args = func_get_args();
            $options            = array();
            $options['content'] = array_shift($args);

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } else if (!is_array($options)) {
            $options = array('content' => $options);
        }

        if (!isset($options['content']) || empty($options['content'])) {
            throw new Exception\InvalidArgumentException('Missing content for translation');
        }

        $originate = null;
        if (!empty($options['locale'])) {
            $originate = (string) $options['locale'];
        }

        if ((array_key_exists('log', $options)) && !($options['log'] instanceof Log\Logger)) {
            throw new Exception\InvalidArgumentException('Instance of Zend_Log_Logger expected for option log');
        }

        try {
            if (!($options['content'] instanceof Translator\Translator) && !($options['content'] instanceof AbstractAdapter)) {
                if (empty($options['locale'])) {
                    $options['locale'] = null;
                }

                $options['locale'] = Locale\Locale::findLocale($options['locale']);
            } else if (empty($options['locale'])) {
                $originate = (string) $this->_options['locale'];
                $options['locale'] = $options['content']->getLocale();
            }
        } catch (Locale\Exception\ExceptionInterface $e) {
            throw new Exception\InvalidArgumentException("The given Language '{$options['locale']}' does not exist", 0, $e);
        }

        $options  = $options + $this->_options;
        if (is_string($options['content']) and is_dir($options['content'])) {
            $test = realpath($options['content']);
            if ($test !== false) {
                $options['content'] = $test;
            }
            $search = strlen($options['content']);

            $prev = '';
            if (DIRECTORY_SEPARATOR == '\\') {
                $separator = '\\\\';
            } else {
                $separator = DIRECTORY_SEPARATOR;
            }

            if (is_array($options['ignore'])) {
                $ignore = '{';
                foreach($options['ignore'] as $key => $match) {
                    if (strpos($key, 'regex') !== false) {
                        $ignore .= $match . '|';
                    } else {
                        $ignore .= $separator . $match . '|';
                    }
                }

                $ignore = substr($ignore, 0, -1) . '}u';
            } else {
                $ignore = '{' . $separator . $options['ignore'] . '}u';
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveRegexIterator(
                     new RecursiveDirectoryIterator($options['content'], RecursiveDirectoryIterator::KEY_AS_PATHNAME),
                     $ignore,
                     RecursiveRegexIterator::MATCH
                ),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $directory => $info) {
                $file = $info->getFilename();
                $original = substr($directory, $search);
                if (is_array($options['ignore'])) {
                    foreach ($options['ignore'] as $key => $hop) {
                        if (strpos($key, 'regex') !== false) {
                            if (preg_match($hop, $original)) {
                                // ignore files matching the given regex from option 'ignore' and all files below
                                continue 2;
                            }
                        } else if (strpos($original, DIRECTORY_SEPARATOR . $hop) !== false) {
                            // ignore files matching first characters from option 'ignore' and all files below
                            continue 2;
                        }
                    }
                } else {
                    if (strpos($original, DIRECTORY_SEPARATOR . $options['ignore']) !== false) {
                        // ignore files matching first characters from option 'ignore' and all files below
                        continue;
                    }
                }

                if ($info->isDir()) {
                    // pathname as locale
                    if (($options['scan'] === self::LOCALE_DIRECTORY) and (Locale\Locale::isLocale($file, true))) {
                        $options['locale'] = $file;
                        $prev              = (string) $options['locale'];
                    }
                } else if ($info->isFile()) {
                    // filename as locale
                    if ($options['scan'] === self::LOCALE_FILENAME) {
                        $filename = explode('.', $file);
                        array_pop($filename);
                        $filename = implode('.', $filename);
                        if (Locale\Locale::isLocale((string) $filename, true)) {
                            $options['locale'] = (string) $filename;
                        } else {
                            $parts  = explode('.', $file);
                            $parts2 = array();
                            foreach($parts as $token) {
                                $parts2 += explode('_', $token);
                            }
                            $parts  = array_merge($parts, $parts2);
                            $parts2 = array();
                            foreach($parts as $token) {
                                $parts2 += explode('-', $token);
                            }
                            $parts = array_merge($parts, $parts2);
                            $parts = array_unique($parts);
                            $prev  = '';
                            foreach($parts as $token) {
                                if (Locale\Locale::isLocale($token, true)) {
                                    if (strlen($prev) <= strlen($token)) {
                                        $options['locale'] = $token;
                                        $prev              = $token;
                                    }
                                }
                            }
                        }
                    }

                    try {
                        $options['content'] = $info->getPathname();
                        $this->_addTranslationData($options);
                    } catch (Exception $e) {
                        // ignore failed sources while scanning
                    }
                }
            }
        } else {
            $this->_addTranslationData($options);
        }

        if ((isset($this->_translate[$originate]) === true) and (count($this->_translate[$originate]) > 0)) {
            $this->setLocale($originate);
        }

        return $this;
    }

    /**
     * Sets new adapter options
     *
     * @param  array $options Adapter options
     * @throws \Zend\Translator\Exception\InvalidArgumentException
     * @return \Zend\Translator\Adapter\AbstractAdapter Provides fluent interface
     */
    public function setOptions(array $options = array())
    {
        $change = false;
        $locale = null;
        if (isset($options['routeHttp']) && !empty($options['routeHttp'])) {
            $routing = Locale\Locale::getBrowser();
            arsort($routing);
            $route = array();
            reset($routing);
            $prev = key($routing);
            foreach($routing as $language => $quality) {
                if ($prev == $language) {
                    continue;
                }

                $route[$prev] = $language;
                $prev         = $language;
            }

            if (!empty($route)) {
                if (isset($options['route'])) {
                    $options['route'] = array_merge($route, $options['route']);
                } else {
                    $options['route'] = $route;
                }
            }
        }

        foreach ($options as $key => $option) {
            if ($key == 'locale') {
                $locale = $option;
            } else if ((isset($this->_options[$key]) and ($this->_options[$key] !== $option)) or
                    !isset($this->_options[$key])) {
                if (($key == 'log') && !($option instanceof Log\Logger)) {
                    throw new Exception\InvalidArgumentException('Instance of Zend_Log expected for option log');
                }

                if ($key == 'cache') {
                    self::setCache($option);
                    continue;
                }

                $this->_options[$key] = $option;
                if ($key != 'log') {
                    $change = true;
                }
            }
        }

        if ($locale !== null) {
            $this->setLocale($locale);
        }

        if (isset(self::$_cache) and ($change == true)) {
            $id = 'Zend_Translator_' . $this->toString() . '_Options';
            $this->saveCache($this->_options, $id);
        }

        return $this;
    }

    /**
     * Returns the adapters name and it's options
     *
     * @param  string|null $optionKey String returns this option
     *                                null returns all options
     * @return integer|string|array|null
     */
    public function getOptions($optionKey = null)
    {
        if ($optionKey === null) {
            return $this->_options;
        }

        if (isset($this->_options[$optionKey]) === true) {
            return $this->_options[$optionKey];
        }

        return null;
    }

    /**
     * Gets locale
     *
     * @return \Zend\Locale\Locale|string|null
     */
    public function getLocale()
    {
        return $this->_options['locale'];
    }

    /**
     * Sets locale
     *
     * @param  string|\Zend\Locale\Locale $locale Locale to set
     * @throws \Zend\Translator\Exception\InvalidArgumentException
     * @return \Zend\Translator\Adapter\AbstractAdapter Provides fluent interface
     */
    public function setLocale($locale)
    {
        if (($locale === "auto") or ($locale === null)) {
            $this->_automatic = true;
        } else {
            $this->_automatic = false;
        }

        try {
            $locale = Locale\Locale::findLocale($locale);
        } catch (Locale\Exception\ExceptionInterface $e) {
            throw new Exception\InvalidArgumentException("The given Language ({$locale}) does not exist", 0, $e);
        }

        if (!isset($this->_translate[$locale])) {
            $temp = explode('_', $locale);
            if (!isset($this->_translate[$temp[0]]) and !isset($this->_translate[$locale])) {
                if (($this->_automatic == true) && (!empty($this->_options['route'])) &&
                        array_key_exists($locale, $this->_options['route'])) {
                    $this->_routed[$locale] = true;
                    return $this->setLocale($this->_options['route'][$locale]);
                } else if (($this->_automatic == true) && (!empty($this->_options['route'])) &&
                        array_key_exists($temp[0], $this->_options['route'])) {
                    $this->_routed[$temp[0]] = true;
                    return $this->setLocale($this->_options['route'][$temp[0]]);
                } else if (!$this->_options['disableNotices']) {
                    if ($this->_options['log']) {
                        $this->_options['log']->log($this->_options['logPriority'], "The language '{$locale}' has to be added before it can be used.");
                    } else {
                        trigger_error("The language '{$locale}' has to be added before it can be used.", E_USER_NOTICE);
                    }
                }
            }

            $locale = $temp[0];
        }

        if (empty($this->_translate[$locale])) {
            if (!$this->_options['disableNotices']) {
                if ($this->_options['log']) {
                    $this->_options['log']->log($this->_options['logPriority'], "No translation for the language '{$locale}' available.");
                } else {
                    trigger_error("No translation for the language '{$locale}' available.", E_USER_NOTICE);
                }
            }
        }

        if ($this->_options['locale'] != $locale) {
            $this->_options['locale'] = $locale;
        }

        $this->_routed = array();
        return $this;
    }

    /**
     * Returns the available languages from this adapter
     *
     * @return array
     */
    public function getList()
    {
        $list = array_keys($this->_translate);
        $result = array();
        foreach($list as $value) {
            if (!empty($this->_translate[$value])) {
                $result[$value] = $value;
            }
        }
        return $result;
    }

    /**
     * Returns the message id for a given translation
     * If no locale is given, the actual language will be used
     *
     * @param  string             $message Message to get the key for
     * @param  string|\Zend\Locale\Locale $locale (optional) Language to return the message ids from
     * @return string|array|false
     */
    public function getMessageId($message, $locale = null)
    {
        if (empty($locale) or !$this->isAvailable($locale)) {
            $locale = $this->_options['locale'];
        }

        return array_search($message, $this->_translate[(string) $locale]);
    }

    /**
     * Returns all available message ids from this adapter
     * If no locale is given, the actual language will be used
     *
     * @param  string|\Zend\Locale\Locale $locale (optional) Language to return the message ids from
     * @return array
     */
    public function getMessageIds($locale = null)
    {
        if (empty($locale) or !$this->isAvailable($locale)) {
            $locale = $this->_options['locale'];
        }

        return array_keys($this->_translate[(string) $locale]);
    }

    /**
     * Returns all available translations from this adapter
     * If no locale is given, the actual language will be used
     * If 'all' is given the complete translation dictionary will be returned
     *
     * @param  string|\Zend\Locale\Locale $locale (optional) Language to return the messages from
     * @return array
     */
    public function getMessages($locale = null)
    {
        if ($locale === 'all') {
            return $this->_translate;
        }

        if ((empty($locale) === true) or ($this->isAvailable($locale) === false)) {
            $locale = $this->_options['locale'];
        }

        return $this->_translate[(string) $locale];
    }

    /**
     * Is the wished language available ?
     *
     * @see    Zend_Locale
     * @param  string|\Zend\Locale\Locale $locale Language to search for, identical with locale identifier,
     *                                    @see Zend_Locale for more information
     * @return boolean
     */
    public function isAvailable($locale)
    {
        $return = isset($this->_translate[(string) $locale]);
        return $return;
    }

    /**
     * Load translation data
     *
     * @param  mixed              $data
     * @param  string|\Zend\Locale\Locale $locale
     * @param  array              $options (optional)
     * @return array
     */
    abstract protected function _loadTranslationData($data, $locale, array $options = array());

    /**
     * Internal function for adding translation data
     *
     * This may be a new language or additional data for an existing language
     * If the options 'clear' is true, then the translation data for the specified
     * language is replaced and added otherwise
     *
     * @see    Zend_Locale
     * @param  array|Traversable $options Translation data to add
     * @throws \Zend\Translator\Exception\InvalidArgumentException
     * @return \Zend\Translator\Adapter\AbstractAdapter Provides fluent interface
     */
    private function _addTranslationData($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } else if (func_num_args() > 1) {
            $args = func_get_args();
            $options['content'] = array_shift($args);

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $options += array_shift($args);
            }
        }

        if (($options['content'] instanceof Translator\Translator) || ($options['content'] instanceof AbstractAdapter)) {
            $options['usetranslateadapter'] = true;
            $content = $options['content'];
            if (empty($options['locale']) || ($options['locale'] == 'auto')) {
                $locales = $content->getList();
            } else {
                $locales = array(1 => $options['locale']);
            }

            foreach ($locales as $locale) {
                $options['locale']  = $locale;
                $options['content'] = $content->getMessages($locale);
                $this->_addTranslationData($options);
            }

            return $this;
        }

        try {
            $options['locale'] = Locale\Locale::findLocale($options['locale']);
        } catch (Locale\Exception\ExceptionInterface $e) {
            throw new Exception\InvalidArgumentException("The given Language '{$options['locale']}' does not exist", 0, $e);
        }

        if ($options['clear'] || !isset($this->_translate[$options['locale']])) {
            $this->_translate[$options['locale']] = array();
        }

        $read = true;
        if (isset(self::$_cache)) {
            $id = 'Zend_Translator_' . md5(serialize($options['content'])) . '_' . $this->toString();
            $temp = self::$_cache->getItem($id);
            if ($temp) {
                $read = false;
            }
        }

        if ($options['reload']) {
            $read = true;
        }

        if ($read) {
            if (!empty($options['usetranslateadapter'])) {
                $temp = array($options['locale'] => $options['content']);
            } else {
                $temp = $this->_loadTranslationData($options['content'], $options['locale'], $options);
            }
        }

        if (empty($temp)) {
            $temp = array();
        }

        $keys = array_keys($temp);
        foreach($keys as $key) {
            if (!isset($this->_translate[$key])) {
                $this->_translate[$key] = array();
            }

            if (array_key_exists($key, $temp) && is_array($temp[$key])) {
                $this->_translate[$key] = $temp[$key] + $this->_translate[$key];
            }
        }

        if ($this->_automatic === true) {
            $find    = new Locale\Locale($options['locale']);
            $browser = $find->getEnvironment() + $find->getBrowser();
            arsort($browser);
            foreach($browser as $language => $quality) {
                if (isset($this->_translate[$language])) {
                    $this->_options['locale'] = $language;
                    break;
                }
            }
        }

        if (($read) and (isset(self::$_cache))) {
            $id = 'Zend_Translator_' . md5(serialize($options['content'])) . '_' . $this->toString();
            $this->saveCache($temp, $id);
        }

        return $this;
    }

    /**
     * Translates the given string
     * returns the translation
     *
     * @see Zend_Locale
     * @param  string|array       $messageId Translation string, or Array for plural translations
     * @param  string|\Zend\Locale\Locale $locale    (optional) Locale/Language to use, identical with
     *                                       locale identifier, @see Zend_Locale for more information
     * @return string
     */
    public function translate($messageId, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_options['locale'];
        }

        $plural = null;
        $origId = $messageId;
        if (is_array($messageId)) {
            if (count($messageId) > 2) {
                $number = array_pop($messageId);
                if (!is_numeric($number)) {
                    $plocale = $number;
                    $number  = array_pop($messageId);
                } else {
                    $plocale = 'en';
                }

                $plural    = $messageId;
                $messageId = $messageId[0];
            } else {
                $messageId = $messageId[0];
            }
        }

        if (!Locale\Locale::isLocale($locale, true)) {
            // use rerouting when enabled
            if (!empty($this->_options['route'])) {
                if (array_key_exists($locale, $this->_options['route']) &&
                    !array_key_exists($locale, $this->_routed)) {
                    $this->_routed[$locale] = true;
                    return $this->translate($origId, $this->_options['route'][$locale]);
                }
            }

            $temp = explode('_', $locale);
            if (!Locale\Locale::isLocale($temp[0], true)) {
                // language does not exist, return original string
                if (!empty($this->_options['route'])) {
                    if (array_key_exists($temp[0], $this->_options['route']) &&
                        !array_key_exists($temp[0], $this->_routed)) {
                        $this->_routed[$temp[0]] = true;
                        return $this->translate($origId, $this->_options['route'][$temp[0]]);
                    }
                }
                $this->_log($messageId, $locale);

                $this->_routed = array();
                if ($plural === null) {
                    return $messageId;
                }

                $rule = Plural::getPlural($number, $plocale);
                if (!isset($plural[$rule])) {
                    $rule = 0;
                }

                return $plural[$rule];
            }

            $locale = new Locale\Locale($locale);
        }

        $locale = (string) $locale;
        if ((is_string($messageId) || is_int($messageId)) && isset($this->_translate[$locale][$messageId])) {
            // return original translation
            if ($plural === null) {
                $this->_routed = array();
                return $this->_translate[$locale][$messageId];
            }

            $rule = Plural::getPlural($number, $locale);
            if (is_array($this->_translate[$locale][$plural[0]]) && isset($this->_translate[$locale][$plural[0]][$rule])) {
                $this->_routed = array();
                return $this->_translate[$locale][$plural[0]][$rule];
            }
        } else if (!empty($this->_options['route']) && array_key_exists($locale, $this->_options['route']) &&
                !array_key_exists($locale, $this->_routed)) {
            $this->_routed[$locale] = true;
            return $this->translate($origId, $this->_options['route'][$locale]);
        } else if (strlen($locale) != 2) {
            // faster than creating a new locale and separate the leading part
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));

            if ((is_string($messageId) || is_int($messageId)) && isset($this->_translate[$locale][$messageId])) {
                // return regionless translation (en_US -> en)
                if ($plural === null) {
                    $this->_routed = array();
                    return $this->_translate[$locale][$messageId];
                }

                $rule = Plural::getPlural($number, $locale);
                if (isset($this->_translate[$locale][$plural[0]][$rule])) {
                    $this->_routed = array();
                    return $this->_translate[$locale][$plural[0]][$rule];
                }
            }
        }

        // use rerouting when enabled
        if (!empty($this->_options['route'])) {
            if (array_key_exists($locale, $this->_options['route']) &&
                !array_key_exists($locale, $this->_routed)) {
                $this->_routed[$locale] = true;
                return $this->translate($origId, $this->_options['route'][$locale]);
            }
        }
        $this->_log($messageId, $locale);

        $this->_routed = array();
        if ($plural === null) {
            return $messageId;
        }

        $rule = Plural::getPlural($number, $plocale);
        if (!isset($plural[$rule])) {
            $rule = 0;
        }

        return $plural[$rule];
    }

    /**
     * Translates the given string using plural notations
     * Returns the translated string
     *
     * @see Zend_Locale
     * @param  string             $singular Singular translation string
     * @param  string             $plural   Plural translation string
     * @param  integer            $number   Number for detecting the correct plural
     * @param  string|\Zend\Locale\Locale $locale   (Optional) Locale/Language to use, identical with
     *                                      locale identifier, @see Zend_Locale for more information
     * @return string
     */
    public function plural($singular, $plural, $number, $locale = null)
    {
        return $this->translate(array($singular, $plural, $number), $locale);
    }

    /**
     * Logs a message when the log option is set
     *
     * @param string $message Message to log
     * @param String $locale  Locale to log
     */
    protected function _log($message, $locale) {
        if ($this->_options['logUntranslated']) {
            $message = str_replace('%message%', $message, $this->_options['logMessage']);
            $message = str_replace('%locale%', $locale, $message);
            if ($this->_options['log']) {
                $this->_options['log']->log($this->_options['logPriority'], $message);
            } else {
                trigger_error($message, E_USER_NOTICE);
            }
        }
    }

    /**
     * Translates the given string
     * returns the translation
     *
     * @param  string             $messageId Translation string
     * @param  string|\Zend\Locale\Locale $locale    (optional) Locale/Language to use, identical with locale
     *                                       identifier, @see Zend_Locale for more information
     * @return string
     */
    public function _($messageId, $locale = null)
    {
        return $this->translate($messageId, $locale);
    }

    /**
     * Checks if a string is translated within the source or not
     * returns boolean
     *
     * @param  string             $messageId Translation string
     * @param  boolean            $original  (optional) Allow translation only for original language
     *                                       when true, a translation for 'en_US' would give false when it can
     *                                       be translated with 'en' only
     * @param  string|\Zend\Locale\Locale $locale    (optional) Locale/Language to use, identical with locale identifier,
     *                                       see Zend_Locale for more information
     * @return boolean
     */
    public function isTranslated($messageId, $original = false, $locale = null)
    {
        if (($original !== false) and ($original !== true)) {
            $locale   = $original;
            $original = false;
        }

        if ($locale === null) {
            $locale = $this->_options['locale'];
        }

        if (!Locale\Locale::isLocale($locale, true)) {
            if (!Locale\Locale::isLocale($locale, false)) {
                // language does not exist, return original string
                return false;
            }

            $locale = new Locale\Locale($locale);
        }

        $locale = (string) $locale;
        if ((is_string($messageId) || is_int($messageId)) && isset($this->_translate[$locale][$messageId])) {
            // return original translation
            return true;
        } else if ((strlen($locale) != 2) and ($original === false)) {
            // faster than creating a new locale and separate the leading part
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));

            if ((is_string($messageId) || is_int($messageId)) && isset($this->_translate[$locale][$messageId])) {
                // return regionless translation (en_US -> en)
                return true;
            }
        }

        // No translation found, return original
        return false;
    }

    /**
     * Returns the set cache
     *
     * @return CacheAdapter The set cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Sets a cache for all Zend_Translator_Adapter's
     *
     * @param CacheAdapter $cache Cache to store to
     */
    public static function setCache(CacheAdapter $cache)
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
                $tag = 'Zend_Translator';
            }

            self::$_cache->clear(CacheAdapter::MATCH_TAGS_OR, array('tags' => array($tag)));
        } else {
            self::$_cache->clear(CacheAdapter::MATCH_ALL);
        }
    }

    /**
     * Saves the given cache
     * Prevents broken cache when write_control is disabled and displays problems by log or error
     *
     * @param  mixed  $data
     * @param  string $id
     * @return boolean Returns false when the cache has not been written
     */
    protected function saveCache($data, $id)
    {
        if (self::$_cacheTags) {
            self::$_cache->setItem($id, $data, array('tags' => array($this->_options['tag'])));
        } else {
            self::$_cache->setItem($id, $data);
        }

        if (!self::$_cache->hasItem($id)) {
            if (!$this->_options['disableNotices']) {
                if ($this->_options['log']) {
                    $this->_options['log']->log($this->_options['logPriority'], "Writing to cache failed.");
                } else {
                    trigger_error("Writing to cache failed.", E_USER_NOTICE);
                }
            }

            self::$_cache->removeItem($id);
            return false;
        }

        return true;
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    abstract public function toString();

    /**
     * Internal method to check if the given cache supports tags
     *
     * @return void
     */
    private static function _getTagSupportForCache()
    {
        if (!self::$_cache instanceof CacheAdapter) {
            self::$_cacheTags = false;
            return false;
        }
        self::$_cacheTags = true;
        return true;
    }
}
