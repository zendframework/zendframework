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

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 */
class TableEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $target = new TestAsset\Entity('partition1', '000001');
        $this->assertEquals('partition1', $target->getPartitionKey());
        $this->assertEquals('000001',     $target->getRowKey());
    }

    /**
     * Test get Azure values
     */
    public function testGetAzureValues()
    {
        $target = new TestAsset\Entity('partition1', '000001');
        $result = $target->getAzureValues();

        $this->assertEquals('Name',       $result[0]->Name);
        $this->assertEquals(null,         $result[0]->Value);

        $this->assertEquals('Age',        $result[1]->Name);
        $this->assertEquals('Edm.Int64',  $result[1]->Type);

        $this->assertEquals('Visible',    $result[2]->Name);
        $this->assertEquals(false,        $result[2]->Value);

        $this->assertEquals('partition1', $result[3]->Value);
        $this->assertEquals('000001',     $result[4]->Value);
    }

    /**
     * Test set Azure values
     */
    public function testSetAzureValuesSuccess()
    {
        $values = array(
            'PartitionKey' => 'partition1',
            'RowKey' => '000001',
            'Name' => 'Maarten',
            'Age' => 25,
            'Visible' => true
        );

        $target = new TestAsset\Entity();
        $target->setAzureValues($values);

        $this->assertEquals('partition1', $target->getPartitionKey());
        $this->assertEquals('000001',     $target->getRowKey());
        $this->assertEquals('Maarten',    $target->FullName);
        $this->assertEquals(25,           $target->Age);
        $this->assertEquals(true,         $target->Visible);
    }

    /**
     * Test set Azure values
     */
    public function testSetAzureValuesFailure()
    {
        $values = array(
            'PartitionKey' => 'partition1',
            'RowKey' => '000001'
        );

        $exceptionRaised = false;
        $target = new TestAsset\Entity();
        try
        {
            $target->setAzureValues($values, true);
        }
        catch (\Exception $ex) {
            $exceptionRaised = true;
        }

        $this->assertTrue($exceptionRaised);
    }
}