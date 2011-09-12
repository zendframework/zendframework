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
 * @package    Zend_Dom
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Dom;

/**
 * Query DOM structures based on CSS selectors and/or XPath
 *
 * @uses       Zend\Dom\Exception
 * @uses       Zend\Dom\Css2Xpath
 * @uses       Zend\Dom\NodeList
 * @package    Zend_Dom
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Query
{
    /**#@+
     * Document types
     */
    const DOC_XML   = 'docXml';
    const DOC_HTML  = 'docHtml';
    const DOC_XHTML = 'docXhtml';
    /**#@-*/

    /**
     * @var string
     */
    protected $_document;

    /**
     * DOMDocument errors, if any
     * @var false|array
     */
    protected $_documentErrors = false;

    /**
     * Document type
     * @var string
     */
    protected $_docType;

    /**
     * XPath namespaces
     * @var array
     */
    protected $_xpathNamespaces = array();

    /**
     * Constructor
     *
     * @param  null|string $document
     * @return void
     */
    public function __construct($document = null)
    {
        $this->setDocument($document);
    }

    /**
     * Set document to query
     *
     * @param  string $document
     * @return \Zend\Dom\Query
     */
    public function setDocument($document)
    {
        if (0 === strlen($document)) {
            return $this;
        }
        // breaking XML declaration to make syntax highlighting work
        if ('<' . '?xml' == substr(trim($document), 0, 5)) {
            return $this->setDocumentXml($document);
        }
        if (strstr($document, 'DTD XHTML')) {
            return $this->setDocumentXhtml($document);
        }
        return $this->setDocumentHtml($document);
    }

    /**
     * Register HTML document
     *
     * @param  string $document
     * @return \Zend\Dom\Query
     */
    public function setDocumentHtml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_HTML;
        return $this;
    }

    /**
     * Register XHTML document
     *
     * @param  string $document
     * @return \Zend\Dom\Query
     */
    public function setDocumentXhtml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_XHTML;
        return $this;
    }

    /**
     * Register XML document
     *
     * @param  string $document
     * @return \Zend\Dom\Query
     */
    public function setDocumentXml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_XML;
        return $this;
    }

    /**
     * Retrieve current document
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->_document;
    }

    /**
     * Get document type
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->_docType;
    }

    /**
     * Get any DOMDocument errors found
     *
     * @return false|array
     */
    public function getDocumentErrors()
    {
        return $this->_documentErrors;
    }

    /**
     * Perform a CSS selector query
     *
     * @param  string $query
     * @return \Zend\Dom\NodeList
     */
    public function execute($query)
    {
        $xpathQuery = Css2Xpath::transform($query);
        return $this->queryXpath($xpathQuery, $query);
    }

    /**
     * Perform an XPath query
     *
     * @param  string|array $xpathQuery
     * @param  string|null  $query      CSS selector query
     * @return \Zend\Dom\NodeList
     */
    public function queryXpath($xpathQuery, $query = null)
    {
        if (null === ($document = $this->getDocument())) {
            throw new Exception\RuntimeException('Cannot query; no document registered');
        }

        libxml_use_internal_errors(true);
        $domDoc = new \DOMDocument;
        $type   = $this->getDocumentType();
        switch ($type) {
            case self::DOC_XML:
                $success = $domDoc->loadXML($document);
                break;
            case self::DOC_HTML:
            case self::DOC_XHTML:
            default:
                $success = $domDoc->loadHTML($document);
                break;
        }
        $errors = libxml_get_errors();
        if (!empty($errors)) {
            $this->_documentErrors = $errors;
            libxml_clear_errors();
        }
        libxml_use_internal_errors(false);

        if (!$success) {
            throw new Exception\RuntimeException(sprintf('Error parsing document (type == %s)', $type));
        }

        $nodeList   = $this->_getNodeList($domDoc, $xpathQuery);
        return new NodeList($query, $xpathQuery, $domDoc, $nodeList);
    }

    /**
     * Register XPath namespaces
     *
     * @param   array $xpathNamespaces
     * @return  void
     */
    public function registerXpathNamespaces($xpathNamespaces)
    {
        $this->_xpathNamespaces = $xpathNamespaces;
    }

    /**
     * Prepare node list
     *
     * @param  DOMDocument $document
     * @param  string|array $xpathQuery
     * @return array
     */
    protected function _getNodeList($document, $xpathQuery)
    {
        $xpath      = new \DOMXPath($document);
        foreach ($this->_xpathNamespaces as $prefix => $namespaceUri) {
            $xpath->registerNamespace($prefix, $namespaceUri);
        }
        $xpathQuery = (string) $xpathQuery;
        return $xpath->query($xpathQuery);
    }
}