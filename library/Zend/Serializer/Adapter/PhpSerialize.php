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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception\RuntimeException;

/**
 * @uses       Zend\Serializer\Adapter\AbstractAdapter
 * @uses       Zend\Serializer\Exception\RuntimeException
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpSerialize extends AbstractAdapter
{
    /**
     * @var null|string Serialized boolean false value
     */
    private static $_serializedFalse = null;

    /**
     * Constructor
     * 
     * @param  array|Zend\Config\Config $opts 
     * @return void
     */
    public function __construct($opts = array()) 
    {
        parent::__construct($opts);

        // needed to check if a returned false is based on a serialize false
        // or based on failure (igbinary can overwrite [un]serialize functions)
        if (self::$_serializedFalse === null) {
            self::$_serializedFalse = serialize(false);
        }
    }

    /**
     * Serialize using serialize()
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws Zend\Serializer\Exception On serialize error
     */
    public function serialize($value, array $opts = array())
    {
        $ret = serialize($value);
        if ($ret === false) {
            $lastErr = error_get_last();
            throw new RuntimeException($lastErr['message']);
        }
        return $ret;
    }

    /**
     * Unserialize
     * 
     * @todo   Allow integration with unserialize_callback_func
     * @param  string $serialized 
     * @param  array $opts 
     * @return mixed
     * @throws Zend\Serializer\Exception on unserialize error
     */
    public function unserialize($serialized, array $opts = array())
    {
        $ret = @unserialize($serialized);
        if ($ret === false && $serialized !== self::$_serializedFalse) {
            $lastErr = error_get_last();
            throw new RuntimeException($lastErr['message']);
        }
        return $ret;
    }
}
