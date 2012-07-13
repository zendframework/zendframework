<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\XML;

/**
 * Represents a SecurityTokenReference XML block
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */
class SecurityTokenReference extends AbstractElement
{
    /**
     * Base64 Binary Encoding URI
     */
    const ENCODING_BASE64BIN = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

    /**
     * Return an instance of the object based on the input XML
     *
     * @param string $xmlData The SecurityTokenReference XML Block
     * @return SecurityTokenReference
     * @throws Exception\InvalidArgumentException
     */
    public static function getInstance($xmlData)
    {
        if($xmlData instanceof AbstractElement) {
            $strXmlData = $xmlData->asXML();
        } elseif (is_string($xmlData)) {
            $strXmlData = $xmlData;
        } else {
            throw new Exception\InvalidArgumentException("Invalid Data provided to create instance");
        }

        $sxe = simplexml_load_string($strXmlData);

        if($sxe->getName() != "SecurityTokenReference") {
            throw new Exception\InvalidArgumentException("Invalid XML Block provided for SecurityTokenReference");
        }

        return simplexml_load_string($strXmlData, 'Zend\InfoCard\XML\SecurityTokenReference');
    }

    /**
     * Return the Key Identifier XML Object
     *
     * @return \Zend\InfoCard\XML\AbstractElement
     * @throws Exception\RuntimeException
     */
    protected function _getKeyIdentifier()
    {
        $this->registerXPathNamespace('o', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
        list($keyident) = $this->xpath('//o:KeyIdentifier');

        if(!($keyident instanceof AbstractElement)) {
            throw new Exception\RuntimeException("Failed to retrieve Key Identifier");
        }

        return $keyident;
    }

    /**
     * Return the Key URI identifying the thumbprint type used
     *
     * @return string The thumbprint type URI
     * @throws  Exception\RuntimeException
     */
    public function getKeyThumbprintType()
    {

        $keyident = $this->_getKeyIdentifier();

        $dom = self::convertToDOM($keyident);

        if(!$dom->hasAttribute('ValueType')) {
            throw new Exception\RuntimeException("Key Identifier did not provide a type for the value");
        }

        return $dom->getAttribute('ValueType');
    }


    /**
     * Return the thumbprint encoding type used as a URI
     *
     * @return string the URI of the thumbprint encoding used
     * @throws Exception\RuntimeException
     */
    public function getKeyThumbprintEncodingType()
    {

        $keyident = $this->_getKeyIdentifier();

        $dom = self::convertToDOM($keyident);

        if(!$dom->hasAttribute('EncodingType')) {
            throw new Exception\RuntimeException("Unable to determine the encoding type for the key identifier");
        }

        return $dom->getAttribute('EncodingType');
    }

    /**
     * Get the key reference data used to identify the public key
     *
     * @param bool $decode if true, will return a decoded version of the key
     * @return string the key reference thumbprint, either in binary or encoded form
     * @throws Exception\ExceptionInterface
     */
    public function getKeyReference($decode = true)
    {
        $keyIdentifier = $this->_getKeyIdentifier();

        $dom = self::convertToDOM($keyIdentifier);
        $encoded = $dom->nodeValue;

        if(empty($encoded)) {
            throw new Exception\InvalidArgumentException("Could not find the Key Reference Encoded Value");
        }

        if($decode) {

            $decoded = "";
            switch($this->getKeyThumbprintEncodingType()) {
                case self::ENCODING_BASE64BIN:
                    $decoded = base64_decode($encoded, true);
                    break;
                default:
                    throw new Exception\RuntimeException("Unknown Key Reference Encoding Type: {$this->getKeyThumbprintEncodingType()}");
            }

            if(!$decoded || empty($decoded)) {
                throw new Exception\RuntimeException("Failed to decode key reference");
            }

            return $decoded;
        }

        return $encoded;
    }
}
