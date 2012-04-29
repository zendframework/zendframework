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
 * @package    Zend_I18n_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\Translator;

use Locale;
use Zend\Loader\Broker;
use Zend\Loader\PluginBroker;
use Zend\Cache\Storage\Adapter as CacheAdapter;

/**
 * Translator.
 *
 * @package    Zend_I18n_Translator
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
     * Translation cache.
     *
     * @var CacheAdapter
     */
    protected $cache;

    /**
     * Plugin broker.
     *
     * @var Broker
     */
    protected $pluginBroker;

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
     * Get default locale.
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
     * Returns the set cache
     *
     * @return CacheAdapter The set cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Sets a cache
     *
     * @param  CacheAdapter $cache
     * @return Translator
     */
    public function setCache(CacheAdapter $cache = null)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Retreive or set the plugin broker.
     *
     * @param  Broker $broker
     * @return Broker
     */
    public function pluginBroker(Broker $broker = null)
    {
        if ($broker !== null) {
            $this->pluginBroker = $broker;
        } elseif ($this->pluginBroker === null) {
            $this->pluginBroker = new PluginBroker();
            $this->pluginBroker->getClassLoader()->registerPlugins(array(
                'phpArray' => __NAMESPACE__ . '\Loader\PhpArray',
                'gettext'  => __NAMESPACE__ . '\Loader\Gettext',
                // And so on …
            ));
        }

        return $this->pluginBroker;
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
        if ($message === '') {
            return '';
        }

        $locale = ($locale ?: $this->getLocale());

        if (!isset($this->messages[$textDomain][$locale])) {
            $this->loadMessages($textDomain, $locale);
        }

        if (!isset($this->messages[$textDomain][$locale][$message])) {
            return $message;
        }

        return $this->messages[$textDomain][$locale][$message];
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
    public function translatePlural($singular, $plural, $number,
        $textDomain = 'default', $locale = null
    ) {
        $data  = $this->translate($singular, $textDomain, $locale);
        $index = $this->messages[$textDomain][$locale]['']->evaluate($number);

        if (!isset($data[$index])) {
            // Exception!!!
        }

        return $data[$index];
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
    public function addTranslationFile($type, $filename,
        $textDomain = 'default', $locale = null
    ) {
        $locale = ($locale ?: '*');

        if (!isset($this->files[$textDomain])) {
            $this->files[$textDomain] = array();
        }

        $this->files[$textDomain][$locale] = array(
            'type'     => $type,
            'filename' => $filename
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
    public function addTranslationPattern($type, $baseDir, $pattern,
        $textDomain = 'default'
    ) {
        if (!isset($this->patterns[$textDomain])) {
            $this->patterns[$textDomain] = array();
        }

        $this->patterns[$textDomain][] = array(
            'type'    => $type,
            'baseDir' => rtrim($baseDir . '/'),
            'pattern' => $pattern
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
                    $this->messages[$textDomain][$locale] = $this->pluginBroker()
                        ->load($pattern['type'])->load($filename, $locale);
                }
            }
        }

        // Load concrete files, may override those loaded from patterns
        foreach (array($locale, '*') as $locale) {
            if (!isset($this->files[$textDomain][$locale])) {
                continue;
            }

            foreach ($this->files[$textDomain][$locale] as $file) {
                $this->messages[$textDomain][$locale] = $this->pluginBroker()
                    ->load($pattern['type'])->load($file['filename'], $locale);
            }

            unset($this->files[$textDomain][$locale]);
        }

        // Cache the loaded text domain
        if ($cache !== null) {
            $cache->setItem($cacheId, $this->messages[$textDomain][$locale]);
        }
    }
}
