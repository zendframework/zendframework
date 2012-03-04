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

use \Locale;

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
     * Default locale.
     * 
     * @var string
     */
    protected $locale;
    
    /**
     * Language part of the default locale.
     * 
     * @var string
     */
    protected $language;
    
    /**
     * Set the default locale.
     * 
     * @param  string $locale
     * @return Translator
     */
    public function setLocale($locale)
    {
        $this->locale   = $locale;
        $tihs->language = null;
        
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
     * Get default language.
     * 
     * @return string
     */
    public function getLanguage()
    {
        if ($this->language === null) {
            $this->language = Locale::getPrimaryLanguage($this->getLocale());
        }
        
        return $this->language;
    }
    
    /**
     * Translate a message.
     * 
     * @param  string $messageId
     * @param  string $domain
     * @param  string $locale
     * @return string 
     */
    public function translate($messageId, $domain = 'default', $locale = null)
    {
        if ($locale === null) {
            $language = $this->getLanguage();
        } else {
            $language = Locale::getPrimaryLanguage($locale);
        }
        
        if (!isset($this->messages[$language][$domain])) {
            $this->loadMessages($language, $domain);
        }
        
        if (!isset($this->messages[$language][$domain])) {
            return $messageId;
        }
        
        return $this->messages[$language][$domain];
    }
    
    /**
     * Add a translation file.
     * 
     * @param  string $type
     * @param  string $filename
     * @param  string $locale
     * @param  string $domain
     * @return Translator 
     */
    public function addTranslationFile($type, $filename, $locale, $domain = 'default')
    {
        $language = Locale::getPrimaryLanguage($locale);
        
        if (!isset($this->files[$language])) {
            $this->files[$language] = array();
        }
        
        $this->files[$language][$domain] = array(
            'type'     => $type,
            'filename' => $filename
        );

        return $this;
    }
    
    /**
     * Load messages for a given language and domain.
     * 
     * @param  string $language
     * @param  string $domain
     * @return void
     */
    protected function loadMessages($language, $domain)
    {
        if (!isset($this->files[$language][$domain])) {
            return;
        }
        
        
    }
}
