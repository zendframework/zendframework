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
class MasterSlaveTableGateway extends TableGateway
{
    /**
     * @var Adapter
     */
    protected $slaveAdapter = null;

    /**
     * @var Adapter
     */
    protected $masterAdapter = null;

    /**
     * Constructor
     * 
     * @param string $table
     * @param Adapter $masterAdapter
     * @param Adapter $slaveAdapter
     * @param type $databaseSchema
     * @param ResultSet $selectResultPrototype 
     */
    public function __construct($table, Adapter $masterAdapter, Adapter $slaveAdapter, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        $this->masterAdapter = $masterAdapter;
        $this->slaveAdapter = $slaveAdapter;

        // initialize adapter to masterAdapter
        parent::__construct($table, $masterAdapter, $databaseSchema, $selectResultPrototype);
    }

    /**
     * Select
     * 
     * @param  string $where
     * @return type 
     */
    public function select($where = null)
    {
        $this->adapter = $this->slaveAdapter;
        return parent::select($where);
    }

    /**
     * Insert
     * 
     * @param  string $set
     * @return type 
     */
    public function insert($set)
    {
        $this->adapter = $this->masterAdapter;
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
        $this->adapter = $this->masterAdapter;
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
        $this->adapter = $this->masterAdapter;
        return parent::delete($where);
    }

}
