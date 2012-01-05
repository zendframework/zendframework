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
 * @see Zend_Service_WindowsAzure_Storage_Blob 
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
class Zend_Service_WindowsAzure_BlobStreamTest extends PHPUnit_Framework_TestCase
{
    static $path;
    
    public function __construct()
    {
        self::$path = __DIR__.'/_files/';
    }
    
    /**
     * Test setup
     */
    protected function setUp()
    {
    }
    
    /**
     * Test teardown
     */
    protected function tearDown()
    {
        $storageClient = $this->createStorageInstance();
        for ($i = 1; $i <= self::$uniqId; $i++)
        {
            try { $storageClient->deleteContainer(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOBSTREAM_CONTAINER_PREFIX . $i); } catch (Exception $e) { }
            try { $storageClient->unregisterStreamWrapper('azure'); } catch (Exception $e) { }
        }
    }

    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNONPROD) {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, Zend_Service_WindowsAzure_RetryPolicy_AbstractRetryPolicy::retryN(10, 250));
        } else {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, Zend_Service_WindowsAzure_RetryPolicy_AbstractRetryPolicy::retryN(10, 250));
        }
        
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY) {
            $storageClient->setProxy(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_PORT, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_CREDENTIALS);
        }

        return $storageClient;
    }
    
    protected static $uniqId = 0;
    
    protected function generateName()
    {
        self::$uniqId++;
        return TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOBSTREAM_CONTAINER_PREFIX . self::$uniqId;
    }
    
    /**
     * Test read file
     */
    public function testReadFile()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();
            $fileName = 'azure://' . $containerName . '/test.txt';
            
            $storageClient = $this->createStorageInstance();
            $storageClient->registerStreamWrapper();
            
            $fh = fopen($fileName, 'w');
            fwrite($fh, "Hello world!");
            fclose($fh);
            
            $result = file_get_contents($fileName);
            
            $storageClient->unregisterStreamWrapper();
            
            $this->assertEquals('Hello world!', $result);
        }
    }
    
    /**
     * Test write file
     */
    public function testWriteFile()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();
            $fileName = 'azure://' . $containerName . '/test.txt';
            
            $storageClient = $this->createStorageInstance();
            $storageClient->registerStreamWrapper();
            
            $fh = fopen($fileName, 'w');
            fwrite($fh, "Hello world!");
            fclose($fh);
            
            $storageClient->unregisterStreamWrapper();
            
            $instance = $storageClient->getBlobInstance($containerName, 'test.txt');
            $this->assertEquals('test.txt', $instance->Name);
        }
    }
    
    /**
     * Test unlink file
     */
    public function testUnlinkFile()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();
            $fileName = 'azure://' . $containerName . '/test.txt';
            
            $storageClient = $this->createStorageInstance();
            $storageClient->registerStreamWrapper();
            
            $fh = fopen($fileName, 'w');
            fwrite($fh, "Hello world!");
            fclose($fh);
            
            unlink($fileName);
            
            $storageClient->unregisterStreamWrapper();
            
            $result = $storageClient->listBlobs($containerName);
            $this->assertEquals(0, count($result));
        }
    }
    
    /**
     * Test copy file
     */
    public function testCopyFile()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();
            $sourceFileName = 'azure://' . $containerName . '/test.txt';
            $destinationFileName = 'azure://' . $containerName . '/test2.txt';
            
            $storageClient = $this->createStorageInstance();
            $storageClient->registerStreamWrapper();
            
            $fh = fopen($sourceFileName, 'w');
            fwrite($fh, "Hello world!");
            fclose($fh);

            copy($sourceFileName, $destinationFileName);

            $storageClient->unregisterStreamWrapper();
            
            $instance = $storageClient->getBlobInstance($containerName, 'test2.txt');
            $this->assertEquals('test2.txt', $instance->Name);
        }
    }
    
    /**
     * Test rename file
     */
    public function testRenameFile()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();
            $sourceFileName = 'azure://' . $containerName . '/test.txt';
            $destinationFileName = 'azure://' . $containerName . '/test2.txt';
            
            $storageClient = $this->createStorageInstance();
            $storageClient->registerStreamWrapper();
            
            $fh = fopen($sourceFileName, 'w');
            fwrite($fh, "Hello world!");
            fclose($fh);
            
            rename($sourceFileName, $destinationFileName);
            
            $storageClient->unregisterStreamWrapper();
            
            $instance = $storageClient->getBlobInstance($containerName, 'test2.txt');
            $this->assertEquals('test2.txt', $instance->Name);
        }
    }
    
    /**
     * Test mkdir
     */
    public function testMkdir()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();
            
            $storageClient = $this->createStorageInstance();
            $storageClient->registerStreamWrapper();
            
            mkdir('azure://' . $containerName);
            
            $storageClient->unregisterStreamWrapper();
            
            $result = $storageClient->listContainers();
            
            $this->assertEquals(1, count($result));
            $this->assertEquals($containerName, $result[0]->Name);
        }
    }
    
    /**
     * Test rmdir
     */
    public function testRmdir()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();
            
            $storageClient = $this->createStorageInstance();
            $storageClient->registerStreamWrapper();
            
            mkdir('azure://' . $containerName);
            rmdir('azure://' . $containerName);
            
            $storageClient->unregisterStreamWrapper();
            
            $result = $storageClient->listContainers();
            
            $this->assertEquals(0, count($result));
        }
    } 
    
    /**
     * Test opendir
     */
    public function testOpendir()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createContainer($containerName);
            
            $storageClient->putBlob($containerName, 'images/WindowsAzure1.gif', self::$path . 'WindowsAzure.gif');
            $storageClient->putBlob($containerName, 'images/WindowsAzure2.gif', self::$path . 'WindowsAzure.gif');
            $storageClient->putBlob($containerName, 'images/WindowsAzure3.gif', self::$path . 'WindowsAzure.gif');
            $storageClient->putBlob($containerName, 'images/WindowsAzure4.gif', self::$path . 'WindowsAzure.gif');
            $storageClient->putBlob($containerName, 'images/WindowsAzure5.gif', self::$path . 'WindowsAzure.gif');
            
            $result1 = $storageClient->listBlobs($containerName);
  
            $storageClient->registerStreamWrapper();
            
            $result2 = array();
            if ($handle = opendir('azure://' . $containerName)) {
                while (false !== ($file = readdir($handle))) {
                    $result2[] = $file;
                }
                closedir($handle);
            }
            
            $storageClient->unregisterStreamWrapper();
            
            $result = $storageClient->listContainers();
            
            $this->assertEquals(count($result1), count($result2));
        }
    } 
}
