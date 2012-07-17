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
 * @link       http://www.infoloom.com/gcaconfs/WEB/chicago98/simeonov.HTM
 * @link       http://en.wikipedia.org/wiki/WDDX
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
class Wddx implements AdapterInterface
{
    /**
     * Comment
     *
     * @var string
     */
    protected $comment = null;

    /**
     * Constructor
     *
     * @param  string $comment
     * @throws Exception\ExtensionNotLoadedException if wddx extension not found
     */
    public function __construct($comment = null)
    {
        if (!extension_loaded('wddx')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "wddx" is required for this adapter'
            );
        }

        if ($comment !== null) {
            $this->comment = (string) $comment;
        }
    }

    /**
     * Set WDDX header comment
     *
     * @param  string $comment
     * @return Wddx
     */
    public function setComment($comment)
    {
        $this->comment = (string) $comment;
        return $this;
    }

    /**
     * Get WDDX header comment
     *
     * @return null|string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Serialize PHP to WDDX
     *
     * @param  mixed $value
     * @return string
     * @throws Exception\RuntimeException on wddx error
     */
    public function serialize($value)
    {
        if ($this->comment) {
            $wddx = wddx_serialize_value($value, $this->comment);
        } else {
            $wddx = wddx_serialize_value($value);
        }

        if ($wddx === false) {
            $lastErr = error_get_last();
            throw new Exception\RuntimeException('Serialization failed:' . $lastErr['message']);
        }
        return $wddx;
    }

    /**
     * Unserialize from WDDX to PHP
     *
     * @param  string $wddx
     * @return mixed
     * @throws Exception\RuntimeException on wddx error
     */
    public function unserialize($wddx)
    {
        $ret = wddx_deserialize($wddx);

        if ($ret === null && class_exists('SimpleXMLElement', false)) {
            // check if the returned NULL is valid
            // or based on an invalid wddx string
            try {
                $simpleXml = new \SimpleXMLElement($wddx);
                if (isset($simpleXml->data[0]->null[0])) {
                    return null; // valid null
                }
                throw new Exception\RuntimeException('Unserialization failed: Invalid wddx packet');
            } catch (\Exception $e) {
                throw new Exception\RuntimeException('Unserialization failed: ' . $e->getMessage(), 0, $e);
            }
        }

        return $ret;
    }
}