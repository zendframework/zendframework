<?php

namespace Zend\Db\Metadata;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver;

class Metadata implements MetadataInterface
{

    protected $adapter = null;

    /**
     * @var MetadataInterface
     */
    protected $source = null;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->source = $this->createSourceFromAdapter($adapter);
    }

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
    public function getTables($schema = null, $database = null)
    {
        return $this->source->getTables();
    }

    public function getViews($schema = null, $database = null)
    {
        return $this->source->getViews();
    }

    public function getTriggers($schema = null, $database = null)
    {
        return $this->source->getTriggers();
    }

    public function getConstraints($table, $schema = null, $database = null)
    {
        return $this->source->getConstraints($table, $schema, $database);
    }

    public function getColumns($table, $schema = null, $database = null)
    {
        return $this->source->getColumns($table);
    }

    public function getConstraintKeys($constraint, $table, $schema = null, $database = null)
    {
        return $this->source->getConstraintKeys($constraint, $table);
    }

    public function getConstraint($constraintName, $table, $schema = null, $database = null)
    {
        return $this->source->getConstraint($constraintName, $table, $schema, $database);
    }

    public function getSchemas()
    {
        // TODO: Implement getSchemas() method.
    }

    public function getTableNames($schema = null, $database = null)
    {
        return $this->source->getTableNames($schema, $database);
    }

    public function getTable($tableName, $schema = null, $database = null)
    {
        return $this->source->getTable($tableName, $schema, $database);
    }

    public function getViewNames($schema = null, $database = null)
    {
        // TODO: Implement getViewNames() method.
    }

    public function getView($viewName, $schema = null, $database = null)
    {
        // TODO: Implement getView() method.
    }

    public function getTriggerNames($schema = null, $database = null)
    {
        // TODO: Implement getTriggerNames() method.
    }

    public function getTrigger($triggerName, $schema = null, $database = null)
    {
        // TODO: Implement getTrigger() method.
    }

    public function getColumnNames($table, $schema = null, $database = null)
    {
        // TODO: Implement getColumnNames() method.
    }

    public function getColumn($columnName, $table, $schema = null, $database = null)
    {
        // TODO: Implement getColumn() method.
    }

}