<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\WindowsAzure;

use Zend\Service\WindowsAzure\Credentials\SharedAccessSignature;
use Zend\Service\WindowsAzure\RetryPolicy\AbstractRetryPolicy;
use Zend\Service\WindowsAzure\Storage\Blob\Blob;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 */
class BlobStorageSharedAccessTest extends \PHPUnit_Framework_TestCase
{
    public static $path;

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
        $storageClient = $this->createAdministrativeStorageInstance();
        for ($i = 1; $i <= self::$uniqId; $i++) {
            try { $storageClient->deleteContainer(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOBSA_CONTAINER_PREFIX . $i); } catch (\Exception $e) { }
        }
        try { $storageClient->deleteContainer('$root'); } catch (\Exception $e) { }
    }

    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNONPROD) {
            $storageClient = new Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, AbstractRetryPolicy::retryN(10, 250));
            $storageClient->setCredentials(
                new SharedAccessSignature(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false)
            );
        } else {
            $storageClient = new Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, AbstractRetryPolicy::retryN(10, 250));
            $storageClient->setCredentials(
                new SharedAccessSignature(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true)
            );
        }

        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY) {
            $storageClient->setProxy(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_PORT, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_CREDENTIALS);
        }

        return $storageClient;
    }

    protected function createAdministrativeStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNONPROD) {
            $storageClient = new Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, AbstractRetryPolicy::retryN(10, 250));
        } else {
            $storageClient = new Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, AbstractRetryPolicy::retryN(10, 250));
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
        return TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOBSA_CONTAINER_PREFIX . self::$uniqId;
    }

    /**
     * Test shared access, only write
     */
    public function testSharedAccess_OnlyWrite()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();

            // Account owner performs this part
            $administrativeStorageClient = $this->createAdministrativeStorageInstance();
            $administrativeStorageClient->createContainer($containerName);

            $sharedAccessUrl = $administrativeStorageClient->generateSharedAccessUrl(
                $containerName,
                '',
                'c',
                'w',
                $administrativeStorageClient->isoDate(time() - 500),
                $administrativeStorageClient->isoDate(time() + 3000)
            );


            // Reduced permissions user performs this part
            $storageClient = $this->createStorageInstance();
            $credentials = $storageClient->getCredentials();
            $credentials->setPermissionSet(array(
                $sharedAccessUrl
            ));

            $result = $storageClient->putBlob($containerName, 'images/WindowsAzure.gif', self::$path . 'WindowsAzure.gif');

            $this->assertEquals($containerName, $result->Container);
            $this->assertEquals('images/WindowsAzure.gif', $result->Name);



            // Now make sure reduced permissions user can not view the uploaded blob
            $exceptionThrown = false;
            try {
                $storageClient->getBlob($containerName, 'images/WindowsAzure.gif', self::$path . 'WindowsAzure.gif');
            } catch (\Exception $ex) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
        }
    }

    /**
     * Test different accounts
     */
    public function testDifferentAccounts()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $containerName = $this->generateName();

            // Account owner performs this part
            $administrativeStorageClient = $this->createAdministrativeStorageInstance();
            $administrativeStorageClient->createContainer($containerName);

            $sharedAccessUrl1 = $administrativeStorageClient->generateSharedAccessUrl(
                $containerName,
                '',
                'c',
                'w',
                $administrativeStorageClient->isoDate(time() - 500),
                $administrativeStorageClient->isoDate(time() + 3000)
            );
            $sharedAccessUrl2 = str_replace($administrativeStorageClient->getAccountName(), 'bogusaccount', $sharedAccessUrl1);


            // Reduced permissions user performs this part and should fail,
            // because different accounts have been used
            $storageClient = $this->createStorageInstance();
            $credentials = $storageClient->getCredentials();

            $exceptionThrown = false;
            try {
                $credentials->setPermissionSet(array(
                    $sharedAccessUrl1,
                    $sharedAccessUrl2
                ));
            } catch (\Exception $ex) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
        }
    }
}
