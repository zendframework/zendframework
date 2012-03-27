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
class Zend_Service_WindowsAzure_BlobStorageTest extends PHPUnit_Framework_TestCase
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
        if (!TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $this->markTestSkipped('Windows Azure Tests disabled');
        }
    }

    /**
     * Test teardown
     */
    protected function tearDown()
    {
        $storageClient = $this->createStorageInstance();
        for ($i = 1; $i <= self::$uniqId; $i++)
        {
            try { $storageClient->deleteContainer(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_CONTAINER_PREFIX . $i); } catch (Exception $e) { }
        }
        try { $storageClient->deleteContainer('$root'); } catch (Exception $e) { }
    }

    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNONPROD)
        {
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
        return TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_CONTAINER_PREFIX . self::$uniqId;
    }

    /**
     * Test container exists
     */
    public function testContainerExists()
    {
        $containerName1 = $this->generateName();
        $containerName2 = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName1);
        $storageClient->createContainer($containerName2);

        $result = $storageClient->containerExists($containerName1);
        $this->assertTrue($result);

        $result = $storageClient->containerExists(md5(time()));
        $this->assertFalse($result);
    }

    /**
     * Test blob exists
     */
    public function testBlobExists()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);
        $storageClient->putBlob($containerName, 'WindowsAzure1.gif', self::$path . 'WindowsAzure.gif');
        $storageClient->putBlob($containerName, 'WindowsAzure2.gif', self::$path . 'WindowsAzure.gif');

        $result = $storageClient->blobExists($containerName, 'WindowsAzure1.gif');
        $this->assertTrue($result);

        $result = $storageClient->blobExists($containerName, md5(time()));
        $this->assertFalse($result);
    }

    /**
     * Test create container
     */
    public function testCreateContainer()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $result = $storageClient->createContainer($containerName);
        $this->assertEquals($containerName, $result->Name);
    }

    /**
     * Test get container acl
     */
    public function testGetContainerAcl()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);
        $acl = $storageClient->getContainerAcl($containerName);
        $this->assertEquals(Zend_Service_WindowsAzure_Storage_Blob::ACL_PRIVATE, $acl);
    }

    /**
     * Test set container acl
     */
    public function testSetContainerAcl()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);

        $storageClient->setContainerAcl($containerName, Zend_Service_WindowsAzure_Storage_Blob::ACL_PUBLIC);
        $acl = $storageClient->getContainerAcl($containerName);

        $this->assertEquals(Zend_Service_WindowsAzure_Storage_Blob::ACL_PUBLIC, $acl);
    }

    /**
     * Test set container acl advanced
     */
    public function testSetContainerAclAdvanced()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);

        $storageClient->setContainerAcl(
            $containerName,
            Zend_Service_WindowsAzure_Storage_Blob::ACL_PRIVATE,
            array(
                new Zend_Service_WindowsAzure_Storage_SignedIdentifier('ABCDEF', '2009-10-10', '2009-10-11', 'r')
            )
        );
        $acl = $storageClient->getContainerAcl($containerName, true);

        $this->assertEquals(1, count($acl));
    }

    /**
     * Test set container metadata
     */
    public function testSetContainerMetadata()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);

        $storageClient->setContainerMetadata($containerName, array(
            'createdby' => 'PHPAzure',
        ));

        $metadata = $storageClient->getContainerMetadata($containerName);
        $this->assertEquals('PHPAzure', $metadata['createdby']);
    }

    /**
     * Test list containers
     */
    public function testListContainers()
    {
        $containerName1 = 'testlist1';
        $containerName2 = 'testlist2';
        $containerName3 = 'testlist3';
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName1);
        $storageClient->createContainer($containerName2);
        $storageClient->createContainer($containerName3);
        $result1 = $storageClient->listContainers('testlist');
        $result2 = $storageClient->listContainers('testlist', 1);

        // cleanup first
        $storageClient->deleteContainer($containerName1);
        $storageClient->deleteContainer($containerName2);
        $storageClient->deleteContainer($containerName3);

        $this->assertEquals(3, count($result1));
        $this->assertEquals($containerName2, $result1[1]->Name);

        $this->assertEquals(1, count($result2));
    }

    /**
     * Test put blob
     */
    public function testPutBlob()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);
        $result = $storageClient->putBlob($containerName, 'images/WindowsAzure.gif', self::$path . 'WindowsAzure.gif');

        $this->assertEquals($containerName, $result->Container);
        $this->assertEquals('images/WindowsAzure.gif', $result->Name);
    }

    /**
     * Test put large blob
     */
    public function testPutLargeBlob()
    {
        if (!TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNLARGEBLOB) {
            $this->markTestSkipped('Large Blob Test disabled');
        }

        // Create a file > Zend_Service_WindowsAzure_Storage_Blob::MAX_BLOB_SIZE
        $fileName = $this->_createLargeFile();

        // Execute test
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);
        $result = $storageClient->putLargeBlob($containerName, 'LargeFile.txt', $fileName);

        $this->assertEquals($containerName, $result->Container);
        $this->assertEquals('LargeFile.txt', $result->Name);

        // Get block list
        $blockList = $storageClient->getBlockList($containerName, 'LargeFile.txt');
        $this->assertTrue(count($blockList['CommittedBlocks']) > 0);

        // Remove file
        unlink($fileName);
    }

    /**
     * Test get blob
     */
    public function testGetBlob()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);
        $storageClient->putBlob($containerName, 'images/WindowsAzure.gif', self::$path . 'WindowsAzure.gif');

        $fileName = tempnam('', 'tst');
        $storageClient->getBlob($containerName, 'images/WindowsAzure.gif', $fileName);

        $this->assertTrue(file_exists($fileName));
        $this->assertEquals(
            file_get_contents(self::$path . 'WindowsAzure.gif'),
            file_get_contents($fileName)
        );

        // Remove file
        unlink($fileName);
    }

    /**
     * Test set blob metadata
     */
    public function testSetBlobMetadata()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);
        $storageClient->putBlob($containerName, 'images/WindowsAzure.gif', self::$path . 'WindowsAzure.gif');

        $storageClient->setBlobMetadata($containerName, 'images/WindowsAzure.gif', array(
            'createdby' => 'PHPAzure',
        ));

        $metadata = $storageClient->getBlobMetadata($containerName, 'images/WindowsAzure.gif');
        $this->assertEquals('PHPAzure', $metadata['createdby']);
    }

    /**
     * Test delete blob
     */
    public function testDeleteBlob()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);

        $storageClient->putBlob($containerName, 'images/WindowsAzure.gif', self::$path . 'WindowsAzure.gif');
        $storageClient->deleteBlob($containerName, 'images/WindowsAzure.gif');

        $result = $storageClient->listBlobs($containerName);
        $this->assertEquals(0, count($result));
    }

    /**
     * Test list blobs
     */
    public function testListBlobs()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);

        $storageClient->putBlob($containerName, 'images/WindowsAzure1.gif', self::$path . 'WindowsAzure.gif');
        $storageClient->putBlob($containerName, 'images/WindowsAzure2.gif', self::$path . 'WindowsAzure.gif');
        $storageClient->putBlob($containerName, 'images/WindowsAzure3.gif', self::$path . 'WindowsAzure.gif');
        $storageClient->putBlob($containerName, 'images/WindowsAzure4.gif', self::$path . 'WindowsAzure.gif');
        $storageClient->putBlob($containerName, 'images/WindowsAzure5.gif', self::$path . 'WindowsAzure.gif');

        $result1 = $storageClient->listBlobs($containerName);
        $this->assertEquals(5, count($result1));
        $this->assertEquals('images/WindowsAzure5.gif', $result1[4]->Name);

        $result2 = $storageClient->listBlobs($containerName, '', '', 2);
        $this->assertEquals(2, count($result2));
    }

    /**
     * Test copy blob
     */
    public function testCopyBlob()
    {
        $containerName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createContainer($containerName);
        $source = $storageClient->putBlob($containerName, 'images/WindowsAzure.gif', self::$path . 'WindowsAzure.gif');

        $this->assertEquals($containerName, $source->Container);
        $this->assertEquals('images/WindowsAzure.gif', $source->Name);

        $destination = $storageClient->copyBlob($containerName, 'images/WindowsAzure.gif', $containerName, 'images/WindowsAzureCopy.gif');

        $this->assertEquals($containerName, $destination->Container);
        $this->assertEquals('images/WindowsAzureCopy.gif', $destination->Name);
    }

    /**
     * Test root container
     */
    public function testRootContainer()
    {
        $containerName = '$root';
        $storageClient = $this->createStorageInstance();
        $result = $storageClient->createContainer($containerName);
        $this->assertEquals($containerName, $result->Name);

        // ACL
        $storageClient->setContainerAcl($containerName, Zend_Service_WindowsAzure_Storage_Blob::ACL_PUBLIC);
        $acl = $storageClient->getContainerAcl($containerName);

        $this->assertEquals(Zend_Service_WindowsAzure_Storage_Blob::ACL_PUBLIC, $acl);

        // Metadata
        $storageClient->setContainerMetadata($containerName, array(
            'createdby' => 'PHPAzure',
        ));

        $metadata = $storageClient->getContainerMetadata($containerName);
        $this->assertEquals('PHPAzure', $metadata['createdby']);

        // List
        $result = $storageClient->listContainers();
        $this->assertEquals(1, count($result));

        // Put blob
        $result = $storageClient->putBlob($containerName, 'WindowsAzure.gif', self::$path . 'WindowsAzure.gif');

        $this->assertEquals($containerName, $result->Container);
        $this->assertEquals('WindowsAzure.gif', $result->Name);

        // Get blob
        $fileName = tempnam('', 'tst');
        $storageClient->getBlob($containerName, 'WindowsAzure.gif', $fileName);

        $this->assertTrue(file_exists($fileName));
        $this->assertEquals(
            file_get_contents(self::$path . 'WindowsAzure.gif'),
            file_get_contents($fileName)
        );

        // Remove file
        unlink($fileName);

        // Blob metadata
        $storageClient->setBlobMetadata($containerName, 'WindowsAzure.gif', array(
            'createdby' => 'PHPAzure',
        ));

        $metadata = $storageClient->getBlobMetadata($containerName, 'WindowsAzure.gif');
        $this->assertEquals('PHPAzure', $metadata['createdby']);

        // List blobs
        $result = $storageClient->listBlobs($containerName);
        $this->assertEquals(1, count($result));

        // Delete blob
        $storageClient->deleteBlob($containerName, 'WindowsAzure.gif');

        $result = $storageClient->listBlobs($containerName);
        $this->assertEquals(0, count($result));
    }

    /**
     * Create large file
     *
     * @return string Filename
     */
    private function _createLargeFile()
    {
        $fileName = tempnam('', 'tst');
        $fp = fopen($fileName, 'w');
        for ($i = 0; $i < Zend_Service_WindowsAzure_Storage_Blob::MAX_BLOB_SIZE / 1024; $i++)
        {
            fwrite($fp,
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' .
                'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
            );
        }
        fclose($fp);
        return $fileName;
    }
}
