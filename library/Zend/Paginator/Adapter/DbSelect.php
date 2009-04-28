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
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Paginator_Adapter_Interface
 */
require_once 'Zend/Paginator/Adapter/Interface.php';

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * @see Zend_Db_Select
 */
require_once 'Zend/Db/Select.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Paginator_Adapter_DbSelect implements Zend_Paginator_Adapter_Interface
{
    /**
     * Name of the row count column
     *
     * @var string
     */
    const ROW_COUNT_COLUMN = 'zend_paginator_row_count';

    /**
     * Database query
     *
     * @var Zend_Db_Select
     */
    protected $_select = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $_rowCount = null;

    /**
     * Constructor.
     *
     * @param Zend_Db_Select $select The select query
     */
    public function __construct(Zend_Db_Select $select)
    {
        $this->_select = $select;
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
     * @param  Zend_Db_Select|integer $totalRowCount Total row count integer
     *                                               or query
     * @return Zend_Paginator_Adapter_DbSelect $this
     * @throws Zend_Paginator_Exception
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof Zend_Db_Select) {
            $columns = $rowCount->getPart(Zend_Db_Select::COLUMNS);

            $countColumnPart = $columns[0][1];

            if ($countColumnPart instanceof Zend_Db_Expr) {
                $countColumnPart = $countColumnPart->__toString();
            }

            // The select query can contain only one column, which should be the row count column
            if (false === strpos($countColumnPart, self::ROW_COUNT_COLUMN)) {
                /**
                 * @see Zend_Paginator_Exception
                 */
                require_once 'Zend/Paginator/Exception.php';

                throw new Zend_Paginator_Exception('Row count column not found');
            }

            $result = $rowCount->query(Zend_Db::FETCH_ASSOC)->fetch();

            $this->_rowCount = count($result) > 0 ? $result[self::ROW_COUNT_COLUMN] : 0;
        } else if (is_integer($rowCount)) {
            $this->_rowCount = $rowCount;
        } else {
            /**
             * @see Zend_Paginator_Exception
             */
            require_once 'Zend/Paginator/Exception.php';

            throw new Zend_Paginator_Exception('Invalid row count');
        }

        return $this;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_select->limit($itemCountPerPage, $offset);

        return $this->_select->query()->fetchAll();
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->_rowCount === null) {
            $rowCount = clone $this->_select;
            $db = $rowCount->getAdapter();

            /**
             * The DISTINCT and GROUP BY queries only work when selecting one column.
             * The question is whether any RDBMS supports DISTINCT for multiple columns, without workarounds.
             */
            if (true === $rowCount->getPart(Zend_Db_Select::DISTINCT)) {
                $columnParts = $rowCount->getPart(Zend_Db_Select::COLUMNS);

                $columns = array();

                foreach ($columnParts as $part) {
                    if ($part[1] == Zend_Db_Select::SQL_WILDCARD || $part[1] instanceof Zend_Db_Expr) {
                        $columns[] = $part[1];
                    } else {
                        $column = $db->quoteIdentifier($part[1], true);

                        if (!empty($part[0])) {
                            $column = $db->quoteIdentifier($part[0], true) . '.' . $column;
                        }

                        $columns[] = $column;
                    }
                }

                if (count($columns) == 1 && $columns[0] == Zend_Db_Select::SQL_WILDCARD) {
                    $groupPart = null;
                } else {
                    $groupPart = implode(',', $columns);
                }
            } else {
                $groupParts = $rowCount->getPart(Zend_Db_Select::GROUP);

                foreach ($groupParts as &$part) {
                    if (!($part == Zend_Db_Select::SQL_WILDCARD || $part instanceof Zend_Db_Expr)) {
                        $part = $db->quoteIdentifier($part, true);
                    }
                }

                $groupPart = implode(',', $groupParts);
            }

            $countPart  = empty($groupPart) ? 'COUNT(*)' : 'COUNT(DISTINCT ' . $groupPart . ')';
            $expression = new Zend_Db_Expr($countPart . ' AS ' . $db->quoteIdentifier(self::ROW_COUNT_COLUMN));

            $rowCount->__toString(); // Workaround for ZF-3719 and related
            $rowCount->reset(Zend_Db_Select::COLUMNS)
                     ->reset(Zend_Db_Select::ORDER)
                     ->reset(Zend_Db_Select::LIMIT_OFFSET)
                     ->reset(Zend_Db_Select::GROUP)
                     ->reset(Zend_Db_Select::DISTINCT)
                     ->columns($expression);

            $this->setRowCount($rowCount);
        }

        return $this->_rowCount;
    }
}
