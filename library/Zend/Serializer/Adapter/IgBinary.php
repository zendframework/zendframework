<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception\ExtensionNotLoadedException;
use Zend\Serializer\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
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
