<?php

namespace ZendTest\Db\TableGateway\Feature;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway\Feature\MetadataFeature;
use Zend\Db\Metadata\Object\ConstraintObject;

class MetadataFeatureTest extends PHPUnit_Framework_TestCase
{


    /**
     * @group integration-test
     */
    public function testPostInitialize()
    {
        $tableGatewayMock = $this->getMockForAbstractClass('Zend\Db\TableGateway\AbstractTableGateway');

        $metadataMock = $this->getMock('Zend\Db\Metadata\MetadataInterface');
        $metadataMock->expects($this->any())->method('getColumnNames')->will($this->returnValue(array('id', 'name')));

        $constraintObject = new ConstraintObject('id_pk', 'table');
        $constraintObject->setColumns(array('id'));
        $constraintObject->setType('PRIMARY KEY');

        $metadataMock->expects($this->any())->method('getConstraints')->will($this->returnValue(array($constraintObject)));

        $feature = new MetadataFeature($metadataMock);
        $feature->setTableGateway($tableGatewayMock);
        $feature->postInitialize();

        $this->assertEquals(array('id', 'name'), $tableGatewayMock->getColumns());
    }

}
