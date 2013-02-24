<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace Zend\Soap;

use DOMDocument;
use DOMElement;
use Zend\Soap\Exception\InvalidArgumentException;
use Zend\Soap\Wsdl\ComplexTypeStrategy\ComplexTypeStrategyInterface as ComplexTypeStrategy;
use Zend\Uri\Uri;

/**
 * \Zend\Soap\Wsdl
 *
 * @category   Zend
 * @package    Zend_Soap
 */
class Wsdl
{
    /**
     * @var object DomDocument Instance
     */
    private $dom;

    /**
     * @var object WSDL Root XML_Tree_Node
     */
    private $wsdl;

    /**
     * @var string URI where the WSDL will be available
     */
    private $uri;

    /**
     * @var DOMElement
     */
    private $schema = null;

    /**
     * Types defined on schema
     *
     * @var array
     */
    private $includedTypes = array();

    /**
     * Strategy for detection of complex types
     */
    protected $strategy = null;

    /**
     * Map of PHP Class names to WSDL QNames.
     *
     * @var array
     */
    protected $classMap = array();

    const NS_WSDL           = 'http://schemas.xmlsoap.org/wsdl/';
    const NS_XMLNS          = 'http://www.w3.org/2000/xmlns/';
    const NS_SOAP           = 'http://schemas.xmlsoap.org/wsdl/soap/';
    const NS_SCHEMA         = 'http://www.w3.org/2001/XMLSchema';
    const NS_S_ENC          = 'http://schemas.xmlsoap.org/soap/encoding/';

    /**
     * Constructor
     *
     * @param string  $name Name of the Web Service being Described
     * @param string|Uri $uri URI where the WSDL will be available
     * @param null|ComplexTypeStrategy $strategy Strategy for detection of complex types
     * @param null|array $classMap Map of PHP Class names to WSDL QNames
     * @throws Exception\RuntimeException
     */
    public function __construct($name, $uri, ComplexTypeStrategy $strategy = null, array $classMap = array())
    {
        if ($uri instanceof Uri) {
            $uri = $uri->toString();
        }

        $this->setUri($uri);
        $this->classMap = $classMap;

        $this->dom = $this->getDOMDocument($name, $this->getUri());

        $this->wsdl = $this->dom->documentElement;

        $this->setComplexTypeStrategy($strategy ?: new Wsdl\ComplexTypeStrategy\DefaultComplexType);
    }

    /**
     * Get the wsdl XML document with all namespaces and required attributes
     *
     * @param string $uri
     * @param string $name
     * @return \DOMDocument
     */
    protected function getDOMDocument($name, $uri = null)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = true;
        $dom->encoding = 'UTF-8';

        $definitions = $dom->createElementNS(Wsdl::NS_WSDL,             'definitions');

        $dom->appendChild($definitions);

        $uri = $this->sanitizeUri($uri);

        $definitions->setAttribute('name',             $name);
        $definitions->setAttribute('targetNamespace',  $uri);

        $definitions->setAttributeNS(Wsdl::NS_XMLNS, 'xmlns:wsdl',      Wsdl::NS_WSDL);
        $definitions->setAttributeNS(Wsdl::NS_XMLNS, 'xmlns:tns',       $uri);
        $definitions->setAttributeNS(Wsdl::NS_XMLNS, 'xmlns:soap',      Wsdl::NS_SOAP);
        $definitions->setAttributeNS(Wsdl::NS_XMLNS, 'xmlns:xsd',       Wsdl::NS_SCHEMA);
        $definitions->setAttributeNS(Wsdl::NS_XMLNS, 'xmlns:soap-enc',  Wsdl::NS_S_ENC);

