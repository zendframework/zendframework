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

    protected $name = null;
    protected $tableName = null;
    protected $schemaName = null;

//    protected $tableCatalogName = null;
//    protected $tableSchemaName = null;

    protected $type = null;
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

    public function __construct($name, $table, $schemaName = null)
    {
        $this->setName($name);
        $this->setTableName($table);
        if ($schemaName) {
            $this->setSchemaName($schemaName);
        }
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setSchemaName($schemaName)
    {
        $this->schemaName = $schemaName;
    }

    public function getSchemaName()
    {
        return $this->schemaName;
    }

    public function getTableName()
    {
        return $this->tableName;
    }
    
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function setType($constraintType)
    {
        $this->type = $constraintType;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function setKeys(array $keys)
    {
        $this->keys = $keys;
    }

    public function getKeys()
    {
        return $this->keys;
    }

    public function isPrimaryKey()
    {
        return (strtoupper($this->type) == 'PRIMARY');
    }

    public function isUniqueKey()
    {
        return (strtoupper($this->type) == 'UNIQUE');
    }

    public function isForeignKey()
    {
        return (strtoupper($this->type) == 'FOREIGN KEY');
    }

}