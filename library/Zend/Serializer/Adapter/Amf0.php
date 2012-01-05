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

/**
 * @namespace
 */
namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception\RuntimeException,
    Zend\Amf\Parser as AmfParser;

/**
 * @uses       Zend\Amf\Parser\Amf0\Deserializer
 * @uses       Zend\Amf\Parser\Amf0\Serializer
 * @uses       Zend\Amf\Parser\InputStream
 * @uses       Zend\Amf\Parser\OutputStream
 * @uses       Zend\Serializer\Adapter\AbstractAdapter
 * @uses       Zend\Serializer\Exception\RuntimeException
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Amf0 extends AbstractAdapter
{
    /**
     * Serialize a PHP value to AMF0 format
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws Zend\Serializer\Exception
     */
    public function serialize($value, array $opts = array())
    {
        try  {
            $stream     = new AmfParser\OutputStream();
            $serializer = new AmfParser\Amf0\Serializer($stream);
            $serializer->writeTypeMarker($value);
            return $stream->getStream();
        } catch (\Exception $e) {
            throw new RuntimeException('Serialization failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Unserialize an AMF0 value to PHP
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return void
     * @throws Zend\Serializer\Exception
     */
    public function unserialize($value, array $opts = array())
    {
        try {
            $stream       = new AmfParser\InputStream($value);
            $deserializer = new AmfParser\Amf0\Deserializer($stream);
            return $deserializer->readTypeMarker();
        } catch (\Exception $e) {
            throw new RuntimeException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
