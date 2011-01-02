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
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Text\Table;

/**
 * Row class for Zend_Text_Table
 *
 * @uses      \Zend\Text\Table\Column
 * @uses      \Zend\Text\Table\Exception
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Row
{
    /**
     * List of all columns
     *
     * @var array
     */
    protected $_columns = array();

    /**
     * Temporary stored column widths
     *
     * @var array
     */
    protected $_columnWidths = null;

    /**
     * Create a new column and append it to the row
     *
     * @param  string $content
     * @param  array  $options
     * @return \Zend\Text\Table\Row
     */
    public function createColumn($content, array $options = null)
    {
        $align    = null;
        $colSpan  = null;
        $encoding = null;

        if ($options !== null) {
            extract($options, EXTR_IF_EXISTS);
        }

        $column = new Column($content, $align, $colSpan, $encoding);

        $this->appendColumn($column);

        return $this;
    }

    /**
     * Append a column to the row
     *
     * @param  \Zend\Text\Table\Column $column The column to append to the row
     * @return \Zend\Text\Table\Row
     */
    public function appendColumn(Column $column)
    {
        $this->_columns[] = $column;

        return $this;
    }

    /**
     * Get a column by it's index
     *
     * Returns null, when the index is out of range
     *
     * @param  integer $index
     * @return \Zend\Text\Table\Column|null
     */
    public function getColumn($index)
    {
        if (!isset($this->_columns[$index])) {
            return null;
        }

        return $this->_columns[$index];
    }

    /**
     * Get all columns of the row
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    /**
     * Get the widths of all columns, which were rendered last
     *
     * @throws \Zend\Text\Table\Exception\UnexpectedValueException When no columns were rendered yet
     * @return integer
     */
    public function getColumnWidths()
    {
        if ($this->_columnWidths === null) {
            throw new Exception\UnexpectedValueException('render() must be called before columnWidths can be populated');
        }

        return $this->_columnWidths;
    }

    /**
     * Render the row
     *
     * @param  array                               $columnWidths Width of all columns
     * @param  \Zend\Text\Table\Decorator $decorator    Decorator for the row borders
     * @param  integer                             $padding      Padding for the columns
     * @throws \Zend\Text\Table\Exception\OverflowException When there are too many columns
     * @return string
     */
    public function render(array $columnWidths, Decorator $decorator, $padding = 0)
    {
        // Prepare an array to store all column widths
        $this->_columnWidths = array();

        // If there is no single column, create a column which spans over the
        // entire row
        if (count($this->_columns) === 0) {
            $this->appendColumn(new Column(null, null, count($columnWidths)));
        }

        // First we have to render all columns, to get the maximum height
        $renderedColumns = array();
        $maxHeight       = 0;
        $colNum          = 0;
        foreach ($this->_columns as $column) {
            // Get the colspan of the column
            $colSpan = $column->getColSpan();

            // Verify if there are enough column widths defined
            if (($colNum + $colSpan) > count($columnWidths)) {
                throw new Exception\OverflowException('Too many columns');
            }

            // Calculate the column width
            $columnWidth = ($colSpan - 1 + array_sum(array_slice($columnWidths,
                                                                 $colNum,
                                                                 $colSpan)));

            // Render the column and split it's lines into an array
            $result = explode("\n", $column->render($columnWidth, $padding));

            // Store the width of the rendered column
            $this->_columnWidths[] = $columnWidth;

            // Store the rendered column and calculate the new max height
            $renderedColumns[] = $result;
            $maxHeight         = max($maxHeight, count($result));

            // Set up the internal column number
            $colNum += $colSpan;
        }

        // If the row doesnt contain enough columns to fill the entire row, fill
        // it with an empty column
        if ($colNum < count($columnWidths)) {
            $remainingWidth = (count($columnWidths) - $colNum - 1) +
                               array_sum(array_slice($columnWidths,
                                                     $colNum));
            $renderedColumns[] = array(str_repeat(' ', $remainingWidth));

            $this->_columnWidths[] = $remainingWidth;
        }

        // Add each single column line to the result
        $result = '';
        for ($line = 0; $line < $maxHeight; $line++) {
            $result .= $decorator->getVertical();

            foreach ($renderedColumns as $renderedColumn) {
                if (isset($renderedColumn[$line]) === true) {
                    $result .= $renderedColumn[$line];
                } else {
                    $result .= str_repeat(' ', strlen($renderedColumn[0]));
                }

                $result .= $decorator->getVertical();
            }

            $result .= "\n";
        }

        return $result;
    }
}
