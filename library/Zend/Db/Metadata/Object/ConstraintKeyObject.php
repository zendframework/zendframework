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
class ConstraintKeyObject
{
    const FK_CASCADE = 'CASCADE';
    const FK_SET_NULL = 'SET NULL';
    const FK_NO_ACTION = 'NO ACTION';
    const FK_RESTRICT = 'RESTRICT';
    const FK_SET_DEFAULT = 'SET DEFAULT';

    /**
     *
     * @var string
     */
    protected $columnName = null;
    /**
     *
     * @var type
     */
    protected $ordinalPosition = null;
    /**
     *
     * @var type
     */
    protected $positionInUniqueConstraint = null;
    /**
     *
     * @var string 
     */
    protected $referencedTableSchema = null;
    /**
     *
     * @var string
     */
    protected $referencedTableName = null;
    /**
     *
     * @var string 
     */
    protected $referencedColumnName = null;
    /**
     *
     * @var type 
     */
    protected $foreignKeyUpdateRule = null;
    /**
     *
     * @var type 
     */
    protected $foreignKeyDeleteRule = null;

    /**
     * Constructor
     * 
     * @param string $column 
     */
    public function __construct($column)
    {
        $this->setColumnName($column);
    }
    /**
     * Get column name
     * 
     * @return string 
     */
    public function getColumnName()
    {
        return $this->columnName;
    }
    /**
     * Set column name
     * 
     * @param  string $columnName
     * @return ConstraintKeyObject 
     */
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;
        return $this;
    }
    /**
     * Get ordinal position
     * 
     * @return type 
     */
    public function getOrdinalPosition()
    {
        return $this->ordinalPosition;
    }
    /**
     * Set ordinal position
     * 
     * @param  type $ordinalPosition
     * @return ConstraintKeyObject 
     */
    public function setOrdinalPosition($ordinalPosition)
    {
        $this->ordinalPosition = $ordinalPosition;
        return $this;
    }
    /**
     * Get position in unique constraint
     * 
     * @return type 
     */
    public function getPositionInUniqueConstraint()
    {
        return $this->positionInUniqueConstraint;
    }
    /**
     * Set position in unique constraint
     * 
     * @param  type $positionInUniqueConstraint
     * @return ConstraintKeyObject 
     */
    public function setPositionInUniqueConstraint($positionInUniqueConstraint)
    {
        $this->positionInUniqueConstraint = $positionInUniqueConstraint;
        return $this;
    }
    /**
     * Get referencred table schema
     * 
     * @return string 
     */
    public function getReferencedTableSchema()
    {
        return $this->referencedTableSchema;
    }
    /**
     * Set referenced table schema
     * 
     * @param type $referencedTableSchema
     * @return ConstraintKeyObject 
     */
    public function setReferencedTableSchema($referencedTableSchema)
    {
        $this->referencedTableSchema = $referencedTableSchema;
        return $this;
    }
    /**
     * Get referenced table name
     * 
     * @return string 
     */
    public function getReferencedTableName()
    {
        return $this->referencedTableName;
    }
    /**
     * Set Referenced table name
     * 
     * @param  string $referencedTableName
     * @return ConstraintKeyObject 
     */
    public function setReferencedTableName($referencedTableName)
    {
        $this->referencedTableName = $referencedTableName;
        return $this;
    }
    /**
     * Get referenced column name
     * 
     * @return string 
     */
    public function getReferencedColumnName()
    {
        return $this->referencedColumnName;
    }
    /**
     * Set referenced column name
     * 
     * @param  string $referencedColumnName
     * @return ConstraintKeyObject 
     */
    public function setReferencedColumnName($referencedColumnName)
    {
        $this->referencedColumnName = $referencedColumnName;
        return $this;
    }
    /**
     * set foreign key update rule
     * 
     * @param type $foreignKeyUpdateRule 
     */
    public function setForeignKeyUpdateRule($foreignKeyUpdateRule)
    {
        $this->foreignKeyUpdateRule = $foreignKeyUpdateRule;
    }
    /**
     * Get foreign key update rule
     * 
     * @return type 
     */
    public function getForeignKeyUpdateRule()
    {
        return $this->foreignKeyUpdateRule;
    }
    /**
     * Set foreign key delete rule
     * 
     * @param type $foreignKeyDeleteRule 
     */
    public function setForeignKeyDeleteRule($foreignKeyDeleteRule)
    {
        $this->foreignKeyDeleteRule = $foreignKeyDeleteRule;
    }
    /**
     * get foreign key delete rule
     * 
     * @return type 
     */
    public function getForeignKeyDeleteRule()
    {
        return $this->foreignKeyUpdateRule;
    }

}
