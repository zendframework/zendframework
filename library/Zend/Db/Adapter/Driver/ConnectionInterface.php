<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
interface ConnectionInterface
{
    public function getCurrentSchema();
    public function getResource();
    public function connect();
    public function isConnected();
    public function disconnect();
    public function beginTransaction();
    public function commit();
    public function rollback();
    public function execute($sql); // return result set
    public function getLastGeneratedValue($name = null);
}
