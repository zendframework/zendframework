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

namespace Zend\Filter;

use Locale;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Alnum extends AbstractLocale
{
    /**
     * @var array
     */
    protected $options = array(
        'locale'            => null,
        'allow_white_space' => false,
    );

    /**
     * Sets default option values for this instance
     *
     * @param  array|Traversable|boolean|null $options
     */
    public function __construct($options = null)
    {
        if ($options !== null) {
            if ($options instanceof Traversable) {
                $options = iterator_to_array($options);
            }

            if (!is_array($options)) {
                $args = func_get_args();
                if (isset($args[0])) {
                    $this->setAllowWhiteSpace($args[0]);
                }
                if (isset($args[1])) {
                    $this->setLocale($args[1]);
                }
            } else {
                $this->setOptions($options);
            }
        }
    }

    /**
     * Sets the allowWhiteSpace option
     *
     * @param  boolean $flag
     * @return Alnum Provides a fluent interface
     */
    public function setAllowWhiteSpace($flag = true)
    {
        $this->options['allow_white_space'] = (boolean) $flag;
        return $this;
    }

    /**
     * Whether white space is allowed
     *
     * @return boolean
     */
    public function getAllowWhiteSpace()
    {
        return $this->options['allow_white_space'];
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns $value as string with all non-alphanumeric characters removed
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        $whiteSpace = $this->options['allow_white_space'] ? '\s' : '';
        $language   = Locale::getPrimaryLanguage($this->getLocale());

        if (!static::hasPcreUnicodeSupport()) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9' . $whiteSpace . ']/';
        } elseif ($language == 'ja'|| $language == 'ko' || $language == 'zh') {
            // Use english alphabet
            $pattern = '/[^a-zA-Z0-9'  . $whiteSpace . ']/u';
        } else {
            // Use native language alphabet
            $pattern = '/[^\p{L}\p{N}' . $whiteSpace . ']/u';
        }

        return preg_replace($pattern, '', (string) $value);
    }
}
