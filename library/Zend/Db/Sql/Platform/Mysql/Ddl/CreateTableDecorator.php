<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param  null|PlatformInterface $platform
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        // localize variables
        foreach (get_object_vars($this->createTable) as $name => $value) {
            $this->{$name} = $value;
        }
        return parent::getSqlString($platform);
    }

    protected function normalizeColumnOption($name)
    {
        return strtolower(str_replace(array('-', '_', ' '), '', $name));
    }

    protected function compareColumnOptions($a, $b)
    {
        $a = $this->normalizeColumnOption($a);
        $a = isset($this->columnOptionSortOrder[$a])
            ? $this->columnOptionSortOrder[$a] : count($this->columnOptionSortOrder);

        $b = $this->normalizeColumnOption($b);
        $b = isset($this->columnOptionSortOrder[$b])
            ? $this->columnOptionSortOrder[$b] : count($this->columnOptionSortOrder);

        return $a - $b;
    }

    /**
     * @param string $sql
     * @return array
     */
    protected function getSqlInsertOffsets($sql)
    {
        $sqlLength = strlen($sql);
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

        for ($i = 0; $i < 3; ++$i) {
            if (!isset($insertStart[$i])) {
                $insertStart[$i] = $sqlLength;
            }
        }

        return $insertStart;
    }

    protected function processColumns(PlatformInterface $platform = null)
    {
        if (!$this->columns) {
            return null;
        }
        $sqls = array();
        foreach ($this->columns as $i => $column) {
            $sql         = $this->processExpression($column, $platform);
            $insertStart = $this->getSqlInsertOffsets($sql);

            foreach ($column->getOptions() as $coName => $coValue) {
                $insert = '';
                if (!$coValue) {
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
}
