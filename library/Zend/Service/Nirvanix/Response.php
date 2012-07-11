<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Nirvanix;

use SimpleXMLElement;

/**
 * This class decorates a SimpleXMLElement parsed from a Nirvanix web service
 * response.  It is primarily exists to provide a convenience feature that
 * throws an exception when <ResponseCode> contains an error.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Nirvanix
 */
class Response
{
    /**
     * SimpleXMLElement parsed from Nirvanix web service response.
     *
     * @var SimpleXMLElement
     */
    protected $sxml;

    /**
     * Class constructor.  Parse the XML response from a Nirvanix method
     * call into a decorated SimpleXMLElement element.
     *
     * @param string $xml  XML response string from Nirvanix
     * @throws Exception\RuntimeException when unable to parse XML
     * @throws Exception\DomainException when receiving invalid response element or xml contains error message
     */
    public function __construct($xml)
    {
        $this->sxml = @simplexml_load_string($xml);

        if (! $this->sxml instanceof SimpleXMLElement) {
            throw new Exception\RuntimeException("XML could not be parsed from response: $xml");
        }

        $name = $this->sxml->getName();
        if ($name != 'Response') {
            throw new Exception\DomainException("Expected XML element Response, got $name");
        }

        $code = (int) $this->sxml->ResponseCode;
        if ($code != 0) {
            $msg = (string) $this->sxml->ErrorMessage;
            throw new Exception\DomainException($msg, $code);
        }
    }

    /**
     * Return the SimpleXMLElement representing this response
     * for direct access.
     *
     * @return SimpleXMLElement
     */
    public function getSxml()
    {
        return $this->sxml;
    }

    /**
     * Delegate undefined properties to the decorated SimpleXMLElement.
     *
     * @param  string  $offset  Undefined property name
     * @return mixed
     */
    public function __get($offset)
    {
        return $this->sxml->$offset;
    }

    /**
     * Delegate undefined methods to the decorated SimpleXMLElement.
     *
     * @param  string  $offset  Underfined method name
     * @param  array   $args    Method arguments
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->sxml, $method), $args);
    }
}
