<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dom
 */

namespace Zend\Dom;

use Countable;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Iterator;

/**
 * Nodelist for DOM XPath query
 *
 * @package    Zend_Dom
 * @subpackage Query
 */
class NodeList implements Iterator, Countable
{
    /**
     * Number of results
     * @var int
     */
    protected $count;

    /**
     * CSS Selector query
     * @var string
     */
    protected $cssQuery;

    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var DOMNodeList
     */
    protected $nodeList;

    /**
     * Current iterator position
     * @var int
     */
    protected $position = 0;

    /**
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * XPath query
     * @var string
     */
    protected $xpathQuery;

    /**
     * Constructor
     *
     * @param  string $cssQuery
     * @param  string|array $xpathQuery
     * @param  DOMDocument $document
     * @param  DOMNodeList $nodeList
     * @return void
     */
    public function  __construct($cssQuery, $xpathQuery, DOMDocument $document, DOMNodeList $nodeList)
    {
        $this->cssQuery   = $cssQuery;
        $this->xpathQuery = $xpathQuery;
        $this->document   = $document;
        $this->nodeList   = $nodeList;
    }

    /**
     * Retrieve CSS Query
     *
     * @return string
     */
    public function getCssQuery()
    {
        return $this->cssQuery;
    }

    /**
     * Retrieve XPath query
     *
     * @return string
     */
    public function getXpathQuery()
    {
        return $this->xpathQuery;
    }

    /**
     * Retrieve DOMDocument
     *
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Iterator: rewind to first element
     *
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
        return $this->nodeList->item(0);
    }

    /**
     * Iterator: is current position valid?
     *
     * @return bool
     */
    public function valid()
    {
        if (in_array($this->position, range(0, $this->nodeList->length - 1)) && $this->nodeList->length > 0) {
            return true;
        }
        return false;
    }

    /**
     * Iterator: return current element
     *
     * @return DOMElement
     */
    public function current()
    {
        return $this->nodeList->item($this->position);
    }

    /**
     * Iterator: return key of current element
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Iterator: move to next element
     *
     * @return void
     */
    public function next()
    {
        ++$this->position;
        return $this->nodeList->item($this->position);
    }

    /**
     * Countable: get count
     *
     * @return int
     */
    public function count()
    {
        return $this->nodeList->length;
    }
}
