<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\Exception;
use Zend\EventManager\EventManagerInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 */
class EventFeature extends AbstractFeature
{

    /**
     * @var EventManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var null
     */
    protected $event = null;

    /**
     * @param EventManagerInterface $eventManager
     * @param EventFeature\TableGatewayEvent $tableGatewayEvent
     */
    public function __construct(EventManagerInterface $eventManager, EventFeature\TableGatewayEvent $tableGatewayEvent)
    {
        $this->eventManager = $eventManager;
        $this->event = ($tableGatewayEvent) ?: new EventFeature\TableGatewayEvent();
    }

    public function preInitialize()
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->eventManager->trigger($this->event);
    }

    public function postInitialize()
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->eventManager->trigger($this->event);
    }

    public function preSelect(Select $select)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('select' => $select));
        $this->eventManager->trigger($this->event);
    }

    public function postSelect(StatementInterface $statement, ResultInterface $result, ResultSetInterface $resultSet)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
            'result_set' => $resultSet
        ));
        $this->eventManager->trigger($this->event);
    }

    public function preInsert(Insert $insert)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('insert' => $insert));
        $this->eventManager->trigger($this->event);
    }

    public function postInsert(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

    public function preUpdate(Update $update)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('update' => $update));
        $this->eventManager->trigger($this->event);
    }

    public function postUpdate(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

    public function preDelete(Delete $delete)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('delete' => $delete));
        $this->eventManager->trigger($this->event);
    }

    public function postDelete(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

}
