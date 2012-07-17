<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Search
 */

namespace Zend\Search\Lucene\Search\QueryEntry;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 */
class Subquery extends AbstractQueryEntry
{
    /**
     * Query
     *
     * @var \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    private $_query;

    /**
     * Object constractor
     *
     * @param \Zend\Search\Lucene\Search\Query\AbstractQuery $query
     */
    public function __construct(\Zend\Search\Lucene\Search\Query\AbstractQuery $query)
    {
        $this->_query = $query;
    }

    /**
     * Process modifier ('~')
     *
     * @param mixed $parameter
     * @throws \Zend\Search\Lucene\Search\Exception\QueryParserException
     */
    public function processFuzzyProximityModifier($parameter = null)
    {
        throw new \Zend\Search\Lucene\Search\Exception\QueryParserException(
            '\'~\' sign must follow term or phrase'
        );
    }


    /**
     * Transform entry to a subquery
     *
     * @param string $encoding
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    public function getQuery($encoding)
    {
        $this->_query->setBoost($this->_boost);

        return $this->_query;
    }
}
