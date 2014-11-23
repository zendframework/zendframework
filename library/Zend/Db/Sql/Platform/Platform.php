<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Platform;

use Zend\Db\Adapter\AdapterInterface;

class Platform extends AbstractPlatform
{
    /**
     * @var AdapterInterface
     */
    protected $adapter = null;

    public function __construct(AdapterInterface $adapter)
    {
        $this->defaultPlatform = $adapter->getPlatform();

        $mySqlPlatform     = new Mysql\Mysql();
        $sqlServerPlatform = new SqlServer\SqlServer();
        $oraclePlatform    = new Oracle\Oracle();
        $ibmDb2Platform    = new IbmDb2\IbmDb2();

        $this->decorators['mysql']     = $mySqlPlatform->getDecorators();
        $this->decorators['sqlserver'] = $sqlServerPlatform->getDecorators();
        $this->decorators['oracle']    = $oraclePlatform->getDecorators();
        $this->decorators['ibm db2']   = $ibmDb2Platform->getDecorators();
        $this->decorators['ibmdb2']    = $this->decorators['ibm db2'];
    }
}
