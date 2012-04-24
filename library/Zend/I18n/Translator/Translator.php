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
     * @param  string $domain
     * @param  string $locale
     * @return string 
     */
    public function translate($message, $domain = 'default', $locale = null)
    {
        if ($message === '') {
            return '';
        }
        
        $locale = ($locale ?: $this->getLocale());
        
        if (!isset($this->messages[$domain][$locale])) {
            $this->loadMessages($domain, $locale);
        }
               
        if (!isset($this->messages[$domain][$locale][$message])) {
            return $message;
        }
        
        return $this->messages[$domain][$locale][$message];
    }
    
    /**
     * Translate a plural message.
     * 
     * @param  type $singular
     * @param  type $plural
     * @param  type $number
     * @param  type $domain
     * @param  type $locale 
     * @return string
     */
    public function translatePlural($singular, $plural, $number, $domain = 'default', $locale = null)
    {
        $data  = $this->translate($singular, $domain, $locale);
        $index = $this->messages[$domain][$locale]['']->evaluate($number);
        
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
     * @param  string $domain
     * @param  string $locale
     * @return Translator 
     */
    public function addTranslationFile($type, $filename, $domain = 'default', $locale = null)
    {
        $locale = ($locale ?: '*');
        
        if (!isset($this->files[$domain])) {
            $this->files[$domain] = array();
        } elseif (!isset($this->files[$domain][$locale])) {
            $this->files[$domain][$locale] = array();
        }
        
        $this->files[$domain][$locale][] = array(
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
     * @param  string $domain 
     * @return Translator
     */
    public function addTranslationPattern($type, $baseDir, $pattern, $domain = 'default')
    {
        if (!isset($this->patterns[$domain])) {
            $this->patterns[$domain] = array();
        }
        
        $this->patterns[$domain][] = array(
            'type'    => $type,
            'baseDir' => rtrim($baseDir . '/'),
            'pattern' => $pattern
        );
        
        return $this;
    }
    
    /**
     * Load messages for a given language and domain.
     * 
     * @param  string $domain
     * @param  string $locale
     * @return void
     */
    protected function loadMessages($domain, $locale)
    {
        if (!isset($this->messages[$domain])) {
            $this->messages[$domain] = array();
        } elseif (!isset($this->messages[$domain][$locale])) {
            $this->messages[$domain][$locale] = array();
        }
        
        // Try to load from pattern
        if (isset($this->patterns[$domain])) {
            foreach ($this->patterns[$domain] as $pattern) {
                $filename = $pattern['baseDir'] . '/' . sprintf($pattern['pattern'], $locale);
                
                if (is_file($filename)) {
                    $data = $this->pluginBroker()->load($pattern['type'])->load($filename);
                    
                    $this->messages[$domain][$locale] = array_replace(
                        $this->messages[$domain][$locale],
                        $data
                    );
                }
            }
        }
        
        // Load concrete files
        foreach (array($locale, '*') as $locale) {
            if (!isset($this->files[$domain][$locale])) {
                continue;
            }
                       
            foreach ($this->files[$domain][$locale] as $file) {
                $data = $this->pluginBroker()->load($file['type'])->load($file['filename']);
                
                if ($locale === '*') {
                    foreach ($data as $messageLocale => $messages) {
                        if (!isset($this->messages[$domain][$messageLocale])) {
                            $this->messages[$domain][$messageLocale] = array();
                        }
                        
                        $this->messages[$domain][$messageLocale] = array_replace(
                            $this->messages[$domain][$messageLocale],
                            $messages
                        );
                    }
                } else {
                    $this->messages[$domain][$locale] = array_replace(
                        $this->messages[$domain][$locale],
                        $data
                    );
                }
            }
            
            unset($this->files[$domain][$locale]);
        }
    }
}
