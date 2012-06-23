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

namespace Zend\Search\Lucene\Search\Weight;
use Zend\Search\Lucene\Search\Query;
use Zend\Search\Lucene;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Boolean extends AbstractWeight
{
    /**
     * IndexReader.
     *
     * @var \Zend\Search\Lucene\SearchIndexInterface
     */
    private $_reader;

    /**
     * The query that this concerns.
     *
     * @var \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    private $_query;

    /**
     * Queries weights
     * Array of Zend_Search_Lucene_Search_Weight
     *
     * @var array
     */
    private $_weights;


    /**
     * Zend_Search_Lucene_Search_Weight_Boolean constructor
     * query - the query that this concerns.
     * reader - index reader
     *
     * @param \Zend\Search\Lucene\Search\Query\AbstractQuery $query
     * @param \Zend\Search\Lucene\SearchIndexInterface    $reader
     */
    public function __construct(Query\AbstractQuery $query, Lucene\SearchIndexInterface    $reader)
    {
        $this->_query   = $query;
        $this->_reader  = $reader;
        $this->_weights = array();

        $signs = $query->getSigns();

        foreach ($query->getSubqueries() as $num => $subquery) {
            if ($signs === null || $signs[$num] === null || $signs[$num]) {
                $this->_weights[$num] = $subquery->createWeight($reader);
            }
        }
    }


    /**
     * The weight for this query
     * Standard Weight::$_value is not used for boolean queries
     *
     * @return float
     */
    public function getValue()
    {
        return $this->_query->getBoost();
    }


    /**
     * The sum of squared weights of contained query clauses.
     *
     * @return float
     */
    public function sumOfSquaredWeights()
    {
        $sum = 0;
        foreach ($this->_weights as $weight) {
            // sum sub weights
            $sum += $weight->sumOfSquaredWeights();
        }

        // boost each sub-weight
        $sum *= $this->_query->getBoost() * $this->_query->getBoost();

        // check for empty query (like '-something -another')
        if ($sum == 0) {
            $sum = 1.0;
        }
        return $sum;
    }


    /**
     * Assigns the query normalization factor to this.
     *
     * @param float $queryNorm
     */
    public function normalize($queryNorm)
    {
        // incorporate boost
        $queryNorm *= $this->_query->getBoost();

        foreach ($this->_weights as $weight) {
            $weight->normalize($queryNorm);
        }
    }
}
