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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Filter;

use Zend\Config\Config,
    Zend\Locale\Locale as ZendLocale,
    Zend\Registry;

/**
 * @uses       Zend\Filter\AbstractFilter
 * @uses       Zend\Locale\Locale
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Alpha extends AbstractFilter
{
    /**
     * Whether to allow white space characters; off by default
     *
     * @var boolean
     */
    protected $allowWhiteSpace;

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     *
     * @var mixed
     **/
    protected static $unicodeEnabled;

    /**
     * Locale to use
     *
     * @var \Zend\Locale\Locale object
     */
    protected $locale;

    /**
     * Sets default option values for this instance
     *
     * @param  boolean $allowWhiteSpace
     * @return void
     */
    public function __construct($options = false)
    {
        if ($options instanceof Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) {
                $temp['allowwhitespace'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['locale'] = array_shift($options);
            }

            $options = $temp;
        }

        if (null === self::$unicodeEnabled) {
            self::$unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        }

        if (array_key_exists('allowwhitespace', $options)) {
            $this->setAllowWhiteSpace($options['allowwhitespace']);
        }

        if (!array_key_exists('locale', $options)) {
            $options['locale'] = null;
        }

        $this->setLocale($options['locale']);
    }

    /**
     * Returns the allowWhiteSpace option
     *
     * @return boolean
     */
    public function getAllowWhiteSpace()
    {
        return $this->allowWhiteSpace;
    }

    /**
     * Sets the allowWhiteSpace option
     *
     * @param boolean $allowWhiteSpace
     * @return \Zend\Filter\Alpha Provides a fluent interface
     */
    public function setAllowWhiteSpace($allowWhiteSpace)
    {
        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the locale option
     *
     * @param boolean $locale
     * @return \Zend\Filter\Alnum Provides a fluent interface
     */
    public function setLocale($locale = null)
    {
        $this->locale = ZendLocale::findLocale($locale);
        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, removing all but alphabetic characters
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $whiteSpace = $this->allowWhiteSpace ? '\s' : '';

        if (!self::$unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $pattern = '/[^a-zA-Z' . $whiteSpace . ']/';
        } elseif (((string) $this->locale == 'ja') 
                  || ((string) $this->locale == 'ko') 
                  || ((string) $this->locale == 'zh')
        ) {
            // The Alphabet means english alphabet.
            $pattern = '/[^a-zA-Z'  . $whiteSpace . ']/u';
        } else {
            // The Alphabet means each language's alphabet.
            $pattern = '/[^\p{L}' . $whiteSpace . ']/u';
        }

        return preg_replace($pattern, '', (string) $value);
    }
}
