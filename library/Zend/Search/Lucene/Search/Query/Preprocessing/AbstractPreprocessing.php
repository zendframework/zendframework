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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Search\Lucene\Search\Query\Preprocessing;
use Zend\Search\Lucene;
use Zend\Search\Lucene\Search\Query;

/**
 * It's an internal abstract class intended to finalize ase a query processing after query parsing.
 * This type of query is not actually involved into query execution.
 *
 * @uses       \Zend\Search\Lucene\Exception
 * @uses       \Zend\Search\Lucene\Search\Query\AbstractQuery
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * @param \Zend\Search\Lucene\SearchIndex $index
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    public function optimize(Lucene\SearchIndex $index)
    {
        throw new Lucene\Exception('This query is not intended to be executed.');
    }

    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param \Zend\Search\Lucene\SearchIndex $reader
     * @return \Zend\Search\Lucene\Search\Weight\Weight
     */
    public function createWeight(Lucene\SearchIndex $reader)
    {
        throw new Lucene\Exception('This query is not intended to be executed.');
    }

    /**
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * @param \Zend\Search\Lucene\SearchIndex $reader
     * @param \Zend\Search\Lucene\Index\DocsFilter|null $docsFilter
     */
    public function execute(Lucene\SearchIndex $reader, $docsFilter = null)
    {
        throw new Lucene\Exception('This query is not intended to be executed.');
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
        throw new Lucene\Exception('This query is not intended to be executed.');
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param \Zend\Search\Lucene\SearchIndex $reader
     * @return float
     */
    public function score($docId, Lucene\SearchIndex $reader)
    {
        throw new Lucene\Exception('This query is not intended to be executed.');
    }

    /**
     * Return query terms
     *
     * @return array
     */
    public function getQueryTerms()
    {
        throw new Lucene\Exception('Rewrite operation has to be done before retrieving query terms.');
    }
}
