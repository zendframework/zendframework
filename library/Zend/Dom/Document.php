<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Dom;

use DOMDocument;
use DOMXPath;
use Zend\Dom\Exception;
use Zend\Stdlib\ErrorHandler;

/**
 * Class used to initialize DomDocument from string, with proper verifications
 */
class Document
{
    /**#@+
     * Document types
     */
    const DOC_XML   = 'DOC_XML';
    const DOC_HTML  = 'DOC_HTML';
    const DOC_XHTML = 'DOC_XHTML';
    /**#@-*/

    /**
     * Raw document
     * @var string
     */
    protected $stringDocument;

    /**
     * DOMDocument generated from raw string document
     * @var DOMDocument
     */
    protected $domDocument;

    /**
     * Type of the document provided
     * @var string
     */
    protected $type;

    /**
     * Error list generated from transformation of document to DOMDocument
     * @var array
     */
    protected $errors = array();

    /**
     * XPath namespaces
     * @var array
     */
    protected $xpathNamespaces = array();

    /**
     * XPath PHP Functions
     * @var mixed
     */
    protected $xpathPhpFunctions;

    /**
     * Constructor
     * @param string|null  $document
     * @param string|null  $encoding
     */
    public function __construct($document = null, $encoding = null)
    {
        $this->setStringDocument($document);
        $this->setEncoding($encoding);
    }

    /**
     * Get raw document
     * @return null|string
     */
    public function getStringDocument()
    {
        return $this->stringDocument;
    }

    /**
     * Set raw document
     * @param string|null  $document
     * @param string|null  $forcedType      Type for the provided document (see constants)
     * @param string|null  $forcedEncoding  Encoding for the provided document
     * @return Document
     */
    public function setStringDocument($document, $forcedType = null, $forcedEncoding = null)
    {
        $type = static::DOC_HTML;
        if (strstr($document, 'DTD XHTML')) {
            $type = static::DOC_XHTML;
        }

        // Breaking XML declaration to make syntax highlighting work
        if ('<' . '?xml' == substr(trim($document), 0, 5)) {
            $type = static::DOC_XML;
            if (preg_match('/<html[^>]*xmlns="([^"]+)"[^>]*>/i', $document, $matches)) {
                $this->xpathNamespaces[] = $matches[1];
                $type = static::DOC_XHTML;
            }
        }

        // Unsetting previously registered DOMDocument
        $this->domDocument     = null;
        $this->stringDocument  = !empty($document) ? $document : null;

        $this->setType($forcedType ?: (!empty($document) ? $type : null));
        $this->setEncoding($forcedEncoding);

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    protected function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getDomDocument()
    {
        if (null === ($stringDocument = $this->getStringDocument())) {
            throw new Exception\RuntimeException('Cannot get DOMDocument; no document registered');
        }

        if (null === $this->domDocument) {
            $this->domDocument  = $this->getDomFromString($stringDocument);
        }

        return $this->domDocument;
    }

    protected function setDomDocument(DOMDocument $domDocument)
    {
        $this->domDocument = $domDocument;

        return $this;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this->encoding;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    protected function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    protected function getDomFromString($stringDocument)
    {
        libxml_use_internal_errors(true);
        libxml_disable_entity_loader(true);

        $encoding  = $this->getEncoding();
        $domDoc    = null === $encoding ? new DOMDocument('1.0') : new DOMDocument('1.0', $encoding);
        $type      = $this->getType();

        switch ($type) {
            case static::DOC_XML:
                $success = $domDoc->loadXML($stringDocument);
                foreach ($domDoc->childNodes as $child) {
                    if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                        throw new Exception\RuntimeException(
                            'Invalid XML: Detected use of illegal DOCTYPE'
                        );
                    }
                }
                break;
            case static::DOC_HTML:
            case static::DOC_XHTML:
            default:
                $success = $domDoc->loadHTML($stringDocument);
                break;
        }

        $errors = libxml_get_errors();
        if (!empty($errors)) {
            $this->setErrors($errors);
            libxml_clear_errors();
        }

        libxml_disable_entity_loader(false);
        libxml_use_internal_errors(false);

        if (!$success) {
            throw new Exception\RuntimeException(sprintf('Error parsing document (type == %s)', $type));
        }

        return $domDoc;
    }

    /**
     * Perform a CSS selector query
     *
     * @param  string $query
     * @return NodeList
     */
    public function queryCss($query)
    {
        $xpathQuery = Css2Xpath::transform($query);
        return $this->queryXpath($xpathQuery, $query);
    }

    /**
     * Perform an XPath query
     *
     * @param  string|array $xpathQuery
     * @param  string|null  $query      CSS selector query
     * @throws Exception\RuntimeException
     * @return NodeList
     */
    public function queryXpath($xpathQuery, $query = null)
    {
        $domDoc    = $this->getDomDocument();
        $nodeList  = $this->getNodeList($domDoc, $xpathQuery);

        return new NodeList($query, $xpathQuery, $domDoc, $nodeList);
    }

    /**
     * Register XPath namespaces
     *
     * @param  array $xpathNamespaces
     * @return void
     */
    public function registerXpathNamespaces($xpathNamespaces)
    {
        $this->xpathNamespaces = $xpathNamespaces;
    }

    /**
     * Register PHP Functions to use in internal DOMXPath
     *
     * @param  bool $xpathPhpFunctions
     * @return void
     */
    public function registerXpathPhpFunctions($xpathPhpFunctions = true)
    {
        $this->xpathPhpFunctions = $xpathPhpFunctions;
    }

    /**
     * Prepare node list
     *
     * @param  DOMDocument $document
     * @param  string|array $xpathQuery
     * @return array
     */
    protected function getNodeList($document, $xpathQuery)
    {
        $xpath = new DOMXPath($document);

        foreach ($this->xpathNamespaces as $prefix => $namespaceUri) {
            $xpath->registerNamespace($prefix, $namespaceUri);
        }

        if ($this->xpathPhpFunctions) {
            $xpath->registerNamespace('php', 'http://php.net/xpath');
            ($this->xpathPhpFunctions === true) ? $xpath->registerPhpFunctions() : $xpath->registerPhpFunctions($this->xpathPhpFunctions);
        }

        ErrorHandler::start();
        $nodeList   = $xpath->query($xpathQuery);
        ErrorHandler::stop(true);

        return $nodeList;
    }
}
