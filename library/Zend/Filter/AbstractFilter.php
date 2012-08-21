<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\ErrorHandler;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * Filter options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     *
     * @var boolean
     **/
    protected static $hasPcreUnicodeSupport = null;

    /**
     * @return bool
     */
    public static function hasPcreUnicodeSupport()
    {
        if (static::$hasPcreUnicodeSupport === null) {
            static::$hasPcreUnicodeSupport = false;
            ErrorHandler::start();
            if (defined('PREG_BAD_UTF8_OFFSET_ERROR') || preg_match('/\pL/u', 'a') == 1) {
                static::$hasPcreUnicodeSupport = true;
            }
            ErrorHandler::stop();
        }
        return static::$hasPcreUnicodeSupport;
    }

    /**
     * @param  array|Traversable $options
     * @return AbstractFilter
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" expects an array or Traversable; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            } elseif (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                throw new Exception\InvalidArgumentException(sprintf(
                    'The option "%s" does not have a matching %s setter method or options[%s] array key',
                    $key, $setter, $key
                ));
            }
        }
        return $this;
    }

    /**
     * Retrieve options representing object state
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Invoke filter as a command
     *
     * Proxies to {@link filter()}
     *
     * @param  mixed $value
     * @throws Exception\ExceptionInterface If filtering $value is impossible
     * @return mixed
     */
    public function __invoke($value)
    {
        return $this->filter($value);
    }

    /**
     *
     * @param  mixed $options
     * @return bool
     */
    protected static function isOptions($options)
    {
        return (is_array($options) || $options instanceof Traversable);
    }
}
