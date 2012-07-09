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
interface ResultInterface extends \Countable, \Iterator
{
    public function buffer();
    public function isQueryResult();
    public function getAffectedRows();
    public function getGeneratedValue();
    public function getResource();
    public function getFieldCount();
}
