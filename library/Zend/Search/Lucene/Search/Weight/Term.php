<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Search
 */

namespace Zend\Search\Lucene\Search\Weight;

use Zend\Search\Lucene;
use Zend\Search\Lucene\Index;
use Zend\Search\Lucene\Search\Query;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 */
class Term extends AbstractWeight
{
    /**
     * IndexReader.
     *
     * @var \Zend\Search\Lucene\SearchIndexInterface
     */
    private $_reader;

    /**
     * Term
     *
     * @var \Zend\Search\Lucene\Index\Term
     */
    private $_term;

    /**
     * The query that this concerns.
     *
     * @var \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    private $_query;

    /**
     * Score factor
     *
     * @var float
     */
    private $_idf;

    /**
     * Query weight
     *
     * @var float
     */
    private $_queryWeight;


    /**
     * Zend_Search_Lucene_Search_Weight_Term constructor
     * reader - index reader
     *
     * @param \Zend\Search\Lucene\Index\Term                 $term
     * @param \Zend\Search\Lucene\Search\Query\AbstractQuery $query
     * @param \Zend\Search\Lucene\SearchIndexInterface             $reader
     */
    public function __construct(Index\Term            $term,
                                Query\AbstractQuery   $query,
                                Lucene\SearchIndexInterface $reader)
    {
        $this->_term   = $term;
        $this->_query  = $query;
        $this->_reader = $reader;
    }


    /**
     * The sum of squared weights of contained query clauses.
     *
     * @return float
     */
    public function sumOfSquaredWeights()
    {
        // compute idf
        $this->_idf = $this->_reader->getSimilarity()->idf($this->_term, $this->_reader);

        // compute query weight
        $this->_queryWeight = $this->_idf * $this->_query->getBoost();

        // square it
        return $this->_queryWeight * $this->_queryWeight;
    }


    /**
     * Assigns the query normalization factor to this.
     *
     * @param float $queryNorm
     */
    public function normalize($queryNorm)
    {
        $this->_queryNorm = $queryNorm;

        // normalize query weight
        $this->_queryWeight *= $queryNorm;

        // idf for documents
        $this->_value = $this->_queryWeight * $this->_idf;
    }
}