        return $dom;
    }

    /**
     * Get the class map of php to wsdl qname types.
     *
     * @return array
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * Set the class map of php to wsdl qname types.
     */
    public function setClassMap($classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * Set a new uri for this WSDL
     *
     * @param  string|Uri $uri
     * @return \Zend\Soap\Wsdl
     */
    public function setUri($uri)
    {
        $uri = $this->sanitizeUri($uri);

        $oldUri = $this->uri;
        $this->uri = $uri;

        if($this->dom instanceof \DOMDocument ) {
            // namespace declarations are NOT true attributes
            $this->dom->documentElement->setAttributeNS(Wsdl::NS_XMLNS,     'xmlns:tns',        $uri);


            $xpath = new \DOMXPath($this->dom);
            $xpath->registerNamespace('unittest',     Wsdl::NS_WSDL);

            $xpath->registerNamespace('tns',          $uri);
            $xpath->registerNamespace('soap',         Wsdl::NS_SOAP);
            $xpath->registerNamespace('xsd',          Wsdl::NS_SCHEMA);
            $xpath->registerNamespace('soap-enc',     Wsdl::NS_S_ENC);
            $xpath->registerNamespace('wsdl',         Wsdl::NS_WSDL);

            // select only attribute nodes. Data nodes does not contain uri except for documentation node but
            // this is for the user to decide
            $attributeNodes = $xpath->query('//attribute::*[contains(., "'.$oldUri.'")]');

            /** @var $node \DOMAttr */
            foreach ($attributeNodes as $node) {
//                var_dump(array($oldUri, $uri, $node->nodeValue, str_replace($oldUri, $uri, $node->nodeValue)));
                $node->nodeValue = str_replace($oldUri, $uri, $node->nodeValue);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Function for sanitizing uri
     *
     * @param $uri
     *
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function sanitizeUri($uri) {

        if ($uri instanceof Uri) {
            $uri = $uri->toString();
        }

        $uri = trim($uri);
        $uri = htmlspecialchars($uri, ENT_QUOTES, 'UTF-8', false);

        if (empty($uri)) {
            throw new InvalidArgumentException('Uri contains invalid characters or is empty');
        }

        return $uri;
    }

    /**
     * Set a strategy for complex type detection and handling
     *
     * @param ComplexTypeStrategy $strategy
     * @return \Zend\Soap\Wsdl
     */
    public function setComplexTypeStrategy(ComplexTypeStrategy $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * Get the current complex type strategy
     *
     * @return ComplexTypeStrategy
     */
    public function getComplexTypeStrategy()
    {
        return $this->strategy;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_messages message} element to the WSDL
     *
     * @param string $messageName Name for the {@link http://www.w3.org/TR/wsdl#_messages message}
     * @param array $parts An array of {@link http://www.w3.org/TR/wsdl#_message parts}
     *                     The array is constructed like:
	 * 						'name of part' => 'part xml schema data type' or
	 * 						'name of part' => array('type' => 'part xml schema type')  or
	 * 						'name of part' => array('element' => 'part xml element name')
	 *
     * @return object The new message's XML_Tree_Node for use in {@link function addDocumentation}
     */
    public function addMessage($messageName, $parts)
    {
        $message = $this->dom->createElementNS(Wsdl::NS_WSDL, 'message');

        $message->setAttribute('name', $messageName);

        if (count($parts) > 0) {
            foreach ($parts as $name => $type) {
                $part = $this->dom->createElementNS(Wsdl::NS_WSDL, 'part');
                $part->setAttribute('name', $name);
                if (is_array($type)) {
                    foreach ($type as $key => $value) {
                        $part->setAttribute($key, $value);
                    }
                } else {
                    $part->setAttribute('type', $type);
                }
                $message->appendChild($part);
            }
        }

        $this->wsdl->appendChild($message);

        return $message;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_porttypes portType} element to the WSDL
     *
     * @param string $name portType element's name
     * @return object The new portType's XML_Tree_Node for use in {@link function addPortOperation} and {@link function addDocumentation}
     */
    public function addPortType($name)
    {
        $portType = $this->dom->createElementNS(Wsdl::NS_WSDL, 'portType');
        $portType->setAttribute('name', $name);
        $this->wsdl->appendChild($portType);

        return $portType;
    }

    /**
     * Add an {@link http://www.w3.org/TR/wsdl#request-response operation} element to a portType element
     *
     * @param object $portType a portType XML_Tree_Node, from {@link function addPortType}
     * @param string $name Operation name
     * @param string $input Input Message
     * @param string $output Output Message
     * @param string $fault Fault Message
     * @return object The new operation's XML_Tree_Node for use in {@link function addDocumentation}
     */
    public function addPortOperation($portType, $name, $input = false, $output = false, $fault = false)
    {
        $operation = $this->dom->createElementNS(Wsdl::NS_WSDL, 'operation');
        $operation->setAttribute('name', $name);

        if (is_string($input) && (strlen(trim($input)) >= 1)) {
            $node = $this->dom->createElementNS(Wsdl::NS_WSDL, 'input');
            $node->setAttribute('message', $input);
            $operation->appendChild($node);
        }
        if (is_string($output) && (strlen(trim($output)) >= 1)) {
            $node= $this->dom->createElementNS(Wsdl::NS_WSDL, 'output');
            $node->setAttribute('message', $output);
            $operation->appendChild($node);
        }
        if (is_string($fault) && (strlen(trim($fault)) >= 1)) {
            $node = $this->dom->createElementNS(Wsdl::NS_WSDL, 'fault');
            $node->setAttribute('message', $fault);
            $operation->appendChild($node);
        }

        $portType->appendChild($operation);

        return $operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_bindings binding} element to WSDL
     *
     * @param string $name Name of the Binding
	 * @param string $portType name of the portType to bind
	 *
     * @return object The new binding's XML_Tree_Node for use with {@link function addBindingOperation} and {@link function addDocumentation}
     */
    public function addBinding($name, $portType)
    {
        $binding = $this->dom->createElementNS(Wsdl::NS_WSDL, 'binding');
        $this->wsdl->appendChild($binding);

        $attr = $this->dom->createAttribute('name');
        $attr->value = $name;
        $binding->appendChild($attr);

        $attr = $this->dom->createAttribute('type');
        $attr->value = $portType;
        $binding->appendChild($attr);

        return $binding;
    }

    /**
     * Add an operation to a binding element
     *
     * @param object $binding A binding XML_Tree_Node returned by {@link function addBinding}
	 * @param string $name
	 * @param array|bool $input An array of attributes for the input element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
	 * @param array|bool $output An array of attributes for the output element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
	 * @param array|bool $fault An array with attributes for the fault element, allowed keys are: 'name', 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
	 *
     * @return object The new Operation's XML_Tree_Node for use with {@link function addSoapOperation} and {@link function addDocumentation}
     */
    public function addBindingOperation($binding, $name, $input = false, $output = false, $fault = false)
    {
        $operation = $this->dom->createElementNS(Wsdl::NS_WSDL, 'operation');
        $binding->appendChild($operation);

        $attr = $this->dom->createAttribute('name');
        $attr->value = $name;
        $operation->appendChild($attr);

        if (is_array($input) AND !empty($input)) {
            $node = $this->dom->createElementNS(Wsdl::NS_WSDL, 'input');
            $operation->appendChild($node);

            $soapNode = $this->dom->createElementNS(Wsdl::NS_SOAP, 'body');
            $node->appendChild($soapNode);

            foreach ($input as $name => $value) {
                $attr = $this->dom->createAttribute($name);
                $attr->value = $value;
                $soapNode->appendChild($attr);
            }

        }

        if (is_array($output) AND !empty($output)) {
            $node = $this->dom->createElementNS(Wsdl::NS_WSDL, 'output');
            $operation->appendChild($node);

            $soapNode = $this->dom->createElementNS(Wsdl::NS_SOAP, 'body');
            $node->appendChild($soapNode);

            foreach ($output as $name => $value) {
                $attr = $this->dom->createAttribute($name);
                $attr->value = $value;
                $soapNode->appendChild($attr);
            }
        }

        if (is_array($fault) AND !empty($fault)) {
            $node = $this->dom->createElementNS(Wsdl::NS_WSDL, 'fault');
            $operation->appendChild($node);

            foreach ($fault as $name => $value) {
                $attr = $this->dom->createAttribute($name);
                $attr->value = $value;

                $node->appendChild($attr);
            }
        }

        return $operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:binding SOAP binding} element to a Binding element
     *
     * @param object $binding A binding XML_Tree_Node returned by {@link function addBinding}
     * @param string $style binding style, possible values are "rpc" (the default) and "document"
     * @param string $transport Transport method (defaults to HTTP)
     * @return bool
     */
    public function addSoapBinding($binding, $style = 'document', $transport = 'http://schemas.xmlsoap.org/soap/http')
    {

        $soapBinding = $this->dom->createElementNS(WSDL::NS_SOAP, 'binding');
        $soapBinding->setAttribute('style', $style);
        $soapBinding->setAttribute('transport', $transport);

        $binding->appendChild($soapBinding);

        return $soapBinding;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:operation SOAP operation} to an operation element
     *
     * @param object $operation An operation XML_Tree_Node returned by {@link function addBindingOperation}
     * @param string $soapAction SOAP Action
     * @return bool
     */
    public function addSoapOperation($binding, $soapAction)
    {
        if ($soapAction instanceof Uri) {
            $soapAction = $soapAction->toString();
        }
        $soapOperation = $this->dom->createElementNS(WSDL::NS_SOAP, 'operation');
        $soapOperation->setAttribute('soapAction', $soapAction);

        $binding->insertBefore($soapOperation, $binding->firstChild);

        return $soapOperation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_services service} element to the WSDL
     *
     * @param string $name Service Name
     * @param string $portName Name of the port for the service
     * @param string $binding Binding for the port
     * @param string $location SOAP Address for the service
     * @return object The new service's XML_Tree_Node for use with {@link function addDocumentation}
     */
    public function addService($name, $portName, $binding, $location)
    {
        if ($location instanceof Uri) {
            $location = $location->toString();
        }
        $service = $this->dom->createElementNS(WSDL::NS_WSDL, 'service');
        $service->setAttribute('name', $name);

        $this->wsdl->appendChild($service);

        $port = $this->dom->createElementNS(WSDL::NS_WSDL, 'port');
        $port->setAttribute('name', $portName);
        $port->setAttribute('binding', $binding);

        $service->appendChild($port);

        $soapAddress = $this->dom->createElementNS(WSDL::NS_SOAP, 'address');
        $soapAddress->setAttribute('location', $location);

        $port->appendChild($soapAddress);

        return $service;
    }

    /**
     * Add a documentation element to any element in the WSDL.
     *
     * Note that the WSDL {@link http://www.w3.org/TR/wsdl#_documentation specification} uses 'document',
     * but the WSDL {@link http://schemas.xmlsoap.org/wsdl/ schema} uses 'documentation' instead.
     * The {@link http://www.ws-i.org/Profiles/BasicProfile-1.1-2004-08-24.html#WSDL_documentation_Element WS-I Basic Profile 1.1} recommends using 'documentation'.
     *
     * @param object $inputNode An XML_Tree_Node returned by another method to add the documentation to
     * @param string $documentation Human readable documentation for the node
     * @return DOMElement The documentation element
     */
    public function addDocumentation($inputNode, $documentation)
    {
        if ($inputNode === $this) {
            $node = $this->dom->documentElement;
        } else {
            $node = $inputNode;
        }

        $doc = $this->dom->createElementNS(WSDL::NS_WSDL, 'documentation');
        if ($node->hasChildNodes()) {
            $node->insertBefore($doc, $node->firstChild);
        } else {
            $node->appendChild($doc);
        }

        $docCData = $this->dom->createTextNode(str_replace(array("\r\n", "\r"), "\n", $documentation));
        $doc->appendChild($docCData);

        return $doc;
    }

    /**
     * Add WSDL Types element
     *
     * @param object $types A DomDocument|DomNode|DomElement|DomDocumentFragment with all the XML Schema types defined in it
     *
     * @return void
     */
    public function addTypes($types)
    {
        if ($types instanceof \DomDocument) {
            $dom = $this->dom->importNode($types->documentElement);
            $this->wsdl->appendChild($dom);
        } elseif ($types instanceof \DomNode || $types instanceof \DomElement || $types instanceof \DomDocumentFragment ) {
            $dom = $this->dom->importNode($types);
            $this->wsdl->appendChild($dom);
        }
    }

    /**
     * Add a complex type name that is part of this WSDL and can be used in signatures.
     *
     * @param string $type
     * @param string $wsdlType
     * @return \Zend\Soap\Wsdl
     */
    public function addType($type, $wsdlType)
    {
        if (!isset($this->includedTypes[$type])) {
            $this->includedTypes[$type] = $wsdlType;
        }
        return $this;
    }

    /**
     * Return an array of all currently included complex types
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->includedTypes;
    }

    /**
     * Return the Schema node of the WSDL
     *
     * @return DOMElement
     */
    public function getSchema()
    {
        if ($this->schema == null) {
            $this->addSchemaTypeSection();
        }

        return $this->schema;
    }

    /**
     * Return the WSDL as XML
     *
     * @return string WSDL as XML
     */
    public function toXML()
    {
           return $this->dom->saveXML();
    }

    /**
     * Return DOM Document
     *
     * @return \DOMDocument
     */
    public function toDomDocument()
    {
        return $this->dom;
    }

    /**
     * Echo the WSDL as XML
     *
     * @return bool
     */
    public function dump($filename = false)
    {

        if (!$filename) {
            echo $this->toXML();
            return true;
        }

        $i = file_put_contents($filename, $this->toXML());

        return $i > 0;
    }

    /**
     * Returns an XSD Type for the given PHP type
     *
     * @param string $type PHP Type to get the XSD type for
     * @return string
     */
    public function getType($type)
    {
        switch (strtolower($type)) {
            case 'string':
            case 'str':
                return 'xsd:string';
            case 'long':
                return 'xsd:long';
            case 'int':
            case 'integer':
                return 'xsd:int';
            case 'float':
                return 'xsd:float';
            case 'double':
                return 'xsd:double';
            case 'boolean':
            case 'bool':
                return 'xsd:boolean';
            case 'array':
                return 'soap-enc:Array';
            case 'object':
                return 'xsd:struct';
            case 'mixed':
                return 'xsd:anyType';
            case 'void':
                return '';
            default:
                // delegate retrieval of complex type to current strategy
                return $this->addComplexType($type);
            }
    }

    /**
     * This function makes sure a complex types section and schema additions are set.
     *
     * @return \Zend\Soap\Wsdl
     */
    public function addSchemaTypeSection()
    {
        if ($this->schema === null) {

            $types = $this->dom->createElementNS(Wsdl::NS_WSDL, 'types');
            $this->wsdl->appendChild($types);

            $this->schema = $this->dom->createElementNS(WSDL::NS_SCHEMA, 'schema');
            $types->appendChild($this->schema);

            $this->schema->setAttribute('targetNamespace', $this->getUri());
        }

        return $this;
    }

    /**
     * Translate PHP type into WSDL QName
     *
     * @param string $type
     * @return string QName
     */
    public function translateType($type)
    {
        if (isset($this->classMap[$type])) {
            return $this->classMap[$type];
        }

        $type = trim($type,'\\');

        // remove namespace,
        $pos = strrpos($type, '\\');
        if ($pos) {
            $type = substr($type, $pos+1);
        }

        return $type;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_types types} data type definition
     *
     * @param string $type Name of the class to be specified
     * @return string XSD Type for the given PHP type
     */
    public function addComplexType($type)
    {
        if (isset($this->includedTypes[$type])) {
            return $this->includedTypes[$type];
        }
        $this->addSchemaTypeSection();

        $strategy = $this->getComplexTypeStrategy();
        $strategy->setContext($this);
        // delegates the detection of a complex type to the current strategy
        return $strategy->addComplexType($type);
    }

    /**
     * Parse an xsd:element represented as an array into a DOMElement.
     *
     * @param array $element an xsd:element represented as an array
     * @throws Exception\RuntimeException if $element is not an array
     * @return DOMElement parsed element
     */
    private function _parseElement($element)
    {
        if (!is_array($element)) {
            throw new Exception\RuntimeException("The 'element' parameter needs to be an associative array.");
        }

        $elementXML = $this->dom->createElementNS(Wsdl::NS_SCHEMA, 'element');
        foreach ($element as $key => $value) {
            if (in_array($key, array('sequence', 'all', 'choice'))) {
                if (is_array($value)) {
                    $complexType = $this->dom->createElementNS(Wsdl::NS_SCHEMA, 'complexType');
                    if (count($value) > 0) {
                        $container = $this->dom->createElementNS(Wsdl::NS_SCHEMA, $key);
                        foreach ($value as $subElement) {
                            $subElementXML = $this->_parseElement($subElement);
                            $container->appendChild($subElementXML);
                        }
                        $complexType->appendChild($container);
                    }
                    $elementXML->appendChild($complexType);
                }
            } else {
                $elementXML->setAttribute($key, $value);
            }
        }
        return $elementXML;
    }

    /**
     * Add an xsd:element represented as an array to the schema.
     *
     * Array keys represent attribute names and values their respective value.
     * The 'sequence', 'all' and 'choice' keys must have an array of elements as their value,
     * to add them to a nested complexType.
     *
     * Example: array( 'name' => 'MyElement',
     *                 'sequence' => array( array('name' => 'myString', 'type' => 'string'),
     *                                      array('name' => 'myInteger', 'type' => 'int') ) );
     * Resulting XML: <xsd:element name="MyElement"><xsd:complexType><xsd:sequence>
     *                  <xsd:element name="myString" type="string"/>
     *                  <xsd:element name="myInteger" type="int"/>
     *                </xsd:sequence></xsd:complexType></xsd:element>
     *
     * @param array $element an xsd:element represented as an array
     * @return string xsd:element for the given element array
     */
    public function addElement($element)
    {
        $schema = $this->getSchema();
        $elementXml = $this->_parseElement($element);
        $schema->appendChild($elementXml);
        return 'tns:' . $element['name'];
    }
}
