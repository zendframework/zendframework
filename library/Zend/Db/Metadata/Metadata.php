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
 * @package    Zend_Db
 * @subpackage Metadata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Metadata;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
    public function getTables($schema = null, $database = null)
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
    public function getViews($schema = null, $database = null)
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
    public function getTriggers($schema = null, $database = null)
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
    public function getConstraints($table, $schema = null, $database = null)
    {
        return $this->source->getConstraints($table, $schema, $database);
    }
    /**
     * Get columns
     * 
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getColumns($table, $schema = null, $database = null)
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
    public function getConstraintKeys($constraint, $table, $schema = null, $database = null)
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
    public function getConstraint($constraintName, $table, $schema = null, $database = null)
    {
        return $this->source->getConstraint($constraintName, $table, $schema, $database);
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
    public function getTableNames($schema = null, $database = null)
    {
        return $this->source->getTableNames($schema, $database);
    }
    /**
     * Get table
     * 
     * @param  string $tableName
     * @param  string $schema
     * @param  string $database
     * @return Object\TableObject 
     */
    public function getTable($tableName, $schema = null, $database = null)
    {
        return $this->source->getTable($tableName, $schema, $database);
    }
    /**
     * Get views names
     * 
     * @param string $schema
     * @param string $database 
     */
    public function getViewNames($schema = null, $database = null)
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
    public function getView($viewName, $schema = null, $database = null)
    {
        // TODO: Implement getView() method.
    }
    /**
     * Get trigger names
     * 
     * @param string $schema
     * @param string $database 
     */
    public function getTriggerNames($schema = null, $database = null)
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
    public function getTrigger($triggerName, $schema = null, $database = null)
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
    public function getColumnNames($table, $schema = null, $database = null)
    {
        // TODO: Implement getColumnNames() method.
    }
    /**
     * Get column
     * 
     * @param string $columnName
     * @param string $table
     * @param string $schema
     * @param string $database 
     */
    public function getColumn($columnName, $table, $schema = null, $database = null)
    {
        // TODO: Implement getColumn() method.
    }

}