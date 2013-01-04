<?php
namespace ZendTest\Db\ResultSet;

use Zend\Db\ResultSet\HydratingResultSet;

class HydratingResultSetIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Zend\Db\ResultSet\HydratingResultSet::current
     */
    public function testCurrentWillReturnBufferedRow()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratingRs->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
        )));
        $hydratingRs->buffer();
        $obj1 = $hydratingRs->current();
        $hydratingRs->rewind();
        $obj2 = $hydratingRs->current();
        $this->assertSame($obj1, $obj2);
    }

}
