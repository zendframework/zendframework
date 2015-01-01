<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Platform\Mysql\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;

class CreateTableDecorator extends CreateTable implements PlatformDecoratorInterface
{
    /**
     * @var CreateTable
     */
    protected $subject;

    /**
     * @var int[]
     */
    protected $columnOptionSortOrder = array(
        'unsigned'      => 0,
        'zerofill'      => 1,
        'identity'      => 2,
        'serial'        => 2,
        'autoincrement' => 2,
        'comment'       => 3,
        'columnformat'  => 4,
        'format'        => 4,
        'storage'       => 5,
    );

    /**
     * @param CreateTable $subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param string $sql
     * @return array
     */
    protected function getSqlInsertOffsets($sql)
    {
        $sqlLength   = strlen($sql);
        $insertStart = array();

        foreach (array('NOT NULL', 'NULL', 'DEFAULT', 'UNIQUE', 'PRIMARY', 'REFERENCES') as $needle) {
            $insertPos = strpos($sql, ' ' . $needle);

            if ($insertPos !== false) {
                switch ($needle) {
                    case 'REFERENCES':
                        $insertStart[2] = !isset($insertStart[2]) ? $insertPos : $insertStart[2];
                        // no break
                    case 'PRIMARY':
                    case 'UNIQUE':
                        $insertStart[1] = !isset($insertStart[1]) ? $insertPos : $insertStart[1];
                        // no break
                    default:
                        $insertStart[0] = !isset($insertStart[0]) ? $insertPos : $insertStart[0];
                }
            }
        }

        foreach (range(0, 3) as $i) {
            $insertStart[$i] = isset($insertStart[$i]) ? $insertStart[$i] : $sqlLength;
        }

        return $insertStart;
    }

    /**
     * {@inheritDoc}
     */
    protected function processColumns(PlatformInterface $platform = null)
    {
        if (! $this->columns) {
            return;
        }

        $sqls = array();

        foreach ($this->columns as $i => $column) {
            $sql           = $this->processExpression($column, $platform);
            $insertStart   = $this->getSqlInsertOffsets($sql);
            $columnOptions = $column->getOptions();

            uksort($columnOptions, array($this, 'compareColumnOptions'));

            foreach ($columnOptions as $coName => $coValue) {
                $insert = '';

                if (! $coValue) {
                    continue;
                }

                switch ($this->normalizeColumnOption($coName)) {
                    case 'unsigned':
                        $insert = ' UNSIGNED';
                        $j = 0;
                        break;
                    case 'zerofill':
                        $insert = ' ZEROFILL';
                        $j = 0;
                        break;
                    case 'identity':
                    case 'serial':
                    case 'autoincrement':
                        $insert = ' AUTO_INCREMENT';
                        $j = 1;
                        break;
                    case 'comment':
                        $insert = ' COMMENT ' . $platform->quoteValue($coValue);
                        $j = 2;
                        break;
                    case 'columnformat':
                    case 'format':
                        $insert = ' COLUMN_FORMAT ' . strtoupper($coValue);
                        $j = 2;
                        break;
                    case 'storage':
                        $insert = ' STORAGE ' . strtoupper($coValue);
                        $j = 2;
                        break;
                }

                if ($insert) {
                    $j = isset($j) ? $j : 0;
                    $sql = substr_replace($sql, $insert, $insertStart[$j], 0);
                    for (; $j < count($insertStart); ++$j) {
                        $insertStart[$j] += strlen($insert);
                    }
                }
            }

            $sqls[$i] = $sql;
        }

        return array($sqls);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function normalizeColumnOption($name)
    {
        return strtolower(str_replace(array('-', '_', ' '), '', $name));
    }

    /**
     * @internal @private this method is only public for PHP 5.3 compatibility purposes.
     *
     * @param string $columnA
     * @param string $columnB
     *
     * @return int
     */
    public function compareColumnOptions($columnA, $columnB)
    {
        $columnA = $this->normalizeColumnOption($columnA);
        $columnA = isset($this->columnOptionSortOrder[$columnA])
            ? $this->columnOptionSortOrder[$columnA] : count($this->columnOptionSortOrder);

        $columnB = $this->normalizeColumnOption($columnB);
        $columnB = isset($this->columnOptionSortOrder[$columnB])
            ? $this->columnOptionSortOrder[$columnB] : count($this->columnOptionSortOrder);

        return $columnA - $columnB;
    }
}
