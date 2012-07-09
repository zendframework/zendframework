<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql;

/**
 *
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
     * @var string
     */
    protected $alias;

    /**
     * @param string $table
     * @param string $schema
     */
    public function __construct($table, $schema = null)
    {
        if (is_array($table)) {
            $keys = array_keys($table);
            $this->alias = array_pop($keys) ?: null;

            $table = $table[$this->alias];
        }
        $this->table = $table;
        $this->schema = $schema;
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

    /**
     * @return bool
     */
    public function hasAlias()
    {
        return ($this->alias != null);
    }

    /**
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getTableAndSchema()
    {
        return array($this->table, $this->schema, $this->alias);
    }

}
