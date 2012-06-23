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
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Search\Lucene\Search\Query;

use Zend\Search\Lucene,
    Zend\Search\Lucene\Search\Highlighter\HighlighterInterface as Highlighter,
    Zend\Search\Lucene\Search\Highlighter\DefaultHighlighter,
    Zend\Search\Lucene\Document;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractQuery
{
    /**
     * query boost factor
     *
     * @var float
     */
    private $_boost = 1;

    /**
     * AbstractQuery weight
     *
     * @var \Zend\Search\Lucene\Search\Weight\AbstractWeight
     */
    protected $_weight = null;

    /**
     * Gets the boost for this clause.  Documents matching
     * this clause will (in addition to the normal weightings) have their score
     * multiplied by boost.   The boost is 1.0 by default.
     *
     * @return float
     */
    public function getBoost()
    {
        return $this->_boost;
    }

    /**
     * Sets the boost for this query clause to $boost.
     *
     * @param float $boost
     */
    public function setBoost($boost)
    {
        $this->_boost = $boost;
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @return float
     */
    abstract public function score($docId, Lucene\SearchIndexInterface $reader);

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @return array
     */
    abstract public function matchedDocs();

    /**
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * AbstractQuery specific implementation
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @param \Zend\Search\Lucene\Index\DocsFilter|null $docsFilter
     */
    abstract public function execute(Lucene\SearchIndexInterface $reader, $docsFilter = null);

    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @return \Zend\Search\Lucene\Search\Weight\AbstractWeight
     */
    abstract public function createWeight(Lucene\SearchIndexInterface $reader);

    /**
     * Constructs an initializes a Weight for a _top-level_query_.
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     */
    protected function _initWeight(Lucene\SearchIndexInterface $reader)
    {
        // Check, that it's a top-level query and query weight is not initialized yet.
        if ($this->_weight !== null) {
            return $this->_weight;
        }

        $this->createWeight($reader);
        $sum = $this->_weight->sumOfSquaredWeights();
        $queryNorm = $reader->getSimilarity()->queryNorm($sum);
        $this->_weight->normalize($queryNorm);
    }

    /**
     * Re-write query into primitive queries in the context of specified index
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $index
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    abstract public function rewrite(Lucene\SearchIndexInterface $index);

    /**
     * Optimize query in the context of specified index
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $index
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    abstract public function optimize(Lucene\SearchIndexInterface $index);

    /**
     * Reset query, so it can be reused within other queries or
     * with other indeces
     */
    public function reset()
    {
        $this->_weight = null;
    }


    /**
     * Print a query
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Return query terms
     *
     * @return array
     */
    abstract public function getQueryTerms();

    /**
     * AbstractQuery specific matches highlighting
     *
     * @param Highlighter $highlighter  Highlighter object (also contains doc for highlighting)
     */
    abstract protected function _highlightMatches(Highlighter $highlighter);

    /**
     * Highlight matches in $inputHTML
     *
     * @param string $inputHTML
     * @param string  $defaultEncoding   HTML encoding, is used if it's not specified using Content-type HTTP-EQUIV meta tag.
     * @param Highlighter|null $highlighter
     * @return string
     */
    public function highlightMatches($inputHTML, $defaultEncoding = '', $highlighter = null)
    {
        if ($highlighter === null) {
            $highlighter = new DefaultHighlighter();
        }

        $doc = Document\HTML::loadHTML($inputHTML, false, $defaultEncoding);
        $highlighter->setDocument($doc);

        $this->_highlightMatches($highlighter);

        return $doc->getHTML();
    }

    /**
     * Highlight matches in $inputHTMLFragment and return it (without HTML header and body tag)
     *
     * @param string $inputHTMLFragment
     * @param string  $encoding   Input HTML string encoding
     * @param Highlighter|null $highlighter
     * @return string
     */
    public function htmlFragmentHighlightMatches($inputHTMLFragment, $encoding = 'UTF-8', $highlighter = null)
    {
        if ($highlighter === null) {
            $highlighter = new DefaultHighlighter();
        }

        $inputHTML = '<html><head><META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=UTF-8"/></head><body>'
                   . iconv($encoding, 'UTF-8//IGNORE', $inputHTMLFragment) . '</body></html>';

        $doc = Document\HTML::loadHTML($inputHTML);
        $highlighter->setDocument($doc);

        $this->_highlightMatches($highlighter);

        return $doc->getHTMLBody();
    }
}
