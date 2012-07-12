<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendTest\Cloud\StorageService\Adapter;

use ZendTest\Cloud\StorageService\TestCase;
use Zend\Cloud\StorageService\Adapter\FileSystem;
use Zend\Config\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 */
class FileSystemTest extends TestCase
{
    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        // No need to wait
        $this->_waitPeriod = 0;
        $path = $this->_config->local_directory;

        // If the test directory exists, remove it and replace it
        if (file_exists($path)) {
            $this->_rmRecursive($path);
        }
        mkdir($path, 0755);
    }

    public function testGetClient()
    {
        $this->assertTrue(is_string($this->_commonStorage->getClient()));
    }

    public function testNoParams()
    {
        $this->markTestIncomplete('No config params needed for FileSystem');
    }

    // TODO: Create a custom test for FileSystem that checks fetchMetadata() with file system MD.
    public function testFetchMetadata()
    {
        $this->markTestIncomplete("FileSystem doesn't support writable metadata.");
    }

    public function testStoreMetadata()
    {
        $this->markTestIncomplete("FileSystem doesn't support writable metadata.");
    }

    public function testDeleteMetadata()
    {
        $this->markTestIncomplete("FileSystem doesn't support writable metadata.");
    }

    /**
     * Tears down this test case
     *
     * @return void
     */
    public function tearDown()
    {
        $path = $this->_config->local_directory;

        // If the test directory exists, remove it
        if(file_exists($path)) {
            $this->_rmRecursive($path);
        }

        parent::tearDown();
    }

    protected function _rmRecursive($path)
    {
        // Tidy up the path
        $path = realpath($path);

        if (!file_exists($path)) {
            return true;
        } elseif (!is_dir($path)) {
            return unlink($path);
        } else {
            foreach (scandir($path) as $item) {
                if (!($item == '.' || $item == '..')) {
                    $this->_rmRecursive($item);
                }
            }
            return rmdir($path);
        }
    }

    protected function _getConfig()
    {
        $config = new Config(array(
            \Zend\Cloud\StorageService\Factory::STORAGE_ADAPTER_KEY        => 'Zend\Cloud\StorageService\Adapter\Filesystem',
            \Zend\Cloud\StorageService\Adapter\FileSystem::LOCAL_DIRECTORY => dirname(__FILE__) . '/../_files/data/FileSystemTest',
        ));

        return $config;
    }
}
