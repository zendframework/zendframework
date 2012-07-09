<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\Sql\Platform;

use Zend\Db\Adapter\Adapter;

class Platform extends AbstractPlatform
{

    /**
     * @var Adapter
     */
    protected $adapter = null;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $platform = $adapter->getPlatform();
        switch (strtolower($platform->getName())) {
            case 'sqlserver':
                $platform = new SqlServer\SqlServer();
                $this->decorators = $platform->decorators;
                break;
            default:
        }
    }

}
