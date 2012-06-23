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

use Zend\Serializer\Exception\RuntimeException,
    Zend\Serializer\Exception\ExtensionNotLoadedException;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IgBinary extends AbstractAdapter
{
    /**
     * @var string Serialized null value
     */
    private static $_serializedNull = null;

    /**
     * Constructor
     * 
     * @param  array|\Traversable $options
     * @return void
     * @throws ExtensionNotLoadedException If igbinary extension is not present
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('igbinary')) {
            throw new ExtensionNotLoadedException('PHP extension "igbinary" is required for this adapter');
        }

        parent::__construct($options);

        if (self::$_serializedNull === null) {
            self::$_serializedNull = igbinary_serialize(null);
        }
    }

    /**
     * Serialize PHP value to igbinary
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws RuntimeException on igbinary error
     */
    public function serialize($value, array $opts = array())
    {
        $ret = igbinary_serialize($value);
        if ($ret === false) {
            $lastErr = error_get_last();
            throw new RuntimeException($lastErr['message']);
        }
        return $ret;
    }

    /**
     * Deserialize igbinary string to PHP value
     * 
     * @param  string|binary $serialized 
     * @param  array $opts 
     * @return mixed
     * @throws RuntimeException on igbinary error
     */
    public function unserialize($serialized, array $opts = array())
    {
        $ret = igbinary_unserialize($serialized);
        if ($ret === null && $serialized !== self::$_serializedNull) {
            $lastErr = error_get_last();
            throw new RuntimeException($lastErr['message']);
        }
        return $ret;
    }
}
