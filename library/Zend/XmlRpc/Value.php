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
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\XmlRpc;

/**
 * Represent a native XML-RPC value entity, used as parameters for the methods
 * called by the Zend\XmlRpc\Client object and as the return value for those calls.
 *
 * This object as a very important static function Zend\XmlRpc\Value::getXmlRpcValue, this
 * function acts likes a factory for the Zend\XmlRpc\Value objects
 *
 * Using this function, users/Zend\XmlRpc\Client object can create the Zend\XmlRpc\Value objects
 * from PHP variables, XML string or by specifing the exact XML-RPC natvie type
 *
 * @package    Zend_XmlRpc
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Value
{
    /**
     * The native XML-RPC representation of this object's value
     *
     * If the native type of this object is array or struct, this will be an array
     * of Zend_XmlRpc_Value objects
     */
    protected $_value;

    /**
     * The native XML-RPC type of this object
     * One of the XMLRPC_TYPE_* constants
     */
    protected $_type;

    /**
     * XML code representation of this object (will be calculated only once)
     */
    protected $_xml;

    /**
     * @var Zend\XmlRpc\Generator\GeneratorAbstract
     */
    protected static $_generator;

    /**
     * Specify that the XML-RPC native type will be auto detected from a PHP variable type
     */
    const AUTO_DETECT_TYPE = 'auto_detect';

    /**
     * Specify that the XML-RPC value will be parsed out from a given XML code
     */
    const XML_STRING = 'xml';

    /**
     * All the XML-RPC native types
     */
    const XMLRPC_TYPE_I4        = 'i4';
    const XMLRPC_TYPE_INTEGER   = 'int';
    const XMLRPC_TYPE_I8        = 'i8';
    const XMLRPC_TYPE_APACHEI8  = 'ex:i8';
    const XMLRPC_TYPE_DOUBLE    = 'double';
    const XMLRPC_TYPE_BOOLEAN   = 'boolean';
    const XMLRPC_TYPE_STRING    = 'string';
    const XMLRPC_TYPE_DATETIME  = 'dateTime.iso8601';
    const XMLRPC_TYPE_BASE64    = 'base64';
    const XMLRPC_TYPE_ARRAY     = 'array';
    const XMLRPC_TYPE_STRUCT    = 'struct';
    const XMLRPC_TYPE_NIL       = 'nil';
    const XMLRPC_TYPE_APACHENIL = 'ex:nil';

    /**
     * Get the native XML-RPC type (the type is one of the Zend_XmlRpc_Value::XMLRPC_TYPE_* constants)
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get XML generator instance
     *
     * @return \Zend\XmlRpc\Generator\GeneratorInterface
     */
    public static function getGenerator()
    {
        if (!self::$_generator) {
            if (extension_loaded('xmlwriter')) {
                self::$_generator = new Generator\XmlWriter();
            } else {
                self::$_generator = new Generator\DomDocument();
            }
        }

        return self::$_generator;
    }

    /**
     * Sets XML generator instance
     *
     * @param  null|Generator\GeneratorInterface $generator
     * @return void
     */
    public static function setGenerator(Generator\GeneratorInterface $generator = null)
    {
        self::$_generator = $generator;
    }

    /**
     * Changes the encoding of the generator
     *
     * @param string $encoding
     * @return void
     */
    public static function setEncoding($encoding)
    {
        $generator    = self::getGenerator();
        $newGenerator = new $generator($encoding);
        self::setGenerator($newGenerator);
    }

    /**
     * Return the value of this object, convert the XML-RPC native value into a PHP variable
     *
     * @return mixed
     */
    abstract public function getValue();


    /**
     * Return the XML code that represent a native MXL-RPC value
     *
     * @return string
     */
    public function saveXml()
    {
        if (!$this->_xml) {
            $this->generateXml();
            $this->_xml = (string) $this->getGenerator();
        }
        return $this->_xml;
    }

    /**
     * Generate XML code that represent a native XML/RPC value
     *
     * @return void
     */
    public function generateXml()
    {
        $this->_generateXml();
    }

    /**
     * Creates a Zend_XmlRpc_Value* object, representing a native XML-RPC value
     * A XmlRpcValue object can be created in 3 ways:
     * 1. Autodetecting the native type out of a PHP variable
     *    (if $type is not set or equal to Zend_XmlRpc_Value::AUTO_DETECT_TYPE)
     * 2. By specifing the native type ($type is one of the Zend_XmlRpc_Value::XMLRPC_TYPE_* constants)
     * 3. From a XML string ($type is set to Zend_XmlRpc_Value::XML_STRING)
     *
     * By default the value type is autodetected according to it's PHP type
     *
     * @param  mixed $value
     * @param  Zend\XmlRpc\Value::constant $type
     * @return Value
     */
    public static function getXmlRpcValue($value, $type = self::AUTO_DETECT_TYPE)
    {
        switch ($type) {
            case self::AUTO_DETECT_TYPE:
                // Auto detect the XML-RPC native type from the PHP type of $value
                return self::_phpVarToNativeXmlRpc($value);

            case self::XML_STRING:
                // Parse the XML string given in $value and get the XML-RPC value in it
                return self::_xmlStringToNativeXmlRpc($value);

            case self::XMLRPC_TYPE_I4:
                // fall through to the next case
            case self::XMLRPC_TYPE_INTEGER:
                return new Value\Integer($value);

            case self::XMLRPC_TYPE_I8:
                // fall through to the next case
            case self::XMLRPC_TYPE_APACHEI8:
                return new Value\BigInteger($value);

            case self::XMLRPC_TYPE_DOUBLE:
                return new Value\Double($value);

            case self::XMLRPC_TYPE_BOOLEAN:
                return new Value\Boolean($value);

            case self::XMLRPC_TYPE_STRING:
                return new Value\String($value);

            case self::XMLRPC_TYPE_BASE64:
                return new Value\Base64($value);

            case self::XMLRPC_TYPE_NIL:
                // fall through to the next case
            case self::XMLRPC_TYPE_APACHENIL:
                return new Value\Nil();

            case self::XMLRPC_TYPE_DATETIME:
                return new Value\DateTime($value);

            case self::XMLRPC_TYPE_ARRAY:
                return new Value\ArrayValue($value);

            case self::XMLRPC_TYPE_STRUCT:
                return new Value\Struct($value);

            default:
                throw new Exception\ValueException('Given type is not a '. __CLASS__ .' constant');
        }
    }


    /**
     * Transform a PHP native variable into a XML-RPC native value
     *
     * @param mixed $value The PHP variable for convertion
     *
     * @return Zend\XmlRpc\Value
     * @static
     */
    protected static function _phpVarToNativeXmlRpc($value)
    {
        switch (gettype($value)) {
            case 'object':
                // Check to see if it's an XmlRpc value
                if ($value instanceof Value) {
                    return $value;
                }

                if ($value instanceof \Zend\Math\BigInteger) {
                    return new Value\BigInteger($value);
                }

                if ($value instanceof \Zend\Date\Date or $value instanceof \DateTime) {
                    return new Value\DateTime($value);
                }

                // Otherwise, we convert the object into a struct
                $value = get_object_vars($value);
                // Break intentionally omitted
            case 'array':
                // Default native type for a PHP array (a simple numeric array) is 'array'
                $obj = 'Zend\\XmlRpc\\Value\\ArrayValue';

                // Determine if this is an associative array
                if (!empty($value) && is_array($value) && (array_keys($value) !== range(0, count($value) - 1))) {
                    $obj = 'Zend\\XmlRpc\\Value\\Struct';
                }
                return new $obj($value);

            case 'integer':
                return new Value\Integer($value);

            case 'double':
                return new Value\Double($value);

            case 'boolean':
                return new Value\Boolean($value);

            case 'NULL':
            case 'null':
                return new Value\Nil();

            case 'string':
                // Fall through to the next case
            default:
                // If type isn't identified (or identified as string), it treated as string
                return new Value\String($value);
        }
    }


    /**
     * Transform an XML string into a XML-RPC native value
     *
     * @param string|SimpleXMLElement $xml A SimpleXMLElement object represent the XML string
     *                                            It can be also a valid XML string for convertion
     *
     * @return Zend\XmlRpc\Value\Value
     * @static
     */
    protected static function _xmlStringToNativeXmlRpc($xml)
    {
        self::_createSimpleXMLElement($xml);

        self::_extractTypeAndValue($xml, $type, $value);

        switch ($type) {
            // All valid and known XML-RPC native values
            case self::XMLRPC_TYPE_I4:
                // Fall through to the next case
            case self::XMLRPC_TYPE_INTEGER:
                $xmlrpcValue = new Value\Integer($value);
                break;
            case self::XMLRPC_TYPE_APACHEI8:
                // Fall through to the next case
            case self::XMLRPC_TYPE_I8:
                $xmlrpcValue = new Value\BigInteger($value);
                break;
            case self::XMLRPC_TYPE_DOUBLE:
                $xmlrpcValue = new Value\Double($value);
                break;
            case self::XMLRPC_TYPE_BOOLEAN:
                $xmlrpcValue = new Value\Boolean($value);
                break;
            case self::XMLRPC_TYPE_STRING:
                $xmlrpcValue = new Value\String($value);
                break;
            case self::XMLRPC_TYPE_DATETIME:  // The value should already be in a iso8601 format
                $xmlrpcValue = new Value\DateTime($value);
                break;
            case self::XMLRPC_TYPE_BASE64:    // The value should already be base64 encoded
                $xmlrpcValue = new Value\Base64($value, true);
                break;
            case self::XMLRPC_TYPE_NIL:
                // Fall through to the next case
            case self::XMLRPC_TYPE_APACHENIL:
                // The value should always be NULL
                $xmlrpcValue = new Value\Nil();
                break;
            case self::XMLRPC_TYPE_ARRAY:
                // PHP 5.2.4 introduced a regression in how empty($xml->value)
                // returns; need to look for the item specifically
                $data = null;
                foreach ($value->children() as $key => $value) {
                    if ('data' == $key) {
                        $data = $value;
                        break;
                    }
                }

                if (null === $data) {
                    throw new Exception\ValueException('Invalid XML for XML-RPC native '. self::XMLRPC_TYPE_ARRAY .' type: ARRAY tag must contain DATA tag');
                }
                $values = array();
                // Parse all the elements of the array from the XML string
                // (simple xml element) to Zend_XmlRpc_Value objects
                foreach ($data->value as $element) {
                    $values[] = self::_xmlStringToNativeXmlRpc($element);
                }
                $xmlrpcValue = new Value\ArrayValue($values);
                break;
            case self::XMLRPC_TYPE_STRUCT:
                $values = array();
                // Parse all the memebers of the struct from the XML string
                // (simple xml element) to Zend_XmlRpc_Value objects
                foreach ($value->member as $member) {
                    // @todo? If a member doesn't have a <value> tag, we don't add it to the struct
                    // Maybe we want to throw an exception here ?
                    if (!isset($member->value) or !isset($member->name)) {
                        continue;
                        //throw new Zend_XmlRpc_Value_Exception('Member of the '. self::XMLRPC_TYPE_STRUCT .' XML-RPC native type must contain a VALUE tag');
                    }
                    $values[(string)$member->name] = self::_xmlStringToNativeXmlRpc($member->value);
                }
                $xmlrpcValue = new Value\Struct($values);
                break;
            default:
                throw new Exception\ValueException('Value type \''. $type .'\' parsed from the XML string is not a known XML-RPC native type');
                break;
        }
        $xmlrpcValue->_setXML($xml->asXML());

        return $xmlrpcValue;
    }

    protected static function _createSimpleXMLElement(&$xml)
    {
        if ($xml instanceof \SimpleXMLElement) {
            return;
        }

        try {
            $xml = new \SimpleXMLElement($xml);
        } catch (\Exception $e) {
            // The given string is not a valid XML
            throw new Exception\ValueException('Failed to create XML-RPC value from XML string: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Extract XML/RPC type and value from SimpleXMLElement object
     *
     * @param SimpleXMLElement $xml
     * @param string &$type Type bind variable
     * @param string &$value Value bind variable
     * @return void
     */
    protected static function _extractTypeAndValue(\SimpleXMLElement $xml, &$type, &$value)
    {
        list($type, $value) = each($xml);
        if (!$type and $value === null) {
            $namespaces = array('ex' => 'http://ws.apache.org/xmlrpc/namespaces/extensions');
            foreach ($namespaces as $namespaceName => $namespaceUri) {
                $namespaceXml = $xml->children($namespaceUri);
                list($type, $value) = each($namespaceXml);
                if ($type !== null) {
                    $type = $namespaceName . ':' . $type;
                    break;
                }
            }
        }

        // If no type was specified, the default is string
        if (!$type) {
            $type = self::XMLRPC_TYPE_STRING;
        }
    }

    /**
     * @param $xml
     * @return void
     */
    protected function _setXML($xml)
    {
        $this->_xml = $this->getGenerator()->stripDeclaration($xml);
    }
}
