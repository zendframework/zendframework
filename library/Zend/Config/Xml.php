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
 * @category  Zend
 * @package   Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config;

/**
 * XML Adapter for Zend_Config
 *
 * @uses      \Zend\Config\Config
 * @uses      \Zend\Config\Exception
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Xml extends Config
{
    /**
     * XML namespace for ZF-related tags and attributes
     */
    const XML_NAMESPACE = 'http://framework.zend.com/xml/zend-config-xml/1.0/';

    /**
     * Whether to skip extends or not
     *
     * @var boolean
     */
    protected $_skipExtends = false;

    /**
     * Loads the section $section from the config file (or string $xml for
     * access facilitated by nested object properties.
     *
     * Sections are defined in the XML as children of the root element.
     *
     * In order to extend another section, a section defines the "extends"
     * attribute having a value of the section name from which the extending
     * section inherits values.
     *
     * Note that the keys in $section will override any keys of the same
     * name in the sections that have been included via "extends".
     *
     * @param  string  $xml     XML file or string to process
     * @param  mixed   $section Section to process
     * @param  boolean $options Whether modifications are allowed at runtime
     * @throws \Zend\Config\Exception When xml is not set or cannot be loaded
     * @throws \Zend\Config\Exception When section $sectionName cannot be found in $xml
     */
    public function __construct($xml, $section = null, $options = false)
    {
        if (empty($xml)) {
            throw new Exception\InvalidArgumentException('Filename is not set');
        }

        $allowModifications = false;
        if (is_bool($options)) {
            $allowModifications = $options;
        } elseif (is_array($options)) {
            if (isset($options['allowModifications'])) {
                $allowModifications = (bool) $options['allowModifications'];
            }
            if (isset($options['skipExtends'])) {
                $this->_skipExtends = (bool) $options['skipExtends'];
            }
        }

        // load XML and throw exception of each failure using previous exception
        $oldUseInternalErrors = libxml_use_internal_errors(true);
        if ($oldUseInternalErrors) {
            libxml_clear_errors();
        }
        if (strstr($xml, '<' . '?xml')) { // string concat to fix syntax highlighting
            $config = simplexml_load_string($xml);
        } else {
            $config = simplexml_load_file($xml);
        }
        $xmlErrors = libxml_get_errors();
        if (!$oldUseInternalErrors) {
            libxml_use_internal_errors(false);
        }
        if ( ($xmlErrorCnt = count($xmlErrors)) ) {
            libxml_clear_errors();

            // create and throw exception stack
            $e = null;
            foreach ($xmlErrors as $xmlError) {
                $msg  = trim($xmlError->message);
                $line = $xmlError->line;
                $col  = $xmlError->column;
                $e = new Exception\RuntimeException(
                    $msg . ' @ line/column ' . $line . '/' . $col, 0, $e
                );
            }
            throw $e;
        }

        if ($section === null) {
            $dataArray = array();
            foreach ($config as $sectionName => $sectionData) {
                $dataArray[$sectionName] = $this->_processExtends($config, $sectionName);
            }

            parent::__construct($dataArray, $allowModifications);
        } else if (is_array($section)) {
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($config->$sectionName)) {
                    throw new Exception\InvalidArgumentException("Section '$sectionName' cannot be found in $xml");
                }

                $dataArray = array_merge($this->_processExtends($config, $sectionName), $dataArray);
            }

            parent::__construct($dataArray, $allowModifications);
        } else {
            if (!isset($config->$section)) {
                throw new Exception\InvalidArgumentException("Section '$section' cannot be found in $xml");
            }

            $dataArray = $this->_processExtends($config, $section);
            if (!is_array($dataArray)) {
                // Section in the XML file contains just one top level string
                $dataArray = array($section => $dataArray);
            }

            parent::__construct($dataArray, $allowModifications);
        }

        $this->_loadedSection = $section;
    }

    /**
     * Helper function to process each element in the section and handle
     * the "extends" inheritance attribute.
     *
     * @param  SimpleXMLElement $element XML Element to process
     * @param  string           $section Section to process
     * @param  array            $config  Configuration which was parsed yet
     * @throws \Zend\Config\Exception When $section cannot be found
     * @return array
     */
    protected function _processExtends(\SimpleXMLElement $element, $section, array $config = array())
    {
        if (!isset($element->$section)) {
            throw new Exception\RuntimeException("Section '$section' cannot be found");
        }

        $thisSection  = $element->$section;
        $nsAttributes = $thisSection->attributes(self::XML_NAMESPACE);

        if (isset($thisSection['extends']) || isset($nsAttributes['extends'])) {
            $extendedSection = (string) (isset($nsAttributes['extends']) ? $nsAttributes['extends'] : $thisSection['extends']);
            $this->_assertValidExtend($section, $extendedSection);

            if (!$this->_skipExtends) {
                $config = $this->_processExtends($element, $extendedSection, $config);
            }
        }

        $config = $this->_arrayMergeRecursive($config, $this->_toArray($thisSection));

        return $config;
    }

    /**
     * Returns a string or an associative and possibly multidimensional array from
     * a SimpleXMLElement.
     *
     * @param  SimpleXMLElement $xmlObject Convert a SimpleXMLElement into an array
     * @return array|string
     */
    protected function _toArray(\SimpleXMLElement $xmlObject)
    {
        $config       = array();
        $nsAttributes = $xmlObject->attributes(self::XML_NAMESPACE);

        // Search for parent node values
        if (count($xmlObject->attributes()) > 0) {
            foreach ($xmlObject->attributes() as $key => $value) {
                if ($key === 'extends') {
                    continue;
                }

                $value = (string) $value;

                if (array_key_exists($key, $config)) {
                    if (!is_array($config[$key])) {
                        $config[$key] = array($config[$key]);
                    }

                    $config[$key][] = $value;
                } else {
                    $config[$key] = $value;
                }
            }
        }

        // Search for local 'const' nodes and replace them
        if (count($xmlObject->children(self::XML_NAMESPACE)) > 0) {
            if (count($xmlObject->children()) > 0) {
                throw new Exception\RuntimeException("A node with a 'const' childnode may not have any other children");
            }

            $dom                 = dom_import_simplexml($xmlObject);
            $namespaceChildNodes = array();

            // We have to store them in an array, as replacing nodes will
            // confuse the DOMNodeList later
            foreach ($dom->childNodes as $node) {
                if ($node instanceof \DOMElement && $node->namespaceURI === self::XML_NAMESPACE) {
                    $namespaceChildNodes[] = $node;
                }
            }

            foreach ($namespaceChildNodes as $node) {
                switch ($node->localName) {
                    case 'const':
                        if (!$node->hasAttributeNS(self::XML_NAMESPACE, 'name')) {
                            throw new Exception\RuntimeException("Misssing 'name' attribute in 'const' node");
                        }

                        $constantName = $node->getAttributeNS(self::XML_NAMESPACE, 'name');

                        if (!defined($constantName)) {
                            throw new Exception\RuntimeException("Constant with name '$constantName' was not defined");
                        }

                        $constantValue = constant($constantName);

                        $dom->replaceChild($dom->ownerDocument->createTextNode($constantValue), $node);
                        break;

                    default:
                        throw new Exception\RuntimeException("Unknown node with name '$node->localName' found");
                }
            }

            return (string) simplexml_import_dom($dom);
        }

        // Search for children
        if (count($xmlObject->children()) > 0) {
            foreach ($xmlObject->children() as $key => $value) {
                if (count($value->children()) > 0 || count($value->children(self::XML_NAMESPACE)) > 0) {
                    $value = $this->_toArray($value);
                } else if (count($value->attributes()) > 0) {
                    $attributes = $value->attributes();
                    if (isset($attributes['value'])) {
                        $value = (string) $attributes['value'];
                    } else {
                        $value = $this->_toArray($value);
                    }
                } else {
                    $value = (string) $value;
                }

                if (array_key_exists($key, $config)) {
                    if (!is_array($config[$key]) || !array_key_exists(0, $config[$key])) {
                        $config[$key] = array($config[$key]);
                    }

                    $config[$key][] = $value;
                } else {
                    $config[$key] = $value;
                }
            }
        } else if (!isset($xmlObject['extends']) && !isset($nsAttributes['extends']) && (count($config) === 0)) {
            // Object has no children nor attributes and doesn't use the extends
            // attribute: it's a string
            $config = (string) $xmlObject;
        }

        return $config;
    }

    /**
     * Merge two arrays recursively, overwriting keys of the same name
     * in $firstArray with the value in $secondArray.
     *
     * @param  mixed $firstArray  First array
     * @param  mixed $secondArray Second array to merge into first array
     * @return array
     */
    protected function _arrayMergeRecursive($firstArray, $secondArray)
    {
        if (is_array($firstArray) && is_array($secondArray)) {
            foreach ($secondArray as $key => $value) {
                if (isset($firstArray[$key])) {
                    $firstArray[$key] = $this->_arrayMergeRecursive($firstArray[$key], $value);
                } else {
                    if($key === 0) {
                        $firstArray= array(0=>$this->_arrayMergeRecursive($firstArray, $value));
                    } else {
                        $firstArray[$key] = $value;
                    }
                }
            }
        } else {
            $firstArray = $secondArray;
        }

        return $firstArray;
    }
}
