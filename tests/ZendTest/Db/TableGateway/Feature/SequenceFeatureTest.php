<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\TableGateway\Feature;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway\Feature\SequenceFeature;

class SequenceFeatureTest extends PHPUnit_Framework_TestCase
{
    /** @var SequenceFeature */
    protected $feature = null;

    /** @var \Zend\Db\TableGateway\TableGateway */
    protected $tableGateway = null;

    /**  @var string primary key name */
    protected $primaryKeyField = 'id';

    /** @var string  sequence name */
    protected $sequenceName = 'table_sequence';

    public function setup()
    {
        $this->feature = new SequenceFeature($this->primaryKeyField, $this->sequenceName);
    }

    /**
     * @dataProvider nextSequenceIdProvider
     */
    public function testNextSequenceId($platformName, $statementSql)
    {
        $platform = $this->getMockForAbstractClass('Zend\Db\Adapter\Platform\PlatformInterface', array('getName'));
        $platform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($platformName));
        $platform->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnValue($this->sequenceName));
        $adapter = $this->getMock('Zend\Db\Adapter\Adapter', array('getPlatform', 'createStatement'), array(), '', false);
        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platform));
        $result = $this->getMockForAbstractClass('Zend\Db\Adapter\Driver\ResultInterface', array(), '', false, true, true, array('current'));
        $result->expects($this->any())
            ->method('current')
            ->will($this->returnValue(array('nextval' => 2)));
        $statement = $this->getMockForAbstractClass('Zend\Db\Adapter\Driver\StatementInterface', array(), '', false, true, true, array('prepare', 'execute'));
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));
        $statement->expects($this->any())
            ->method('prepare')
            ->with($statementSql);
        $adapter->expects($this->once())
            ->method('createStatement')
            ->will($this->returnValue($statement));
        $this->tableGateway = $this->getMockForAbstractClass('Zend\Db\TableGateway\TableGateway', array('table', $adapter), '', true);
        $this->feature->setTableGateway($this->tableGateway);
        $this->feature->nextSequenceId();
    }

    public function nextSequenceIdProvider()
    {
        return array(array('PostgreSQL', 'SELECT NEXTVAL(\'"' . $this->sequenceName . '"\')'),
            array('Oracle', 'SELECT ' . $this->sequenceName . '.NEXTVAL as "nextval" FROM dual'));
    }
}
