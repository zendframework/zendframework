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
class ReferentialConstraint
{
    /*
    protected $catalogName = null;
    protected $schemaName = null;
    */

    /**
     *
     * @var string 
     */
    protected $uniqueConstraintCatalogName = null;
    /**
     *
     * @var string 
     */
    protected $uniqueConstraintSchemaName = null;
    /**
     *
     * @var string 
     */
    protected $uniqueConstraintName = null;
    /**
     *
     * @var type 
     */
    protected $matchOption = null;
    /**
     *
     * @var type 
     */
    protected $updateRule = null;
    /**
     *
     * @var type 
     */
    protected $deleteRule = null;

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
     * Get unique constraint catalog name
     * 
     * @return string 
     */
    public function getUniqueConstraintCatalogName()
    {
        return $this->uniqueConstraintCatalogName;
    }
    /**
     * Set unique constraint catalog name
     * 
     * @param  string $uniqueConstraintCatalogName
     * @return ReferentialConstraint 
     */
    public function setUniqueConstraintCatalogName($uniqueConstraintCatalogName)
    {
        $this->uniqueConstraintCatalogName = $uniqueConstraintCatalogName;
        return $this;
    }
    /**
     * Get unique constraint schema name
     * 
     * @return string 
     */
    public function getUniqueConstraintSchemaName()
    {
        return $this->uniqueConstraintSchemaName;
    }
    /**
     * Set unique constraint schema name
     * 
     * @param  string $uniqueConstraintSchemaName
     * @return ReferentialConstraint 
     */
    public function setUniqueConstraintSchemaName($uniqueConstraintSchemaName)
    {
        $this->uniqueConstraintSchemaName = $uniqueConstraintSchemaName;
        return $this;
    }
    /**
     * Get unique constraint name
     * 
     * @return string 
     */
    public function getUniqueConstraintName()
    {
        return $this->uniqueConstraintName;
    }
    /**
     * Set unique constraint name
     * 
     * @param  string $uniqueConstraintName
     * @return ReferentialConstraint 
     */
    public function setUniqueConstraintName($uniqueConstraintName)
    {
        $this->uniqueConstraintName = $uniqueConstraintName;
        return $this;
    }
    /**
     * Get match option
     * 
     * @return type 
     */
    public function getMatchOption()
    {
        return $this->matchOption;
    }
    /**
     * Set match option
     * 
     * @param  type $matchOption
     * @return ReferentialConstraint 
     */
    public function setMatchOption($matchOption)
    {
        $this->matchOption = $matchOption;
        return $this;
    }
    /**
     * Get update rule
     * 
     * @return type 
     */
    public function getUpdateRule()
    {
        return $this->updateRule;
    }
    /**
     * Set update rule
     * 
     * @param  type $updateRule
     * @return ReferentialConstraint 
     */
    public function setUpdateRule($updateRule)
    {
        $this->updateRule = $updateRule;
        return $this;
    }
    /**
     * Get delete rule
     * 
     * @return type 
     */
    public function getDeleteRule()
    {
        return $this->deleteRule;
    }
    /**
     * Set delete rule
     * 
     * @param  type $deleteRule
     * @return ReferentialConstraint 
     */
    public function setDeleteRule($deleteRule)
    {
        $this->deleteRule = $deleteRule;
        return $this;
    }
    
}
