<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\Adapter\Adapter;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 */
class MasterSlaveFeature extends AbstractFeature
{

    /**
     * @var Adapter
     */
    protected $masterAdapter = null;

    /**
     * @var Adapter
     */
    protected $slaveAdapter = null;

    /**
     * Constructor
     *
     * @param Adapter $slaveAdapter
     */
    public function __construct(Adapter $slaveAdapter)
    {
        $this->slaveAdapter = $slaveAdapter;
    }

    /**
     * after initialization, retrieve the original adapter as "master"
     */
    public function postInitialize()
    {
        $this->masterAdapter = $this->tableGateway->adapter;
    }

    /**
     * preSelect()
     * Replace adapter with slave temporarily
     */
    public function preSelect()
    {
        $this->tableGateway->adapter = $this->slaveAdapter;
    }

    /**
     * postSelect()
     * Ensure to return to the master adapter
     */
    public function postSelect()
    {
        $this->tableGateway->adapter = $this->masterAdapter;
    }
}
