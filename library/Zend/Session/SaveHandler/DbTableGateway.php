<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-webat this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Session\SaveHandler;

use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Exception;

/**
 * DB Table Gateway session save handler
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage SaveHandler
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DbTableGateway implements SaveHandlerInterface
{
    /**
     * Session Save Path
     *
     * @var string
     */
    protected $sessionSavePath;

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Lifetime
     * @var int
     */
    protected $lifetime;

    /**
     * Zend Db Table Gateway
     * @var Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * DbTableGateway Options
     * @var DbTableGatewayOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param  Zend\Db\Adapter\Adapter $adapter
     * @param  DbTableGatewayOptions $options
     */
    public function __construct(TableGateway $tableGateway, DbTableGatewayOptions $options)
    {
        $this->tableGateway = $tableGateway;
        $this->options = $options;
    }

    /**
     * Open Session
     *
     * @param string $save_path
     * @param string $name
     * @return boolean
     */
    public function open($savePath, $name)
    {
        $this->sessionSavePath = $savePath;
        $this->sessionName     = $name;
        $this->lifetime        = ini_get('session.gc_maxlifetime');

        return true;
    }

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {

        $rows = $this->tableGateway->select(array(
            $this->options->getIdColumn() => $id,
            $this->options->getNameColumn() => $this->sessionName,
        ));

        if ($row = $rows->current()) {
            if ($row->{$this->options->getModifiedColumn()} +
                $row->{$this->options->getLifetimeColumn()} > time()) {
                return $row->{$this->options->getDataColumn()};
            }
            $this->destroy($id);
        }
        return '';
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return boolean
     */
    public function write($id, $data)
    {
        $data = array(
            $this->options->getModifiedColumn() => time(),
            $this->options->getDataColumn() => (string) $data,
        );

        $rows = $this->tableGateway->select(array(
            $this->options->getIdColumn() => $id,
            $this->options->getNameColumn() => $this->sessionName,
        ));

        if ($row = $rows->current()) {
            return (bool) $this->tableGateway->update($data, array(
                $this->options->getIdColumn() => $id,
                $this->options->getNameColumn() => $this->sessionName,
            ));
        }
        $data[$this->options->getLifetimeColumn()] = $this->lifetime;
        $data[$this->options->getIdColumn()] = $id;
        $data[$this->options->getNameColumn()] = $this->sessionName;
        return (bool) $this->tableGateway->insert($data);
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        return (bool) $this->tableGateway->delete(array(
            $this->options->getIdColumn() => $id,
            $this->options->getNameColumn() => $this->sessionName,
        ));
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        $this->delete($this->getAdapter()->quoteIdentifier($this->options->getModifiedColumn()) . ' + '
                    . $this->getAdapter()->quoteIdentifier($this->options->getLifetimeColumn()) . ' < '
                    . $this->getAdapter()->quote(time()));
        return true;
    }
}
