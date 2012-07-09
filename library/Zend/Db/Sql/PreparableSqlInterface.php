<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 */
interface PreparableSqlInterface
{

    /**
     * @abstract
     * @param Adapter $adapter
     * @return StatementInterface
     */
    public function prepareStatement(Adapter $adapter, StatementInterface $statement);
}
