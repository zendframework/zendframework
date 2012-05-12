<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace ZendTest\Service\WindowsAzure;

use Zend\Service\WindowsAzure\Storage\DynamicTableEntity;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 */
class DynamicTableEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $target = new DynamicTableEntity('partition1', '000001');
        $this->assertEquals('partition1', $target->getPartitionKey());
        $this->assertEquals('000001',     $target->getRowKey());
    }

    /**
     * Test get Azure values
     */
    public function testGetAzureValues()
    {
        $target = new DynamicTableEntity('partition1', '000001');
        $target->Name = 'Name';
        $target->Age  = 25;
        $result = $target->getAzureValues();

        $this->assertEquals('Name',       $result[0]->Name);
        $this->assertEquals('Name',       $result[0]->Value);
        $this->assertEquals('Edm.String', $result[0]->Type);

        $this->assertEquals('Age',        $result[1]->Name);
        $this->assertEquals(25,           $result[1]->Value);
        $this->assertEquals('Edm.Int32',  $result[1]->Type);

        $this->assertEquals('partition1', $result[2]->Value);
        $this->assertEquals('000001',     $result[3]->Value);
    }

    /**
     * Test set Azure values
     */
    public function testSetAzureValues()
    {
        $values = array(
            'PartitionKey' => 'partition1',
            'RowKey' => '000001',
            'Name' => 'Maarten',
            'Age' => 25,
            'Visible' => true
        );

        $target = new DynamicTableEntity();
        $target->setAzureValues($values);
        $target->setAzurePropertyType('Age', 'Edm.Int32');

        $this->assertEquals('partition1', $target->getPartitionKey());
        $this->assertEquals('000001',     $target->getRowKey());
        $this->assertEquals('Maarten',    $target->Name);
        $this->assertEquals(25,           $target->Age);
        $this->assertEquals('Edm.Int32',  $target->getAzurePropertyType('Age'));
        $this->assertEquals(true,         $target->Visible);
    }
}
