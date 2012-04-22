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

namespace Zend\I18n\Translator\Loader;

/**
 * Loader interface.
 *
 * @package    Zend_I18n_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Loader
{
    /**
     * Load translations from a file.
     * 
     * If the file format contains translations for a single locale, an array
     * with messageId => translation is returned. In case the file format
     * supplies multiple locales at once, an array with locale => messages is
     * returned, where messages is an array with messageId => translation.
     * 
     * In case a translation is a plural translation, the translation itself
     * should be an indexed array (a list) of all plural forms. By default, a
     * germanic plural form is assumed (n != 1). When the locale uses an
     * alternative plural form, you must supply a corresponding header entry
     * with the name "plural_forms" and a value like for instance:
     * 
     * "nplurals=3; plural=n==1 ? 0 : n==2 ? 1 : 2"
     * 
     * Headers are defined in each message array in an element with an empty
     * key containing an array with header => content elements.
     * 
     * @param  string $filename
     * @return array
     */
    public function load($filename);
}
