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
        $sqlPlatform = new Mysql\Mysql();
        $this->decorators['mysql'] = $sqlPlatform->getDecorators();
        $sqlPlatform = new SqlServer\SqlServer();
        $this->decorators['sqlserver'] = $sqlPlatform->getDecorators();
        $sqlPlatform = new Oracle\Oracle();
        $this->decorators['oracle'] = $sqlPlatform->getDecorators();
        $sqlPlatform = new IbmDb2\IbmDb2();
        $this->decorators['ibm db2'] = $sqlPlatform->getDecorators();
        $this->decorators['ibm_db2'] = $this->decorators['ibm db2'];
        $this->decorators['ibmdb2']  = $this->decorators['ibm db2'];
    }
}
