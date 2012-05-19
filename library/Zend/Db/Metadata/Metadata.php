<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Metadata;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
class Metadata implements MetadataInterface
{
    /**
     * Adapter
     * 
     * @var Adapter 
     */
    protected $adapter = null;

    /**
     * @var MetadataInterface
     */
    protected $source = null;

    /**
     * Constructor
     * 
     * @param Adapter $adapter 
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->source = $this->createSourceFromAdapter($adapter);
    }

    /**
     * Create source from adapter
     * 
     * @param  Adapter $adapter
     * @return Source\InformationSchemaMetadata 
     */
    protected function createSourceFromAdapter(Adapter $adapter)
    {
        switch ($adapter->getPlatform()->getName()) {
            case 'MySQL':
            case 'SQLServer':
                return new Source\InformationSchemaMetadata($adapter);
            case 'SQLite':
                return new Source\SqliteMetadata($adapter);
        }

        throw new \Exception('cannot create source from adapter');
    }

    // @todo methods

    /**
     * @param null $schema
     * @param null $database
     * @return Object\TableObject[]
     */
    public function getTables($schema = null)
    {
        return $this->source->getTables();
    }

    /**
     * Get views
     * 
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getViews($schema = null)
    {
        return $this->source->getViews();
    }

    /**
     * Get triggers
     * 
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getTriggers($schema = null)
    {
        return $this->source->getTriggers();
    }

    /**
     * Get constraints
     * 
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getConstraints($table, $schema = null)
    {
        return $this->source->getConstraints($table, $schema);
    }

    /**
     * Get columns
     * 
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getColumns($table, $schema = null)
    {
        return $this->source->getColumns($table);
    }

    /**
     * Get constraint keys
     * 
     * @param  string $constraint
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getConstraintKeys($constraint, $table, $schema = null)
    {
        return $this->source->getConstraintKeys($constraint, $table);
    }

    /**
     * Get constraints
     * 
     * @param  string $constraintName
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return Object\ConstraintObject 
     */
    public function getConstraint($constraintName, $table, $schema = null)
    {
        return $this->source->getConstraint($constraintName, $table, $schema);
    }

    /**
     * Get schemas
     */
    public function getSchemas()
    {
        // TODO: Implement getSchemas() method.
    }

    /**
     * Get table names
     * 
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getTableNames($schema = null)
    {
        return $this->source->getTableNames($schema);
    }

    /**
     * Get table
     * 
     * @param  string $tableName
     * @param  string $schema
     * @param  string $database
     * @return Object\TableObject 
     */
    public function getTable($tableName, $schema = null)
    {
        return $this->source->getTable($tableName, $schema);
    }

    /**
     * Get views names
     * 
     * @param string $schema
     * @param string $database 
     */
    public function getViewNames($schema = null)
    {
        // TODO: Implement getViewNames() method.
    }

    /**
     * Get view
     * 
     * @param string $viewName
     * @param string $schema
     * @param string $database 
     */
    public function getView($viewName, $schema = null)
    {
        // TODO: Implement getView() method.
    }

    /**
     * Get trigger names
     * 
     * @param string $schema
     * @param string $database 
     */
    public function getTriggerNames($schema = null)
    {
        // TODO: Implement getTriggerNames() method.
    }

    /**
     * Get trigger
     * 
     * @param string $triggerName
     * @param string $schema
     * @param string $database 
     */
    public function getTrigger($triggerName, $schema = null)
    {
        // TODO: Implement getTrigger() method.
    }

    /**
     * Get column names
     * 
     * @param string $table
     * @param string $schema
     * @param string $database 
     */
    public function getColumnNames($table, $schema = null)
    {
        return $this->source->getColumnNames($table, $schema);
    }

    /**
     * Get column
     * 
     * @param string $columnName
     * @param string $table
     * @param string $schema
     */
    public function getColumn($columnName, $table, $schema = null)
    {
        // TODO: Implement getColumn() method.
    }

}
