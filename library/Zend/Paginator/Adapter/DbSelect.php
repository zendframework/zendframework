<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace Zend\Paginator\Adapter;

use Zend\Db\Sql;

/**
 * @category   Zend
 * @package    Zend_Paginator
 */
class DbSelect implements AdapterInterface
{
    /**
     * Name of the row count column
     *
     * @var string
     */
    const ROW_COUNT_COLUMN = 'zend_paginator_row_count';

    /**
     * The COUNT query
     *
     * @var \Zend\Db\Sql\Select
     */
    protected $countSelect = null;

    /**
     * Database query
     *
     * @var \Zend\Db\Sql\Select
     */
    protected $select = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $rowCount = null;

    /**
     * Constructor.
     *
     * @param \Zend\Db\Sql\Select $select The select query
     */
    public function __construct(Sql\Select $select)
    {
        $this->select = $select;
    }

    /**
     * Sets the total row count, either directly or through a supplied
     * query.  Without setting this, {@link getPages()} selects the count
     * as a subquery (SELECT COUNT ... FROM (SELECT ...)).  While this
     * yields an accurate count even with queries containing clauses like
     * LIMIT, it can be slow in some circumstances.  For example, in MySQL,
     * subqueries are generally slow when using the InnoDB storage engine.
     * Users are therefore encouraged to profile their queries to find
     * the solution that best meets their needs.
     *
     * @param  \Zend\Db\Sql\Select|integer $rowCount Total row count integer
     *                                               or query
     * @throws Exception\InvalidArgumentException
     * @return DbSelect
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof Sql\Select) {
            $columns = $rowCount->getPart(Sql\Select::COLUMNS);

            $countColumnPart = $columns[0][1];

            if ($countColumnPart instanceof Sql\ExpressionInterface) {
                $countColumnPart = $countColumnPart->__toString();
            }

            $rowCountColumn = $this->select->getAdapter()->foldCase(self::ROW_COUNT_COLUMN);

            // The select query can contain only one column, which should be the row count column
            if (false === strpos($countColumnPart, $rowCountColumn)) {
                throw new Exception\InvalidArgumentException('Row count column not found');
            }

            $result = $rowCount->query(Db\Db::FETCH_ASSOC)->fetch();

            $this->rowCount = count($result) > 0 ? $result[$rowCountColumn] : 0;
        } elseif (is_integer($rowCount)) {
            $this->rowCount = $rowCount;
        } else {
            throw new Exception\InvalidArgumentException('Invalid row count');
        }

        return $this;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset           Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->select->limit($itemCountPerPage, $offset);

        return $this->select->query()->fetchAll();
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->rowCount === null) {
            $this->setRowCount(
                $this->getCountSelect()
            );
        }

        return $this->rowCount;
    }

    /**
     * Get the COUNT select object for the provided query
     *
     * TODO: Have a look at queries that have both GROUP BY and DISTINCT specified.
     * In that use-case I'm expecting problems when either GROUP BY or DISTINCT
     * has one column.
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getCountSelect()
    {
        /**
         * We only need to generate a COUNT query once. It will not change for
         * this instance.
         */
        if ($this->countSelect !== null) {
            return $this->countSelect;
        }

        $rowCount = clone $this->select;
        $rowCount->__toString(); // Workaround for ZF-3719 and related

        $db = $rowCount->getAdapter();

        $countColumn = $db->quoteIdentifier($db->foldCase(self::ROW_COUNT_COLUMN));
        $countPart   = 'COUNT(1) AS ';
        $groupPart   = null;
        $unionParts  = $rowCount->getPart(Sql\Select::UNION);

        /**
         * If we're dealing with a UNION query, execute the UNION as a subquery
         * to the COUNT query.
         */
        if (!empty($unionParts)) {
            $expression = new Sql\Expression($countPart . $countColumn);
            $rowCount   = $db
                ->select()
                ->bind($rowCount->getBind())
                ->from($rowCount, $expression);
        } else {
            $columnParts = $rowCount->getPart(Sql\Select::COLUMNS);
            $groupParts  = $rowCount->getPart(Sql\Select::GROUP);
            $havingParts = $rowCount->getPart(Sql\Select::HAVING);
            $isDistinct  = $rowCount->getPart(Sql\Select::DISTINCT);

            /**
             * If there is more than one column AND it's a DISTINCT query, more
             * than one group, or if the query has a HAVING clause, then take
             * the original query and use it as a subquery os the COUNT query.
             */
            if (($isDistinct && count($columnParts) > 1) || count($groupParts) > 1 || !empty($havingParts)) {
                $rowCount = $db->select()->from($this->select);
            } elseif ($isDistinct) {
                $part = $columnParts[0];

                if ($part[1] !== Sql\Select::SQL_WILDCARD && !($part[1] instanceof Sql\ExpressionInterface)) {
                    $column = $db->quoteIdentifier($part[1], true);

                    if (!empty($part[0])) {
                        $column = $db->quoteIdentifier($part[0], true) . '.' . $column;
                    }

                    $groupPart = $column;
                }
            } elseif (!empty($groupParts) && $groupParts[0] !== Sql\Select::SQL_WILDCARD &&
                !($groupParts[0] instanceof Sql\ExpressionInterface)
            ) {
                $groupPart = $db->quoteIdentifier($groupParts[0], true);
            }

            /**
             * If the original query had a GROUP BY or a DISTINCT part and only
             * one column was specified, create a COUNT(DISTINCT ) query instead
             * of a regular COUNT query.
             */
            if (!empty($groupPart)) {
                $countPart = 'COUNT(DISTINCT ' . $groupPart . ') AS ';
            }

            /**
             * Create the COUNT part of the query
             */
            $expression = new Sql\Expression($countPart . $countColumn);

            $rowCount->reset(Sql\Select::COLUMNS)
                ->reset(Sql\Select::ORDER)
                ->reset(Sql\Select::LIMIT_OFFSET)
                ->reset(Sql\Select::GROUP)
                ->reset(Sql\Select::DISTINCT)
                ->reset(Sql\Select::HAVING)
                ->columns($expression);
        }

        $this->countSelect = $rowCount;

        return $rowCount;
    }
}
