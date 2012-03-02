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
 * @subpackage TableGateway
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

    public function __construct($tableName, Adapter $masterAdapter, Adapter $slaveAdapter, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        $this->masterAdapter = $masterAdapter;
        $this->slaveAdapter = $slaveAdapter;

        // initialize adapter to masterAdapter
        parent::__construct($tableName, $masterAdapter, $databaseSchema, $selectResultPrototype);
    }


    public function select($where)
    {
        $this->adapter = $this->slaveAdapter;
        return parent::select($where);
    }

    public function insert($set)
    {
        $this->adapter = $this->masterAdapter;
        return parent::insert($set);
    }

    public function update($set, $where)
    {
        $this->adapter = $this->masterAdapter;
        return parent::update($set, $where);
    }

    public function delete($where)
    {
        $this->adapter = $this->masterAdapter;
        return parent::delete($where);
    }

}