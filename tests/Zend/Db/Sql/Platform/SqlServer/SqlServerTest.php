<?php

namespace ZendTest\Db\Sql\Platform\SqlServer;

use Zend\Db\Sql\Platform\SqlServer\SqlServer;

class SqlServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox unit test / object test: Test SqlServer object has Select proxy
     * @covers Zend\Db\Sql\Platform\SqlServer\SqlServer::__construct
     */
    public function testConstruct()
    {
        $sqlServer = new SqlServer;
        $decorators = $sqlServer->getDecorators();

        list($type, $decorator) = each($decorators);
        $this->assertEquals('Zend\Db\Sql\Select', $type);
        $this->assertInstanceOf('Zend\Db\Sql\Platform\SqlServer\SelectDecorator', $decorator);
    }

}
