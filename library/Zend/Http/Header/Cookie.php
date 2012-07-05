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
 * @package    Zend_Http
 * @subpackage Cookie
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Http\Header;

use Zend\Uri,
    ArrayObject;

/**
 * @see http://www.ietf.org/rfc/rfc2109.txt
 * @see http://www.w3.org/Protocols/rfc2109/rfc2109
 */
class Cookie extends ArrayObject implements HeaderInterface
{

    protected $encodeValue = true;

    public static function fromSetCookieArray(array $setCookies)
    {
        $nvPairs = array();
        /* @var $setCookie SetCookie */
        foreach ($setCookies as $setCookie) {
            if (!$setCookie instanceof SetCookie) {
                throw new Exception\InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . ' requires an array of SetCookie objects');
            }
            if (array_key_exists($setCookie->getName(), $nvPairs)) {
                throw new Exception\InvalidArgumentException('Two cookies with the same name were provided to ' . __CLASS__ . '::' . __METHOD__);
            }

            $nvPairs[$setCookie->getName()] = $setCookie->getValue();
        }
        return new static($nvPairs);
    }

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'cookie') {
            throw new Exception\InvalidArgumentException('Invalid header line for Server string: "' . $name . '"');
        }

        $nvPairs = preg_split('#;\s*#', $value);

        $arrayInfo = array();
        foreach ($nvPairs as $nvPair) {
            $parts = explode('=', $nvPair, 2);
            if (count($parts) != 2) {
                throw new Exception\RuntimeException('Malformed Cookie header found');
            }
            list($name, $value) = $parts;
            $arrayInfo[$name] = urldecode($value);
        }

        $header->exchangeArray($arrayInfo);
        
        return $header;
    }

    public function __construct(array $array = array())
    {
        parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
    }

    public function setEncodeValue($encodeValue)
    {
        $this->encodeValue = (bool) $encodeValue;
        return $this;
    }

    public function getEncodeValue()
    {
        return $this->encodeValue;
    }

    public function getFieldName()
    {
        return 'Cookie';
    }

    public function getFieldValue()
    {
        $nvPairs = array();

        foreach ($this as $name => $value) {
            $nvPairs[] = $name . '=' . (($this->encodeValue) ? urlencode($value) : $value);
        }

        return implode('; ', $nvPairs);
    }
    
    public function toString()
    {
        return 'Cookie: ' . $this->getFieldValue();
    }

    /**
     * Get the cookie as a string, suitable for sending as a "Cookie" header in an
     * HTTP request
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }


}
