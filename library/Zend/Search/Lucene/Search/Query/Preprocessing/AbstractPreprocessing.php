<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Search
 */

namespace Zend\Search\Lucene\Search\Query\Preprocessing;

use Zend\Search\Lucene;
use Zend\Search\Lucene\Exception\UnsupportedMethodCallException;
use Zend\Search\Lucene\Search\Query;

/**
 * It's an internal abstract class intended to finalize ase a query processing after query parsing.
 * This type of query is not actually involved into query execution.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @internal
 */
abstract class AbstractPreprocessing extends Query\AbstractQuery
{
    /**
     * Matched terms.
     *
     * Matched terms list.
     * It's filled during rewrite operation and may be used for search result highlighting
     *
     * Array of Zend_Search_Lucene_Index_Term objects
     *
     * @var array
     */
    protected $_matches = null;

    /**
     * Optimize query in the context of specified index
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $index
     * @throws \Zend\Search\Lucene\Exception\UnsupportedMethodCallException
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    public function optimize(Lucene\SearchIndexInterface $index)
    {
        throw new UnsupportedMethodCallException('This query is not intended to be executed.');
    }

    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @throws \Zend\Search\Lucene\Exception\UnsupportedMethodCallException
     */
    public function createWeight(Lucene\SearchIndexInterface $reader)
    {
        throw new UnsupportedMethodCallException('This query is not intended to be executed.');
    }

    /**
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @param \Zend\Search\Lucene\Index\DocsFilter|null $docsFilter
     * @throws \Zend\Search\Lucene\Exception\UnsupportedMethodCallException
     */
    public function execute(Lucene\SearchIndexInterface $reader, $docsFilter = null)
    {
        throw new UnsupportedMethodCallException('This query is not intended to be executed.');
    }

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @throws \Zend\Search\Lucene\Exception\UnsupportedMethodCallException
     * @return array
     */
    public function matchedDocs()
    {
        throw new UnsupportedMethodCallException('This query is not intended to be executed.');
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @throws \Zend\Search\Lucene\Exception\UnsupportedMethodCallException
     * @return float
     */
    public function score($docId, Lucene\SearchIndexInterface $reader)
    {
        throw new UnsupportedMethodCallException('This query is not intended to be executed.');
    }

    /**
     * Return query terms
     *
     * @throws \Zend\Search\Lucene\Exception\UnsupportedMethodCallException
     * @return array
     */
    public function getQueryTerms()
    {
        throw new UnsupportedMethodCallException('Rewrite operation has to be done before retrieving query terms.');
    }
}
