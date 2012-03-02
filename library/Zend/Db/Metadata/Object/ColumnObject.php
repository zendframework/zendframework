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
class ColumnObject
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
    /**
     *
     * @var 
     */
    protected $ordinalPosition = null;
    /**
     *
     * @var string
     */
    protected $columnDefault = null;
    /**
     *
     * @var boolean 
     */
    protected $isNullable = null;
    /**
     *
     * @var string
     */
    protected $dataType = null;
    /**
     *
     * @var integer
     */
    protected $characterMaximumLength = null;
    /**
     *
     * @var integer
     */
    protected $characterOctetLength = null;
    /**
     *
     * @var type
     */
    protected $numericPrecision = null;
    /**
     *
     * @var type 
     */
    protected $numericScale = null;
//    protected $characterSetName = null;
//    protected $collationName = null;
    /**
     *
     * @var array
     */
    protected $errata = array();


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
     * @param string $tableName
     * @param string $schemaName 
     */
    public function __construct($name, $tableName, $schemaName = null)
    {
        $this->setName($name);
        $this->setTableName($tableName);
        $this->setSchemaName($schemaName);
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
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
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
     * @return int $ordinalPosition
     */
    public function getOrdinalPosition()
    {
        return $this->ordinalPosition;
    }

    /**
     * @param int $ordinalPosition to set
     * @return Column
     */
    public function setOrdinalPosition($ordinalPosition)
    {
        $this->ordinalPosition = $ordinalPosition;
        return $this;
    }

    /**
     * @return the $columnDefault
     */
    public function getColumnDefault()
    {
        return $this->columnDefault;
    }

    /**
     * @param mixed $columnDefault to set
     */
    public function setColumnDefault($columnDefault)
    {
        $this->columnDefault = $columnDefault;
        return $this;
    }

    /**
     * @return bool $isNullable
     */
    public function getIsNullable()
    {
        return $this->isNullable;
    }

    /**
     * @param bool $isNullable to set
     */
    public function setIsNullable($isNullable)
    {
        $this->isNullable = $isNullable;
        return $this;
    }

    /**
     * @return the $dataType
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @param $dataType the $dataType to set
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
        return $this;
    }

    /**
     * @return the $characterMaximumLength
     */
    public function getCharacterMaximumLength()
    {
        return $this->characterMaximumLength;
    }

    /**
     * @param $characterMaximumLength the $characterMaximumLength to set
     */
    public function setCharacterMaximumLength($characterMaximumLength)
    {
        $this->characterMaximumLength = $characterMaximumLength;
        return $this;
    }

    /**
     * @return the $characterOctetLength
     */
    public function getCharacterOctetLength()
    {
        return $this->characterOctetLength;
    }

    /**
     * @param $characterOctetLength the $characterOctetLength to set
     */
    public function setCharacterOctetLength($characterOctetLength)
    {
        $this->characterOctetLength = $characterOctetLength;
        return $this;
    }

    /**
     * @return the $numericPrecision
     */
    public function getNumericPrecision()
    {
        return $this->numericPrecision;
    }

    /**
     * @param $numericPrevision the $numericPrevision to set
     */
    public function setNumericPrecision($numericPrecision)
    {
        $this->numericPrecision = $numericPrecision;
        return $this;
    }

    /**
     * @return the $numericScale
     */
    public function getNumericScale()
    {
        return $this->numericScale;
    }

    /**
     * @param $numericScale the $numericScale to set
     */
    public function setNumericScale($numericScale)
    {
        $this->numericScale = $numericScale;
        return $this;
    }

//    /**
//     * @return the $characterSetName
//     */
//    public function getCharacterSetName()
//    {
//        return $this->characterSetName;
//    }
//
//    /**
//     * @param $characterSetName the $characterSetName to set
//     */
//    public function setCharacterSetName($characterSetName)
//    {
//        $this->characterSetName = $characterSetName;
//        return $this;
//    }
//
//    /**
//     * @return the $collationName
//     */
//    public function getCollationName()
//    {
//        return $this->collationName;
//    }
//
//    /**
//     * @param $collationName the $collationName to set
//     */
//    public function setCollationName($collationName)
//    {
//        $this->collationName = $collationName;
//        return $this;
//    }


    /**
     * @return the $errata
     */
    public function getErratas()
    {
        return $this->errata;
    }

    /**
     * @param string $errataName
     * @param mixed $errataValue
     * @return ColumnMetadata
     */
    public function setErrata($errataName, $errataValue)
    {
        $this->errata[$errataName] = $errataValue;
        return $this;
    }




}