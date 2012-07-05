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
 * @package    Zend_Cloud_StorageService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\StorageService;

use Zend\Cloud\StorageService\Adapter,
    Zend\Cloud\StorageService\Factory,
    Zend\Config\Config,
    PHPUnit_Framework_TestCase as PHPUnitTestCase;

/**
 * This class forces the adapter tests to implement tests for all methods on
 * Zend\Cloud\StorageService
 *
 * @category   Zend
 * @package    Zend_Cloud_StorageService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Reference to storage adapter to test
     *
     * @var \Zend\Cloud\StorageService
     */
    protected $_commonStorage;

    protected $_dummyNamePrefix = 'TestItem';

    protected $_dummyDataPrefix = 'TestData';
    
    protected $_clientType = 'stdClass';

    /**
     * Config object
     *
     * @var \Zend\Config\Config
     */

    protected $_config;

    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 1;

    public function setUp()
    {
        $this->_config = $this->_getConfig();
        $this->_commonStorage = Factory::getAdapter($this->_config);
    }

    public function testGetClient()
    {
    	$this->assertTrue(is_a($this->_commonStorage->getClient(), $this->_clientType));
    }

//    public function testNoParams()
//    {
//        $config = array(Factory::STORAGE_ADAPTER_KEY => $this->_config->get(Factory::STORAGE_ADAPTER_KEY));
//        $this->setExpectedException('Zend\Cloud\StorageService\Exception\ExceptionInterface');
//        $s = Factory::getAdapter($config);
//    }
//
//    /**
//     * Test fetch item
//     *
//     * @return void
//     */
//    public function testFetchItemString()
//    {
//        $dummyNameText   = null;
//        $dummyNameStream = null;
//        try {
//            $originalData  = $this->_dummyDataPrefix . 'FetchItem';
//            $dummyNameText = $this->_dummyNamePrefix . 'ForFetchText';
//            $this->_clobberItem($originalData, $dummyNameText);
//            $this->_wait();
//
//            $returnedData = $this->_commonStorage->fetchItem($dummyNameText);
//            $this->assertEquals($originalData, $returnedData);
//            $this->_commonStorage->deleteItem($dummyNameText);
//            $this->_wait();
//
//            $this->assertFalse($this->_commonStorage->fetchItem($dummyNameText));
//        } catch (Exception $e) {
//            try {
//                $this->_commonStorage->deleteItem($dummyNameText);
//            } catch (\Zend\Cloud\StorageService\Exception $ignoreMe) {
//            }
//            throw $e;
//        }
//    }
//
//	/**
//     * Test fetch item
//     *
//     * @return void
//     */
//    public function testFetchItemStream()
//    {
//        // TODO: Add support for streaming fetch
//        return $this->markTestIncomplete('Cloud API doesn\'t support streamed fetches yet');
//        $dummyNameText   = null;
//        $dummyNameStream = null;
//        try {
//            $originalFilename = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files/data/dummy_data.txt');
//            $dummyNameStream  = $this->_dummyNamePrefix . 'ForFetchStream';
//            $stream = fopen($originalFilename, 'r');
//            $this->_clobberItem($stream, $dummyNameStream);
//            $this->_wait();
//
//            $returnedData = $this->_commonStorage->fetchItem($dummyNameStream);
//            $this->assertEquals(file_get_contents($originalFilename), $returnedData);
//            $this->_commonStorage->deleteItem($dummyNameStream);
//        } catch (Exception $e) {
//            try {
//                $this->_commonStorage->deleteItem($dummyNameStream);
//            } catch (\Zend\Cloud\StorageService\Exception $ignoreMe) {
//            }
//            throw $e;
//        }
//    }
//
//    /**
//     * Test store item
//     *
//     * @return void
//     */
//    public function testStoreItemText()
//    {
//        $dummyNameText = null;
//        try {
//            // Test string data
//            $originalData  = $this->_dummyDataPrefix . 'StoreItem';
//            $dummyNameText = $this->_dummyNamePrefix . 'ForStoreText';
//            $this->_clobberItem($originalData, $dummyNameText);
//            $this->_wait();
//
//            $returnedData = $this->_commonStorage->fetchItem($dummyNameText);
//            $this->assertEquals($originalData, $returnedData);
//            $this->_commonStorage->deleteItem($dummyNameText);
//        } catch (Exception $e) {
//            try {
//                $this->_commonStorage->deleteItem($dummyNameText);
//            } catch (\Zend\Cloud\StorageService\Exception $ignoreMe) {
//            }
//            throw $e;
//        }
//    }
//
//	/**
//     * Test store item
//     *
//     * @return void
//     */
//    public function testStoreItemStream()
//    {
//        $dummyNameStream = $this->_dummyNamePrefix . 'ForStoreStream';
//        try {
//            // Test stream data
//            $originalFilename = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files/data/dummy_data.txt');
//            $stream = fopen($originalFilename, 'r');
//            $this->_commonStorage->storeItem($dummyNameStream, $stream);
//            $this->_wait();
//
//            $returnedData = $this->_commonStorage->fetchItem($dummyNameStream);
//            $this->assertEquals(file_get_contents($originalFilename), $returnedData);
//            $this->_commonStorage->deleteItem($dummyNameStream);
//        } catch (Exception $e) {
//            try {
//                $this->_commonStorage->deleteItem($dummyNameStream);
//            } catch (\Zend\Cloud\StorageService\Exception $ignoreMe) {
//            }
//            throw $e;
//        }
//    }
//
//    /**
//     * Test delete item
//     *
//     * @return void
//     */
//    public function testDeleteItem()
//    {
//        $dummyName = $this->_dummyNamePrefix . 'ForDelete';
//        try {
//            // Test string data
//            $originalData = $this->_dummyDataPrefix . 'DeleteItem';
//            $this->_clobberItem($originalData, $dummyName);
//            $this->_wait();
//
//            $returnedData = $this->_commonStorage->fetchItem($dummyName);
//            $this->assertEquals($originalData, $returnedData);
//            $this->_wait();
//
//            $this->_commonStorage->deleteItem($dummyName);
//            $this->_wait();
//
//            $this->assertFalse($this->_commonStorage->fetchItem($dummyName));
//        } catch (Exception $e) {
//            try {
//                $this->_commonStorage->deleteItem($dummyName);
//            } catch (\Zend\Cloud\StorageService\Exception $ignorme) {
//            }
//            throw $e;
//        }
//    }
//
//    /**
//     * Test copy item
//     *
//     * @return void
//     */
//    public function testCopyItem()
//    {
//        $this->markTestSkipped('This test should be re-enabled when the semantics of "copy" change');
//        try {
//            // Test string data
//            $originalData = $this->_dummyDataPrefix . 'CopyItem';
//            $dummyName1 = $this->_dummyNamePrefix . 'ForCopy1';
//            $dummyName2 = $this->_dummyNamePrefix . 'ForCopy2';
//            $this->_clobberItem($originalData, $dummyName1);
//            $this->_wait();
//
//            $returnedData = $this->_commonStorage->fetchItem($dummyName1);
//            $this->assertEquals($originalData, $returnedData);
//            $this->_wait();
//
//            $this->_commonStorage->copyItem($dummyName1, $dummyName2);
//            $copiedData = $this->_commonStorage->fetchItem($dummyName2);
//            $this->assertEquals($originalData, $copiedData);
//            $this->_commonStorage->deleteItem($dummyName1);
//            $this->_commonStorage->fetchItem($dummyName1);
//            $this->_commonStorage->deleteItem($dummyName2);
//            $this->_commonStorage->fetchItem($dummyName2);
//        } catch (Exception $e) {
//            try {
//                $this->_commonStorage->deleteItem($dummyName1);
//                $this->_commonStorage->deleteItem($dummyName2);
//            } catch (\Zend\Cloud\StorageService\Exception\ExceptionInterface $ignoreme) {
//            }
//            throw $e;
//        }
//    }
//
//	/**
//     * Test move item
//     *
//     * @return void
//     */
//    public function testMoveItem()
//    {
//        $this->markTestSkipped('This test should be re-enabled when the semantics of "move" change');
//
//        try {
//            // Test string data
//            $originalData = $this->_dummyDataPrefix . 'MoveItem';
//            $dummyName1 = $this->_dummyNamePrefix . 'ForMove1';
//            $dummyName2 = $this->_dummyNamePrefix . 'ForMove2';
//            $this->_clobberItem($originalData, $dummyName1);
//            $this->_wait();
//
//            $this->_commonStorage->moveItem($dummyName1, $dummyName2);
//            $this->_wait();
//
//            $movedData = $this->_commonStorage->fetchItem($dummyName2);
//            $this->assertEquals($originalData, $movedData);
//            $this->assertFalse($this->_commonStorage->fetchItem($dummyName1));
//            $this->_commonStorage->deleteItem($dummyName2);
//            $this->assertFalse($this->_commonStorage->fetchItem($dummyName2));
//        } catch (Exception $e) {
//            try {
//                $this->_commonStorage->deleteItem($dummyName1);
//                $this->_commonStorage->deleteItem($dummyName2);
//            } catch (\Zend\Cloud\StorageService\Exception\ExceptionInterface $ignoreme) {
//            }
//            throw $e;
//        }
//    }
//
//	/**
//     * Test fetch metadata
//     *
//     * @return void
//     */
//    public function testFetchMetadata()
//    {
//        try {
//            // Test string data
//            $data = $this->_dummyDataPrefix . 'FetchMetadata';
//            $dummyName = $this->_dummyNamePrefix . 'ForMetadata';
//            $this->_clobberItem($data, $dummyName);
//            $this->_wait();
//
//            $this->_commonStorage->storeMetadata($dummyName, array('zend' => 'zend'));
//            $this->_wait();
//
//            // Hopefully we can assert more about the metadata in the future :/
//            $this->assertTrue(is_array($this->_commonStorage->fetchMetadata($dummyName)));
//            $this->_commonStorage->deleteItem($dummyName);
//        } catch (Exception $e) {
//            try {
//                $this->_commonStorage->deleteItem($dummyName);
//            } catch (\Zend\Cloud\StorageService\Exception\ExceptionInterface $ignoreme) {
//            }
//            throw $e;
//        }
//    }

	/**
     * Test list items
     *
     * @return void
     */
    public function testListItems()
    {
        $dummyName1 = null;
        $dummyName2 = null;
        try {

            $dummyName1 = $this->_dummyNamePrefix . 'ForListItem1';
            $dummyData1 = $this->_dummyDataPrefix . 'Item1';
            $this->_clobberItem($dummyData1, $dummyName1);

            $dummyName2 = $this->_dummyNamePrefix . 'ForListItem2';
            $dummyData2 = $this->_dummyDataPrefix . 'Item2';
            $this->_clobberItem($dummyData2, $dummyName2);
            $this->_wait();

            $objects = $this->_commonStorage->listItems('');

            $this->assertEquals(2, sizeof($objects));

            // PHPUnit does an identical comparison for assertContains(), so we just
            // use assertTrue and in_array()
            $this->assertTrue(in_array($dummyName1, $objects));
            $this->assertTrue(in_array($dummyName2, $objects));

            $this->_commonStorage->deleteItem($dummyName1);
            $this->_commonStorage->deleteItem($dummyName2);
        } catch (Exception $e) {
            try {
                $this->_commonStorage->deleteItem($dummyName1);
                $this->_commonStorage->deleteItem($dummyName2);
            } catch (\Zend\Cloud\StorageService\Exception $ignoreme) {
            }
            throw $e;
        }
    }

    protected function _wait()
    {
        sleep($this->_waitPeriod);
    }

    /**
     * Put given item at given path
     *
     * Removes old item if it was stored there.
     *
     * @param string $data Data item to place there
     * @param string $path Path to write
     */
    protected function _clobberItem($data, $path)
    {
        if($this->_commonStorage->fetchItem($path)) {
            $this->_commonStorage->deleteItem($path);
        }
        $this->_wait();
        $this->_commonStorage->storeItem($path, $data);
    }

    /**
     * Get adapter configuration for concrete test
     * @returns \Zend\Config\Config
     */
    abstract protected function _getConfig();
}
