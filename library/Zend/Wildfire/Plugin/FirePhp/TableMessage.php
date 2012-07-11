<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Wildfire
 */

namespace Zend\Wildfire\Plugin\FirePhp;

use Zend\Wildfire;
use Zend\Wildfire\Plugin\Exception;
use Zend\Wildfire\Plugin\FirePhp;

/**
 * A message envelope that can be updated for the duration of the requet before
 * it gets flushed at the end of the request.
 *
 * @category   Zend
 * @package    Zend_Wildfire
 * @subpackage Plugin
 */
class TableMessage extends Message
{
    /**
     * The header of the table containing all columns
     * @var array
     */
    protected $_header = null;

    /**
     * The rows of the table
     * $var array
     */
    protected $_rows = array();

    /**
     * Constructor
     *
     * @param string $label The label of the table
     */
    public function __construct($label)
    {
        parent::__construct(FirePhp::TABLE, null);
        $this->setLabel($label);
    }

    /**
     * Set the table header
     *
     * @param array $header The header columns
     * @return void
     */
    public function setHeader($header)
    {
        $this->_header = $header;
    }

    /**
     * Append a row to the end of the table.
     *
     * @param array $row An array of column values representing a row.
     * @return void
     */
    public function addRow($row)
    {
        $this->_rows[] = $row;
    }

    /**
     * Get the actual message to be sent in its final format.
     *
     * @return mixed Returns the message to be sent.
     */
    public function getMessage()
    {
        $table = $this->_rows;
        if($this->_header) {
            array_unshift($table,$this->_header);
        }
        return $table;
    }

    /**
     * Returns the row at the given index
     *
     * @param integer $index The index of the row
     * @return array Returns the row
     * @throws \Zend\Wildfire\Exception
     */
    public function getRowAt($index)
    {
        $count = $this->getRowCount();

        if($index < 0 || $index > $count-1) {
            throw new Exception\OutOfBoundsException('Row index('.$index.') out of bounds('.$count.')!');
        }

        return $this->_rows[$index];
    }

    /**
     * Sets the row on the given index to a new row
     *
     * @param integer $index The index of the row
     * @param array $row The new data for the row
     * @throws \Zend\Wildfire\Exception
     */
    public function setRowAt($index, $row)
    {
        $count = $this->getRowCount();

        if($index < 0 || $index > $count-1) {
            throw new Exception\OutOfBoundsException('Row index('.$index.') out of bounds('.$count.')!');
        }

        $this->_rows[$index] = $row;
    }

    /**
     * Returns the number of rows
     *
     * @return integer
     */
    public function getRowCount()
    {
        return count($this->_rows);
    }

    /**
     * Returns the last row of the table
     *
     * @return array Returns the last row
     * @throws \Zend\Wildfire\Exception
     */
    public function getLastRow()
    {
        $count = $this->getRowCount();

        if($count==0) {
            throw new Exception\OutOfBoundsException('Cannot get last row as no rows exist!');
        }

        return $this->_rows[$count-1];
    }
}
