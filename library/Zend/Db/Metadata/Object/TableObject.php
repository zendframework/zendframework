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
class TableObject
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
    protected $type = null;
    /**
     *
     * @var array
     */
    protected $columns = null;
    /**
     *
     * @var array
     */
    protected $constraints = null;

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
     */
    public function __construct($name)
    {
        if ($name) {
            $this->setName($name);
        }
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
     * Set type
     * 
     * @param  string $type
     * @return TableObject 
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    /**
     * Set columns
     * 
     * @param array $columns 
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }
    /**
     * Get columns
     * 
     * @return array 
     */
    public function getColumns()
    {
        return $this->columns;
    }
    /**
     * Set constraints
     * 
     * @param array $constraints 
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }
    /**
     * Get constraints
     * 
     * @return array 
     */
    public function getConstraints()
    {
        return $this->columns;
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

}
