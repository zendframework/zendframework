<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Writer\Extension;

use DOMDocument;
use DOMElement;

/**
* @category Zend
* @package Zend_Feed_Writer_Entry_Rss
*/
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @var DOMDocument
     */
    protected $_dom = null;

    /**
     * @var mixed
     */
    protected $_entry = null;

    /**
     * @var DOMElement
     */
    protected $_base = null;

    /**
     * @var mixed
     */
    protected $_container = null;

    /**
     * @var string
     */
    protected $_type = null;

    /**
     * @var DOMElement
     */
    protected $_rootElement = null;

    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Set the data container
     *
     * @param  mixed $container
     * @return AbstractRenderer
     */
    public function setDataContainer($container)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Set feed encoding
     *
     * @param  string $enc
     * @return AbstractRenderer
     */
    public function setEncoding($enc)
    {
        $this->_encoding = $enc;
        return $this;
    }

    /**
     * Get feed encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Set DOMDocument and DOMElement on which to operate
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $base
     * @return AbstractRenderer
     */
    public function setDomDocument(DOMDocument $dom, DOMElement $base)
    {
        $this->_dom  = $dom;
        $this->_base = $base;
        return $this;
    }

    /**
     * Get data container being rendered
     *
     * @return mixed
     */
    public function getDataContainer()
    {
        return $this->_container;
    }

    /**
     * Set feed type
     *
     * @param  string $type
     * @return AbstractRenderer
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * Get feedtype
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set root element of document
     *
     * @param  DOMElement $root
     * @return AbstractRenderer
     */
    public function setRootElement(DOMElement $root)
    {
        $this->_rootElement = $root;
        return $this;
    }

    /**
     * Get root element
     *
     * @return DOMElement
     */
    public function getRootElement()
    {
        return $this->_rootElement;
    }

    /**
     * Append namespaces to feed
     *
     * @return void
     */
    abstract protected function _appendNamespaces();
}
