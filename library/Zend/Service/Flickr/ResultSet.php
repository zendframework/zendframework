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
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Flickr;

use DOMDocument;
use DOMXPath;
use SeekableIterator;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ResultSet implements SeekableIterator
{
    /**
     * Total number of available results
     *
     * @var int
     */
    public $totalResultsAvailable;

    /**
     * Number of results in this result set
     *
     * @var int
     */
    public $totalResultsReturned;

    /**
     * The offset of this result set in the total set of available results
     *
     * @var int
     */
    public $firstResultPosition;

    /**
     * Results storage
     *
     * @var \DOMNodeList
     */
    protected $results = null;

    /**
     * Reference to Flickr object with which the request was made
     *
     * @var Flickr
     */
    private $flickr;

    /**
     * Current index for the Iterator
     *
     * @var int
     */
    private $currentIndex = 0;

    /**
     * Parse the Flickr Result Set
     *
     * @param  DOMDocument $dom
     * @param  Flickr      $flickr
     */
    public function __construct(DOMDocument $dom, Flickr $flickr)
    {
        $this->flickr = $flickr;

        $xpath = new DOMXPath($dom);

        $photos = $xpath->query('//photos')->item(0);

        $page    = $photos->getAttribute('page');
        $pages   = $photos->getAttribute('pages');
        $perPage = $photos->getAttribute('perpage');
        $total   = $photos->getAttribute('total');

        $this->totalResultsReturned  = ($page == $pages || $pages == 0)
            ? ($total - ($page - 1) * $perPage)
            : (int)$perPage;
        $this->firstResultPosition   = ($page - 1) * $perPage + 1;
        $this->totalResultsAvailable = (int)$total;

        if ($total > 0) {
            $this->results = $xpath->query('//photo');
        }
    }

    /**
     * Total Number of results returned
     *
     * @return int Total number of results returned
     */
    public function totalResults()
    {
        return $this->totalResultsReturned;
    }

    /**
     * Implements SeekableIterator::current()
     *
     * @return Result
     */
    public function current()
    {
        return new Result($this->results->item($this->currentIndex), $this->flickr);
    }

    /**
     * Implements SeekableIterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->currentIndex;
    }

    /**
     * Implements SeekableIterator::next()
     *
     * @return void
     */
    public function next()
    {
        $this->currentIndex += 1;
    }

    /**
     * Implements SeekableIterator::rewind()
     *
     * @return void
     */
    public function rewind()
    {
        $this->currentIndex = 0;
    }

    /**
     * Implements SeekableIterator::seek()
     *
     * @param  int $index
     * @throws Exception\OutOfBoundsException
     * @return void
     */
    public function seek($index)
    {
        $indexInt = (int)$index;
        if ($indexInt >= 0 && (null === $this->results || $indexInt < $this->results->length)) {
            $this->currentIndex = $indexInt;
        } else {
            throw new Exception\OutOfBoundsException("Illegal index '$index'");
        }
    }

    /**
     * Implements SeekableIterator::valid()
     *
     * @return boolean
     */
    public function valid()
    {
        return null !== $this->results && $this->currentIndex < $this->results->length;
    }
}