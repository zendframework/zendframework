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
    Zend\Search\Lucene\Search\Weight,
    Zend\Search\Lucene\Search\Highlighter\HighlighterInterface as Highlighter;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class EmptyResult extends AbstractQuery
{
    /**
     * Re-write query into primitive queries in the context of specified index
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $index
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    public function rewrite(Lucene\SearchIndexInterface $index)
    {
        return $this;
    }

    /**
     * Optimize query in the context of specified index
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $index
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    public function optimize(Lucene\SearchIndexInterface $index)
    {
        // "EmptyResult" query is a primitive query and don't need to be optimized
        return $this;
    }

    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @return \Zend\Search\Lucene\Search\Weight\EmptyResultWeight
     */
    public function createWeight(Lucene\SearchIndexInterface $reader)
    {
        return new Weight\EmptyResultWeight();
    }

    /**
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @param \Zend\Search\Lucene\Index\DocsFilter|null $docsFilter
     */
    public function execute(Lucene\SearchIndexInterface $reader, $docsFilter = null)
    {
        // Do nothing
    }

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @return array
     */
    public function matchedDocs()
    {
        return array();
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @return float
     */
    public function score($docId, Lucene\SearchIndexInterface $reader)
    {
        return 0;
    }

    /**
     * Return query terms
     *
     * @return array
     */
    public function getQueryTerms()
    {
        return array();
    }

    /**
     * Query specific matches highlighting
     *
     * @param Highlighter $highlighter  Highlighter object (also contains doc for highlighting)
     */
    protected function _highlightMatches(Highlighter $highlighter)
    {
        // Do nothing
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        return '<EmptyQuery>';
    }
}
