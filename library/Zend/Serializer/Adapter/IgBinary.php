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

use Zend\Serializer\Exception;

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
    private static $serializedNull = null;

    /**
     * Constructor
     *
     * @throws Exception\ExtensionNotLoadedException If igbinary extension is not present
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('igbinary')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "igbinary" is required for this adapter'
            );
        }

        if (self::$serializedNull === null) {
            self::$serializedNull = igbinary_serialize(null);
        }

        parent::__construct($options);
    }

    /**
     * Serialize PHP value to igbinary
     *
     * @param  mixed $value
     * @return string
     * @throws Exception\RuntimeException on igbinary error
     */
    public function serialize($value)
    {
        $ret = igbinary_serialize($value);
        if ($ret === false) {
            $lastErr = error_get_last();
            throw new Exception\RuntimeException('Serialization failed: ' . $lastErr['message']);
        }
        return $ret;
    }

    /**
     * Deserialize igbinary string to PHP value
     *
     * @param  string $serialized
     * @return mixed
     * @throws Exception\RuntimeException on igbinary error
     */
    public function unserialize($serialized)
    {
        if ($serialized === self::$serializedNull) {
            return null;
        }

        $ret = igbinary_unserialize($serialized);

        if ($ret === null) {
            $lastErr = error_get_last();
            $message = $lastErr ? $lastErr['message'] : 'syntax error';
            throw new Exception\RuntimeException('Unserialization failed: ' . $message);
        }

        return $ret;
    }
}