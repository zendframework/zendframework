<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

/**
 */
class TableIdentifier
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @param string $table
     * @param string $schema
     */
    public function __construct($table, $schema = null)
    {
        if (! (is_string($table) || is_callable(array($table, '__toString')))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '$table must be a valid table name, parameter of type %s given',
                is_object($table) ? get_class($table) : gettype($table)
            ));
        }

        $this->table = (string) $table;

        if (null === $schema) {
            $this->schema = null;
        } else {
            if (! (is_string($schema) || is_callable(array($schema, '__toString')))) {
                throw new Exception\InvalidArgumentException(sprintf(
                    '$schema must be a valid schema name, parameter of type %s given',
                    is_object($schema) ? get_class($schema) : gettype($schema)
                ));
            }

            $this->schema = (string) $schema;
        }
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function hasSchema()
    {
        return ($this->schema != null);
    }

    /**
     * @param $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return null|string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    public function getTableAndSchema()
    {
        return array($this->table, $this->schema);
    }
}
