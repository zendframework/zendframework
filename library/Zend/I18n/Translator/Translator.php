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
 * @package    Zend_I18n
 * @subpackage Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\Translator;

use Locale;
use Zend\Cache\Storage\StorageInterface as CacheStorage;

/**
 * Translator.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Translator
{
    /**
     * Messages loaded by the translator.
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Files used for loading messages.
     *
     * @var array
     */
    protected $files = array();

    /**
     * Patterns used for loading messages.
     *
     * @var array
     */
    protected $patterns = array();

    /**
     * Default locale.
     *
     * @var string
     */
    protected $locale;

    /**
     * Locale to use as fallback if there is no translation.
     *
     * @var string
     */
    protected $fallbackLocale;

    /**
     * Translation cache.
     *
     * @var CacheStorage
     */
    protected $cache;

    /**
     * Plugin manager for translation loaders.
     *
     * @var LoaderPluginManager
     */
    protected $pluginManager;

    /**
     * Set the default locale.
     *
     * @param  string $locale
     * @return Translator
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get the default locale.
     *
     * @return string
     */
    public function getLocale()
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Set the fallback locale.
     *
     * @param  string $locale
     * @return Translator
     */
    public function setFallbackLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get the fallback locale.
     *
     * @return string
     */
    public function getFallbackLocale()
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Sets a cache
     *
     * @param  CacheStorage $cache
     * @return Translator
     */
    public function setCache(CacheStorage $cache = null)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Returns the set cache
     *
     * @return CacheStorage The set cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the plugin manager for translation loaders
     * 
     * @param  LoaderPluginManager $pluginManager 
     * @return Translator
     */
    public function setPluginManager(LoaderPluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
        return $this;
    }

    /**
     * Retreive the plugin manager for tranlation loaders.
     *
     * Lazy loads an instance if none currently set.
     *
     * @return LoaderPluginManager
     */
    public function getPluginManager()
    {
        if (!$this->pluginManager instanceof LoaderPluginManager) {
            $this->setPluginManager(new LoaderPluginManager());
        }

        return $this->pluginManager;
    }

    /**
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function translate($message, $textDomain = 'default', $locale = null)
    {
        $locale      = ($locale ?: $this->getLocale());
        $translation = $this->getTranslatedMessage($message, $locale, $textDomain);

        if ($translation !== null && $translation !== '') {
            return $translation;
        } 
        
        if (null !== ($fallbackLocale = $this->getFallbackLocale()) 
            && $locale !== $fallbackLocale
        ) {
            return $this->translate($message, $textDomain, $fallbackLocale);
        }

        return $message;
    }

    /**
     * Translate a plural message.
     *
     * @param  type $singular
     * @param  type $plural
     * @param  type $number
     * @param  type $textDomain
     * @param  type $locale
     * @return string
     */
    public function translatePlural(
        $singular,
        $plural,
        $number,
        $textDomain = 'default',
        $locale = null
    ) {
        $locale      = $locale ?: $this->getLocale();
        $translation = $this->getTranslatedMessage($message, $locale, $textDomain);

        if ($translation === null || $translation === '') {
            if (null !== ($fallbackLocale = $this->getFallbackLocale()) 
                && $locale !== $fallbackLocale
            ) {
                return $this->translatePlural(
                    $singular, 
                    $plural, 
                    $number, 
                    $textDomain, 
                    $fallbackLocale
                );
            }

            return ($number != 1 ? $singular : $plural);
        }

        $index = $this->messages[$textDomain][$locale]
                      ->pluralRule()
                      ->evaluate($number);

        if (!isset($translation[$index])) {
            throw new Exception\OutOfBoundsException(sprintf(
                'Provided index %d does not exist in plural array', $index
            ));
        }

        return $translation[$index];
    }

    /**
     * Get a translated message.
     *
     * @param  string $message
     * @param  string $locale
     * @param  string $textDomain
     * @return string|null
     */
    protected function getTranslatedMessage(
        $message,
        $locale = null,
        $textDomain = 'default'
    ) {
        if ($message === '') {
            return '';
        }

        if (!isset($this->messages[$textDomain][$locale])) {
            $this->loadMessages($textDomain, $locale);
        }

        if (!array_key_exists($message, $this->messages[$textDomain][$locale])) {
            return null;
        }

        return $this->messages[$textDomain][$locale][$message];
    }

    /**
     * Add a translation file.
     *
     * @param  string $type
     * @param  string $filename
     * @param  string $textDomain
     * @param  string $locale
     * @return Translator
     */
    public function addTranslationFile(
        $type,
        $filename,
        $textDomain = 'default',
        $locale = null
    ) {
        $locale = $locale ?: '*';

        if (!isset($this->files[$textDomain])) {
            $this->files[$textDomain] = array();
        }

        $this->files[$textDomain][$locale] = array(
            'type'     => $type,
            'filename' => $filename,
        );

        return $this;
    }

    /**
     * Add multiple translations with a pattern.
     *
     * @param  string $type
     * @param  string $baseDir
     * @param  string $pattern
     * @param  string $textDomain
     * @return Translator
     */
    public function addTranslationPattern(
        $type,
        $baseDir,
        $pattern,
        $textDomain = 'default'
    ) {
        if (!isset($this->patterns[$textDomain])) {
            $this->patterns[$textDomain] = array();
        }

        $this->patterns[$textDomain][] = array(
            'type'    => $type,
            'baseDir' => rtrim($baseDir . '/'),
            'pattern' => $pattern,
        );

        return $this;
    }

    /**
     * Load messages for a given language and domain.
     *
     * @param  string $textDomain
     * @param  string $locale
     * @return void
     */
    protected function loadMessages($textDomain, $locale)
    {
        if (!isset($this->messages[$textDomain])) {
            $this->messages[$textDomain] = array();
        }

        if (null !== ($cache = $this->getCache())) {
            $cacheId = 'Zend_I18n_Translator_Messages_' . md5($textDomain . $locale);

            if (false !== ($result = $cache->getItem($cacheId))) {
                $this->messages[$textDomain][$locale] = $result;
                return;
            }
        }

        // Try to load from pattern
        if (isset($this->patterns[$textDomain])) {
            foreach ($this->patterns[$textDomain] as $pattern) {
                $filename = $pattern['baseDir'] . '/'
                          . sprintf($pattern['pattern'], $locale);

                if (is_file($filename)) {
                    $this->messages[$textDomain][$locale] = $this->getPluginManager()
                         ->get($pattern['type'])
                         ->load($filename, $locale);
                }
            }
        }

        // Load concrete files, may override those loaded from patterns
        foreach (array($locale, '*') as $currentLocale) {
            if (!isset($this->files[$textDomain][$currentLocale])) {
                continue;
            }

            $file = $this->files[$textDomain][$currentLocale];
            $this->messages[$textDomain][$locale] = $this->getPluginManager()
                 ->get($file['type'])
                 ->load($file['filename'], $locale);

            unset($this->files[$textDomain][$currentLocale]);
        }

        // Cache the loaded text domain
        if ($cache !== null) {
            $cache->setItem($cacheId, $this->messages[$textDomain][$locale]);
        }
    }
}
