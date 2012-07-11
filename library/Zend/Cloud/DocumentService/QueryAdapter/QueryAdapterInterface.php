<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\DocumentService\QueryAdapter;

/**
 * This interface describes the API that concrete query adapter should implement
 *
 * Common interface for document storage services in the cloud. This interface
 * supports most document services and provides some flexibility for
 * vendor-specific features and requirements via an optional $options array in
 * each method signature. Classes implementing this interface should implement
 * URI construction for collections and documents from the parameters given in each
 * method and the account data passed in to the constructor. Classes
 * implementing this interface are also responsible for security; access control
 * isn't currently supported in this interface, although we are considering
 * access control support in future versions of the interface. Query
 * optimization mechanisms are also not supported in this version.
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage DocumentService
 */
interface QueryAdapterInterface
{
    /**
     * SELECT clause (fields to be selected)
     *
     * @param string $select
     * @return \Zend\Cloud\DocumentService\QueryAdapter\QueryAdapterInterface
     */
    public function select($select);

    /**
     * FROM clause (table name)
     *
     * @param string $from
     * @return \Zend\Cloud\DocumentService\QueryAdapter\QueryAdapterInterface
     */
    public function from($from);

    /**
     * WHERE clause (conditions to be used)
     *
     * @param string $where
     * @param mixed $value Value or array of values to be inserted instead of ?
     * @param string $op Operation to use to join where clauses (AND/OR)
     * @return \Zend\Cloud\DocumentService\QueryAdapter\QueryAdapterInterface
     */
    public function where($where, $value = null, $op = 'and');

    /**
     * WHERE clause for item ID
     *
     * This one should be used when fetching specific rows since some adapters
     * have special syntax for primary keys
     *
     * @param mixed $value Row ID for the document
     * @return \Zend\Cloud\DocumentService\QueryAdapter\QueryAdapterInterface
     */
    public function whereId($value);

    /**
     * LIMIT clause (how many rows ot return)
     *
     * @param int $limit
     * @return \Zend\Cloud\DocumentService\QueryAdapter\QueryAdapterInterface
     */
    public function limit($limit);

    /**
     * ORDER BY clause (sorting)
     *
     * @param string $sort Column to sort by
     * @param string $direction Direction - asc/desc
     * @return \Zend\Cloud\DocumentService\QueryAdapter\QueryAdapterInterface
     */
    public function order($sort, $direction = 'asc');

    /**
     * Assemble the query into a format the adapter can utilize
     *
     * @return mixed
     */
    public function assemble();
}
