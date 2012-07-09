<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\RowGateway;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage RowGateway
 */
interface RowGatewayInterface
{
    public function save();
    public function delete();
}
