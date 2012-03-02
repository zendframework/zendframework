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

namespace Zend\Db\Metadata\Object;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ConstraintObject
{
    /*
    protected $catalogName = null;
    protected $schemaName = null;
    */

    /**
     *
     * @var string
     */
    protected $name = null;
    /**
     *
     * @var string
     */
    protected $tableName = null;
    /**
     *
     * @var string
     */
    protected $schemaName = null;

//    protected $tableCatalogName = null;
//    protected $tableSchemaName = null;

    /**
     *
     * @var string
     */
    protected $type = null;
    /**
     *
     * @var array
     */
    protected $keys = null;

    /*
    public function getCatalogName()
    {
        return $this->catalogName;
    }
    
    public function setCatalogName($catalogName)
    {
        $this->catalogName = $catalogName;
        return $this;
    }
    
    public function getSchemaName()
    {
        return $this->schemaName;
    }
    
    public function setSchemaName($schemaName)
    {
        $this->schemaName = $schemaName;
        return $this;
    }
    */
    /**
     * Constructor
     * 
     * @param string $name
     * @param string $table
     * @param string $schemaName 
     */
    public function __construct($name, $table, $schemaName = null)
    {
        $this->setName($name);
        $this->setTableName($table);
        if ($schemaName) {
            $this->setSchemaName($schemaName);
        }
    }
    /**
     * Set name
     * 
     * @param string $name 
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    /**
     * Get name
     * 
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Set schema name
     * 
     * @param string $schemaName 
     */
    public function setSchemaName($schemaName)
    {
        $this->schemaName = $schemaName;
    }
    /**
     * Get schema name
     * 
     * @return string 
     */
    public function getSchemaName()
    {
        return $this->schemaName;
    }
    /**
     * Get table name
     * 
     * @return string 
     */
    public function getTableName()
    {
        return $this->tableName;
    }
    /**
     * Set table name
     * 
     * @param  string $tableName
     * @return ConstraintObject 
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }
    /**
     * Set type
     * 
     * @param type $constraintType 
     */
    public function setType($constraintType)
    {
        $this->type = $constraintType;
    }
    /**
     * Get type
     * 
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Set keys
     * 
     * @param array $keys 
     */
    public function setKeys(array $keys)
    {
        $this->keys = $keys;
    }
    /**
     * Get keys
     * 
     * @return string 
     */
    public function getKeys()
    {
        return $this->keys;
    }
    /**
     * Is primary key
     * 
     * @return boolean 
     */
    public function isPrimaryKey()
    {
        return (strtoupper($this->type) == 'PRIMARY');
    }
    /**
     * Is unique key
     * 
     * @return boolean 
     */
    public function isUniqueKey()
    {
        return (strtoupper($this->type) == 'UNIQUE');
    }
    /**
     * Is foreign key
     * 
     * @return boolean 
     */
    public function isForeignKey()
    {
        return (strtoupper($this->type) == 'FOREIGN KEY');
    }

}