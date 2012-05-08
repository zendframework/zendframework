<?php

namespace Zend\Db\Sql\Platform\SqlServer;

use Zend\Db\Sql\Platform\AbstractPlatform,
    Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Sql\Select;

class SqlServer extends AbstractPlatform
{

    /**
     * @param Adapter $adapter
     */
    public function __construct(SelectDecorator $selectDecorator = null)
    {
        $this->setTypeDecorator('Zend\Db\Sql\Select', ($selectDecorator) ?: new SelectDecorator());
    }

}
