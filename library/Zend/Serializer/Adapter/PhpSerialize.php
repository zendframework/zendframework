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
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpSerialize extends AbstractAdapter
{
    /**
     * @var null|string Serialized boolean false value
     */
    private static $serializedFalse = null;

    /**
     * Constructor
     * 
     * @param  array|\Traversable $options
     * @return void
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        // needed to check if a returned false is based on a serialize false
        // or based on failure (igbinary can overwrite [un]serialize functions)
        if (self::$serializedFalse === null) {
            self::$serializedFalse = serialize(false);
        }
    }

    /**
     * Serialize using serialize()
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws RuntimeException On serialize error
     */
    public function serialize($value, array $opts = array())
    {
        set_error_handler(function($errno, $errstr = '', $errfile = '', $errline = '') {
            $message = sprintf(
                'Error with serialize operation in %s:%d: %s',
                $errfile,
                $errline,
                $errstr
            );
            throw new RuntimeException($message, $errno);
        });
        $ret = serialize($value);
        restore_error_handler();
        return $ret;
    }

    /**
     * Unserialize
     * 
     * @todo   Allow integration with unserialize_callback_func
     * @param  string $serialized 
     * @param  array $opts 
     * @return mixed
     * @throws RuntimeException on unserialize error
     */
    public function unserialize($serialized, array $opts = array())
    {
        if (!is_string($serialized)) {
            // Must already be unserialized!
            return $serialized;
            throw new RuntimeException(sprintf(
                '%s expects a serialized string argument; received "%s"',
                __METHOD__,
                (is_object($serialized) ? get_class($serialized) : gettype($serialized))
            ));
        }
        if (!preg_match('/^((s|i|d|b|a|O|C):|N;)/', $serialized)) {
            return $serialized;
        }

        // If we have a serialized boolean false value, just return false; 
        // prevents the unserialize handler from creating an error.
        if ($serialized === self::$serializedFalse) {
            return false;
        }

        set_error_handler(function($errno, $errstr = '', $errfile = '', $errline = '') use ($serialized) {
            $message = sprintf(
                'Error with unserialize operation in %s:%d: %s; (string: "%s")',
                $errfile,
                $errline,
                $errstr,
                $serialized
            );
            throw new RuntimeException($message, $errno);
        }, E_NOTICE);
        $ret = unserialize($serialized);
        restore_error_handler();
        return $ret;
    }
}
