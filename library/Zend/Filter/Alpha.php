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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Filter;

use Traversable,
    Zend\Locale\Locale as ZendLocale,
    Zend\Registry,
    Zend\Stdlib\IteratorToArray;

/**
 * @uses       Zend\Filter\AbstractFilter
 * @uses       Zend\Locale\Locale
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
        if ($options instanceof Traversable) {
            $options = IteratorToArray::convert($options);
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) {
                $temp['allowWhiteSpace'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['locale'] = array_shift($options);
            }

            $options = $temp;
        }

        if (array_key_exists('unicodeEnabled', $options)) {
            $this->setUnicodeEnabled($options['unicodeEnabled']);
        }

        if (array_key_exists('allowWhiteSpace', $options)) {
            $this->setAllowWhiteSpace($options['allowWhiteSpace']);
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
     * Toggle unicode matching capabilities
     * 
     * @param  bool $flag 
     * @return Alpha
     */
    public function setUnicodeEnabled($flag)
    {
        $flag = (bool) $flag;
        if (!$flag) {
            static::$unicodeEnabled = $flag;
            return;
        }

        if (!static::isUnicodeCapable()) {
            throw new Exception\RuntimeException(sprintf(
                '%s cannot be unicode enabled; installed PCRE is not capable',
                __CLASS__
            ));
        }

        static::$unicodeEnabled = $flag;
        return $this;
    }

    /**
     * Is this instance unicode enabled?
     * 
     * @return bool
     */
    public function isUnicodeEnabled()
    {
        if (null === static::$unicodeEnabled) {
            static::$unicodeEnabled = static::isUnicodeCapable();
        }
        return static::$unicodeEnabled;
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

        $locale = (string) $this->locale;
        if (!$this->isUnicodeEnabled()) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $pattern = '/[^a-zA-Z' . $whiteSpace . ']/';
        } elseif (($locale == 'ja')
                  || ($locale == 'ko')
                  || ($locale == 'zh')
        ) {
            // Use english alphabet
            $pattern = '/[^a-zA-Z'  . $whiteSpace . ']/u';
        } else {
            // Use native language alphabet
            $pattern = '/[^\p{L}' . $whiteSpace . ']/u';
        }

        return preg_replace($pattern, '', (string) $value);
    }

    /**
     * Are we unicode capable?
     * 
     * @return bool
     */
    protected static function isUnicodeCapable()
    {
        return (@preg_match('/\pL/u', 'a') ? true : false);
    }
}
