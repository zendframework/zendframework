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
 * @package    Zend_Captcha
 */

namespace Zend\Captcha;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Captcha
 */
abstract class Factory
{
    /**
     * @var array Known captcha types
     */
    protected static $classMap = array(
        'dumb'      => 'Zend\Captcha\Dumb',
        'figlet'    => 'Zend\Captcha\Figlet',
        'image'     => 'Zend\Captcha\Image',
        'recaptcha' => 'Zend\Captcha\ReCaptcha',
    );

    /**
     * Create a captcha adapter instance
     * 
     * @param  array|Traversable $options 
     * @return AdapterInterface
     * @throws Exception\InvalidArgumentException for a non-array, non-Traversable $options
     * @throws Exception\DomainException if class is missing or invalid
     */
    public static function factory($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        if (!isset($options['class'])) {
            throw new Exception\DomainException(sprintf(
                '%s expects a "class" attribute in the options; none provided',
                __METHOD__
            ));
        }

        $class = $options['class'];
        if (isset(static::$classMap[strtolower($class)])) {
            $class = static::$classMap[strtolower($class)];
        }
        if (!class_exists($class)) {
            throw new Exception\DomainException(sprintf(
                '%s expects the "class" attribute to resolve to an existing class; received "%s"',
                __METHOD__,
                $class
            ));
        }
        
        unset($options['class']);

        if (isset($options['options'])) {
            $options = $options['options'];
        }
        $captcha = new $class($options);

        if (!$captcha instanceof AdapterInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects the "class" attribute to resolve to a valid Zend\Captcha\AdapterInterface instance; received "%s"',
                __METHOD__,
                $class
            ));
        }

        return $captcha;
    }
}

