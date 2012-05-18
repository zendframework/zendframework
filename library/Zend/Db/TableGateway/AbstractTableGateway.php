<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 */
abstract class AbstractTableGateway extends TableGateway
{
    /**
     *
     * @var boolean
     */
    protected $initialized = false;

    /**
     *
     * @var boolean
     */
    protected $lazyInitialize = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setup();
        if ($this->lazyInitialize != true) {
            $this->initialize();
        }
    }

    /**
     * Set adapter
     * 
     * @param Adapter $adapter 
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @abstract
     */
    public function setup()
    {
        // filled in by the child class
    }

    /**
     * Initialize
     * 
     * @return null 
     */
    public function initialize()
    {
        if ($this->initialized == true) {
            return;
        }

        if ($this->tableName == null) {
            throw new \Exception('$tableName must be configured in initialize()');
        }

        if (!$this->adapter) {
            // what to do?
        }

        if ($this->schema == null) {
            $this->schema = $this->adapter->getDefaultSchema();
        }

        if (!$this->selectResultPrototype) {
            $this->setSelectResultPrototype(new ResultSet);
        }

        $this->initializeSqlObjects();
        $this->initialized = true;
    }

    /**
     * Select
     * 
     * @param  string $where
     * @return type 
     */
    public function select($where = null)
    {
        $this->initialize();
        return parent::select($where);
    }

    /**
     * Insert
     * 
     * @param type $set
     * @return type 
     */
    public function insert($set)
    {
        $this->initialize();
        return parent::insert($set);
    }

    /**
     * Update
     * 
     * @param  string $set
     * @param  string $where
     * @return type 
     */
    public function update($set, $where = null)
    {
        $this->initialize();
        return parent::update($set, $where);
    }

    /**
     * Delete
     * 
     * @param  string $where
     * @return type 
     */
    public function delete($where)
    {
        $this->initialize();
        return parent::delete($where);
    }

}
