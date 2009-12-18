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
 * @version    $Id: BlobStorageTest.php 14561 2009-05-07 08:05:12Z unknown $
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_WindowsAzure_DynamicTableEntityTest::main');
}

/**
 * Test helpers
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once dirname(__FILE__) . '/../../../TestConfiguration.php.dist';
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Service_WindowsAzure_Storage_DynamicTableEntity */
require_once 'Zend/Service/WindowsAzure/Storage/DynamicTableEntity.php';

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @version    $Id: BlobStorageTest.php 14561 2009-05-07 08:05:12Z unknown $
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_DynamicTableEntityTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Service_WindowsAzure_DynamicTableEntityTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $target = new Zend_Service_WindowsAzure_Storage_DynamicTableEntity('partition1', '000001');
        $this->assertEquals('partition1', $target->getPartitionKey());
        $this->assertEquals('000001',     $target->getRowKey());
    }
    
    /**
     * Test get Azure values
     */
    public function testGetAzureValues()
    {
        $target = new Zend_Service_WindowsAzure_Storage_DynamicTableEntity('partition1', '000001');
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
        
        $target = new Zend_Service_WindowsAzure_Storage_DynamicTableEntity();
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

// Call Zend_Service_WindowsAzure_DynamicTableEntityTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Service_WindowsAzure_DynamicTableEntityTest::main") {
    Zend_Service_WindowsAzure_DynamicTableEntityTest::main();
}
