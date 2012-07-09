<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 */
interface SqlInterface
{
    public function getSqlString(PlatformInterface $adapterPlatform = null);
}
