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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\Technorati;

use DomDocument,
    DOMXPath,
    OutOfBoundsException,
    SeekableIterator;

/**
 * This is the most essential result set.
 * The scope of this class is to be extended by a query-specific child result set class,
 * and it should never be used to initialize a standalone object.
 *
 * Each of the specific result sets represents a collection of query-specific
 * Result objects.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @abstract
 */
abstract class ResultSet implements SeekableIterator
{
    /**
     * The total number of results available
     *
     * @var     int
     * @access  protected
     */
    protected $totalResultsAvailable;

    /**
     * The number of results in this result set
     *
     * @var     int
     * @access  protected
     */
    protected $totalResultsReturned;

    /**
     * The offset in the total result set of this search set
     *
     * @var     int
     */
    //TODO public $firstResultPosition;


    /**
     * A DomNodeList of results
     *
     * @var     DomNodeList
     * @access  protected
     */
    protected $results;

    /**
     * Technorati API response document
     *
     * @var     DomDocument
     * @access  protected
     */
    protected $dom;

    /**
     * Object for $this->dom
     *
     * @var     DOMXpath
     * @access  protected
     */
    protected $xpath;

    /**
     * XML string representation for $this->dom
     *
     * @var     string
     * @access  protected
     */
    protected $xml;

    /**
     * Current Item
     *
     * @var     int
     * @access  protected
     */
    protected $currentIndex = 0;


    /**
     * Parses the search response and retrieves the results for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment for this object
     * @param   array $options      query options as associative array
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        $this->init($dom, $options);

        // Technorati loves to make developer's life really hard
        // I must read query options in order to normalize a single way
        // to display start and limit.
        // The value is printed out in XML using many different tag names,
        // too hard to get it from XML

        // Additionally, the following tags should be always available
        // according to API documentation but... this is not the truth!
        // - querytime
        // - limit
        // - start (sometimes rankingstart)

        // query tag is only available for some requests, the same for url.
        // For now ignore them.

        //$start = isset($options['start']) ? $options['start'] : 1;
        //$limit = isset($options['limit']) ? $options['limit'] : 20;
        //$this->firstResultPosition = $start;
    }

    /**
     * Initializes this object from a DomDocument response.
     *
     * Because __construct and __wakeup shares some common executions,
     * it's useful to group them in a single initialization method.
     * This method is called once each time a new instance is created
     * or a serialized object is unserialized.
     *
     * @param   DomDocument $dom the ReST fragment for this object
     * @param   array $options   query options as associative array
     *      * @return  void
     */
    protected function init(DomDocument $dom, $options = array())
    {
        $this->dom     = $dom;
        $this->xpath   = new DOMXPath($dom);

        $this->results = $this->xpath->query("//item");
    }

    /**
     * Number of results returned.
     *
     * @return  int     total number of results returned
     */
    public function totalResults()
    {
        return (int) $this->totalResultsReturned;
    }


    /**
     * Number of available results.
     *
     * @return  int     total number of available results
     */
    public function totalResultsAvailable()
    {
        return (int) $this->totalResultsAvailable;
    }

    /**
     * Implements SeekableIterator::current().
     *
     * @return  void
     * @throws  Zend\Service\Exception
     * @abstract
     */
    // abstract public function current();

    /**
     * Implements SeekableIterator::key().
     *
     * @return  int
     */
    public function key()
    {
        return $this->currentIndex;
    }

    /**
     * Implements SeekableIterator::next().
     *
     * @return  void
     */
    public function next()
    {
        $this->currentIndex += 1;
    }

    /**
     * Implements SeekableIterator::rewind().
     *
     * @return  bool
     */
    public function rewind()
    {
        $this->currentIndex = 0;
        return true;
    }

    /**
     * Implement SeekableIterator::seek().
     *
     * @param   int $index
     * @return  void
     * @throws  OutOfBoundsException
     */
    public function seek($index)
    {
        $indexInt = (int) $index;
        if ($indexInt >= 0 && $indexInt < $this->results->length) {
            $this->currentIndex = $indexInt;
        } else {
            throw new OutOfBoundsException("Illegal index '$index'");
        }
    }

    /**
     * Implement SeekableIterator::valid().
     *
     * @return boolean
     */
    public function valid()
    {
        return null !== $this->results && $this->currentIndex < $this->results->length;
    }

    /**
     * Returns the response document as XML string.
     *
     * @return string   the response document converted into XML format
     */
    public function getXml()
    {
        return $this->dom->saveXML();
    }

    /**
     * Overwrites standard __sleep method to make this object serializable.
     *
     * DomDocument and DOMXpath objects cannot be serialized.
     * This method converts them back to an XML string.
     *
     * @return void
     */
    public function __sleep()
    {
        $this->xml     = $this->getXml();
        $vars = array_keys(get_object_vars($this));
        return array_diff($vars, array('_dom', '_xpath'));
    }

    /**
     * Overwrites standard __wakeup method to make this object unserializable.
     *
     * Restores object status before serialization.
     * Converts XML string into a DomDocument object and creates a valid
     * DOMXpath instance for given DocDocument.
     *
     * @return void
     */
    public function __wakeup()
    {
        $dom = new DOMDocument();
        $dom->loadXml($this->xml);
        $this->init($dom);
        $this->xml = null; // reset XML content
    }
}
