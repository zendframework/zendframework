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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Spreadsheets_ListEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->listEntry = new Zend_Gdata_Spreadsheets_ListEntry();
        $this->rowData = array();
        $this->rowData[] = new Zend_Gdata_Spreadsheets_Extension_Custom(
            'column_1', 'value 1');
        $this->rowData[] = new Zend_Gdata_Spreadsheets_Extension_Custom(
            'column_2', 'value 2');
    }

    public function testToAndFromString()
    {        
        $this->listEntry->setCustom($this->rowData);
        $rowDataOut = $this->listEntry->getCustom();
        
        $this->assertEquals(count($this->rowData), count($rowDataOut));
        for ($i = 0; $i < count($this->rowData); $i++) {
        $this->assertEquals($this->rowData[$i]->getText(),
             $rowDataOut[$i]->getText());
        $this->assertEquals($this->rowData[$i]->getColumnName(),
            $rowDataOut[$i]->getColumnName());
        }
        
        $newListEntry = new Zend_Gdata_Spreadsheets_ListEntry();
        $doc = new DOMDocument();
        $doc->loadXML($this->listEntry->saveXML());
        $newListEntry->transferFromDom($doc->documentElement);
        $rowDataFromXML = $newListEntry->getCustom();
        
        $this->assertEquals(count($this->rowData), count($rowDataFromXML));
        for ($i = 0; $i < count($this->rowData); $i++) {
        $this->assertEquals($this->rowData[$i]->getText(),
             $rowDataFromXML[$i]->getText());
        $this->assertEquals($this->rowData[$i]->getColumnName(),
            $rowDataFromXML[$i]->getColumnName());
        }
    }

    public function testCustomElementOrderingPreserved()
    {   
        $this->listEntry->setCustom($this->rowData);
        
        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        for ($i = 0; $i < count($this->rowData); $i++) {
            $this->assertEquals($this->rowData[$i],
                $this->listEntry->custom[$i]);
        }
    }

    public function testCustomElementsCanBeRetrievedByName()
    {
        $this->listEntry->setCustom($this->rowData);
        
        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        for ($i = 0; $i < count($this->rowData); $i++) {
            $this->assertEquals($this->rowData[$i],
                $this->listEntry->getCustomByName(
                    $this->rowData[$i]->getColumnName()));
        }
    }
    
    public function testCustomElementsCanBeRetrievedByNameUsingArrayNotation()
    {
        $this->listEntry->setCustom($this->rowData);

        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        for ($i = 0; $i < count($this->rowData); $i++) {
            $this->assertEquals($this->rowData[$i],
                $this->listEntry->getCustomByName(
                    $this->rowData[$i]->getColumnName()));
        }
    }
    
    public function testCanAddIndividualCustomElements()
    {
        for ($i = 0; $i < count($this->rowData); $i++) {
            $this->listEntry->addCustom($this->rowData[$i]);
        }
        
        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        for ($i = 0; $i < count($this->rowData); $i++) {
            $this->assertEquals($this->rowData[$i],
                $this->listEntry->custom[$i]);
        }
    }
    
    public function testRetrievingNonexistantCustomElementReturnsNull()
    {
        $this->assertNull($this->listEntry->getCustomByName('nonexistant'));
    }
    
    public function testCanReplaceAllCustomElements()
    {
        $this->listEntry->setCustom($this->rowData);
        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        $this->listEntry->setCustom(array());
        $this->assertEquals(0, count($this->listEntry->getCustom()));
    }
    
    public function testCanDeleteCustomElementById()
    {
        $this->listEntry->setCustom($this->rowData);
        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        $this->assertEquals($this->rowData[0], $this->listEntry->custom[0]);
        
        $this->listEntry->removeCustom(0);
        $this->assertEquals(count($this->rowData) - 1,
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        $this->assertEquals($this->rowData[1], $this->listEntry->custom[0]);
    }
    
    public function testCanDeleteCustomElementByName()
    {
        $this->listEntry->setCustom($this->rowData);
        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        $this->assertEquals($this->rowData[0],
            $this->listEntry->getCustomByName(
                $this->rowData[0]->getColumnName()));
        
        $this->listEntry->removeCustomByName('column_1');
        $this->assertEquals(count($this->rowData) - 1,
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        $this->assertNull($this->listEntry->getCustomByName(
            $this->rowData[0]->getColumnName()));
    }
    
    public function testDeletingNonexistantElementByIdThrowsException()
    {
        $this->listEntry->setCustom($this->rowData);
        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        
        $exceptionCaught = false;
        try {
            $this->listEntry->removeCustom(9999);
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            $exceptionCaught = true;
            $this->assertEquals('Element does not exist.', $e->getMessage());
        }
        $this->assertTrue($exceptionCaught);
    }
    
    public function testDeletingNonexistantElementByNameThrowsException()
    {
        $this->listEntry->setCustom($this->rowData);
        $this->assertEquals(count($this->rowData),
            count($this->listEntry->getCustom()));
        $this->assertEquals(count($this->listEntry->getCustom()),
            count($this->listEntry->getCustomByName()));
        
        $exceptionCaught = false;
        try {
            $this->listEntry->removeCustomByName('nonexistant');
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            $exceptionCaught = true;
            $this->assertEquals('Element does not exist.', $e->getMessage());
        }
        $this->assertTrue($exceptionCaught);
    }

}
