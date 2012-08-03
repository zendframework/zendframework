<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Metadata\Display;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
class TextUi
{

    /**
     *
     * @var
     */
    protected $camelCaseFilter = null;

    /**
     * Render
     *
     * @param  \Zend\Db\Metadata\Metadata $metadata
     * @return string
     */
    public function render(\Zend\Db\Metadata\Metadata $metadata)
    {
        $output = '';
        $output .= $this->renderTables($metadata->getTables());
        return $output;
    }

    /**
     * Render tables
     *
     * @param  array $tables
     * @return string
     */
    public function renderTables(array $tables)
    {
        $output = '';
        foreach ($tables as $table) {
            $output .= $this->renderTable($table);
        }
        return $output;
    }

    /**
     * Render table
     *
     * @param  \Zend\Db\Metadata\Table $table
     * @return string
     */
    public function renderTable(\Zend\Db\Metadata\Table $table)
    {
        $output = '';
        $output .= 'The \'' . $table->getName() . "' Table\n";
        $output .= $this->renderColumns($table->getColumnCollection()) . "\n";
        $output .= $this->renderConstraints($table->getConstraintCollection()) . "\n\n";
        return $output;
    }

    /**
     * Render columns
     *
     * @param  \Zend\Db\Metadata\ColumnCollection $columnCollection
     * @return string
     */
    public function renderColumns(\Zend\Db\Metadata\ColumnCollection $columnCollection)
    {
        $columnAttributes = array(
            array('name', 'Name', 12),
            array('ordinalPosition', "Ordinal\nPosition", 10),
            array('columnDefault', "Column\nDefault", 8),
            array('isNullable', "Is\nNullable", 9),
            array('dataType', "Data\nType", 8),
            array('characterMaximumLength', "Chr. Max\nLength", 10),
            array('characterOctetLength', "Chr. Octet\nLength", 11),
            array('numericPrecision', "Num\nPrecision", 10),
            array('numericScale', "Num\nScale", 6),
            array('characterSetName', "Charset\nName", 8),
            array('collationName', "Collation\nName", 12),
            );

        $rows = $rowWidths = array();
        // make header
        foreach ($columnAttributes as $cAttrIndex => $cAttrData) {
            list($cAttrName, $cAttrDisplayName, $cAttrDefaultLength) = $cAttrData;
            $row[$cAttrIndex] = $cAttrDisplayName;
            $rowWidths[$cAttrIndex] = $cAttrDefaultLength; // default width
        }

        $rows[] = $row;

        foreach ($columnCollection as $columnMetadata) {
            $row = array();
            foreach ($columnAttributes as $cAttrIndex => $cAttrData) {
                list($cAttrName, $cAttrDisplayName, $cAttrDefaultLength) = $cAttrData;
                $value = $columnMetadata->{'get' . $cAttrName}();
                if (strlen($value) > $rowWidths[$cAttrIndex]) {
                    $rowWidths[$cAttrIndex] = strlen($value);
                }
                $row[$cAttrIndex] = (string) $value;
            }
            $rows[] = $row;
        }

        $table = new \Zend\Text\Table\Table(array(
            'columnWidths' => $rowWidths,
            'decorator' => 'ascii'
            ));
        foreach ($rows as $row) {
            $table->appendRow($row);
        }

        return 'Columns' . PHP_EOL . $table->render();
    }

    /**
     * Render constraints
     *
     * @param  \Zend\Db\Metadata\ConstraintCollection $constraints
     * @return string
     */
    public function renderConstraints(\Zend\Db\Metadata\ConstraintCollection $constraints)
    {
        $rows = array();
        foreach ($constraints as $constraint) {
            $row = array();
            $row[] = $constraint->getName();
            $row[] = $constraint->getType();
            $rows[] = $row;
        }

        $table = new \Zend\Text\Table\Table(array(
            'columnWidths' => array(25, 25),
            'decorator' => 'ascii'
            ));
        foreach ($rows as $row) {
            $table->appendRow($row);
        }

        return 'Constraints: ' . PHP_EOL . $table->render();
    }

}
