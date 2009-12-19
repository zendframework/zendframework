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
 * @version    $Id: BlobStorageSharedAccessTest.php 25258 2009-08-14 08:40:41Z unknown $
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_WindowsAzure_BlobStorageSharedAccessTest::main');
}

require_once 'Zend/Service/WindowsAzure/Storage/Blob.php';
require_once 'Zend/Service/WindowsAzure/Credentials/SharedAccessSignature.php';

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 * @version    $Id: BlobStorageSharedAccessTest.php 25258 2009-08-14 08:40:41Z unknown $
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_BlobStorageSharedAccessTest extends PHPUnit_Framework_TestCase
{
    static $path;
    
    public function __construct()
    {
        self::$path = dirname(__FILE__).'/_files/';
    }
    
    public static function main()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            $suite  = new PHPUnit_Framework_TestSuite("Zend_Service_WindowsAzure_BlobStorageSharedAccessTest");
            $result = PHPUnit_TextUI_TestRunner::run($suite);
        }
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
        for ($i = 1; $i <= self::$uniqId; $i++)
        {
            try { $storageClient->deleteContainer(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOBSA_CONTAINER_PREFIX . $i); } catch (Exception $e) { }
        }
        try { $storageClient->deleteContainer('$root'); } catch (Exception $e) { }
    }

    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNONPROD) {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
            $storageClient->setCredentials(
                new Zend_Service_WindowsAzure_Credentials_SharedAccessSignature(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false)
            );
        } else {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
            $storageClient->setCredentials(
                new Zend_Service_WindowsAzure_Credentials_SharedAccessSignature(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true)
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
            $storageClient = new Zend_Service_WindowsAzure_Storage_Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
        } else {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Blob(TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
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
            } catch (Exception $ex) {
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
            } catch (Exception $ex) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
        }
    }
}

// Call Zend_Service_WindowsAzure_BlobStorageSharedAccessTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Service_WindowsAzure_BlobStorageSharedAccessTest::main") {
    Zend_Service_WindowsAzure_BlobStorageSharedAccessTest::main();
}
