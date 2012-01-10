<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Service_WindowsAzure_Storage_TableEntity 
 */

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_TableEntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $target = new TSETTest_TestEntity('partition1', '000001');
        $this->assertEquals('partition1', $target->getPartitionKey());
        $this->assertEquals('000001',     $target->getRowKey());
    }
    
    /**
     * Test get Azure values
     */
    public function testGetAzureValues()
    {
        $target = new TSETTest_TestEntity('partition1', '000001');
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
        
        $target = new TSETTest_TestEntity();
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
        $target = new TSETTest_TestEntity();
        try 
        {
            $target->setAzureValues($values, true);
        }
        catch (Exception $ex) {
            $exceptionRaised = true;
        }
        
        $this->assertTrue($exceptionRaised);
    }
}

/**
 * Test entity
 */
class TSETTest_TestEntity extends Zend_Service_WindowsAzure_Storage_TableEntity
{
    /**
     * @azure Name
     */
    public $FullName;
    
    /**
     * @azure Age Edm.Int64
     */
    public $Age;
    
    /**
     * @azure Visible Edm.Boolean
     */
    public $Visible = false;
}
