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

use Zend\Config\Factory as ConfigFactory,
    Zend\Cloud\StorageService\Factory,
    Zend\Cloud\StorageService\Adapter\FileSystem,
    Zend\Cloud\StorageService\Adapter\Nirvanix,
    Zend\Cloud\StorageService\Adapter\S3,
    //Zend\Cloud\StorageService\Adapter\WindowsAzure,
    Zend\Http\Client\Adapter\Test as HttpClientTest,
    Zend\Http\Response as HttpResponse,
    PHPUnit_Framework_TestCase as PHPUnitTestCase;

/**
 * Test class for \Zend\Cloud\StorageService\Factory
 *
 * @category   Zend
 * @package    Zend_Cloud_StorageService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cloud
 */
class FactoryTest extends PHPUnitTestCase
{
    public function testGetStorageAdapterKey()
    {
        $this->assertTrue(is_string(Factory::STORAGE_ADAPTER_KEY));
    }

    public function testGetAdapterWithConfig()
    {
        $httptest = new HttpClientTest();

        // Nirvanix adapter
        $nirvanixConfig = ConfigFactory::fromFile(realpath(dirname(__FILE__) . '/_files/config/nirvanix.ini'), true);
        $nirvanixConfig = $nirvanixConfig->toArray();
        $nirvanixConfig[Nirvanix::HTTP_ADAPTER] = $httptest;

        $doc = new \DOMDocument('1.0', 'utf-8');
        $root = $doc->createElement('Response');
        $responseCode = $doc->createElement('ResponseCode', 0);
        $sessionTok   = $doc->createElement('SessionToken', '54592180-7060-4D4B-BC74-2566F4B2F943');
        $root->appendChild($responseCode);
        $root->appendChild($sessionTok);
        $doc->appendChild($root);
        $body = $doc->saveXML();
        
        $resp = HttpResponse::fromString("HTTP/1.1 200 OK\nContent-type: text/xml;charset=UTF-8\nDate: 0\n\n".$body);
        
        $httptest->setResponse($resp);
        $nirvanixAdapter = Factory::getAdapter($nirvanixConfig);
        $this->assertEquals('Zend\Cloud\StorageService\Adapter\Nirvanix', get_class($nirvanixAdapter));

        // S3 adapter
        $s3Config = ConfigFactory::fromFile(realpath(dirname(__FILE__) . '/_files/config/s3.ini'), true);
        $s3Adapter = Factory::getAdapter($s3Config);
        $this->assertEquals('Zend\Cloud\StorageService\Adapter\S3', get_class($s3Adapter));

        // file system adapter
        $fileSystemConfig = ConfigFactory::fromFile(realpath(dirname(__FILE__) . '/_files/config/filesystem.ini'), true);
        $fileSystemAdapter = Factory::getAdapter($fileSystemConfig);
        $this->assertEquals('Zend\Cloud\StorageService\Adapter\FileSystem', get_class($fileSystemAdapter));

        // Azure adapter
        /*
        $azureConfig    = ConfigFactory::fromFile(realpath(dirname(__FILE__) . '/_files/config/windowsazure.ini'), true);
        $azureConfig    = $azureConfig->toArray();
        $azureContainer = $azureConfig[WindowsAzure::CONTAINER];
        $azureConfig[WindowsAzure::HTTP_ADAPTER] = $httptest;
        $q = "?";

        $doc = new \DOMDocument('1.0', 'utf-8');
        $root = $doc->createElement('EnumerationResults');
        $acctName = $doc->createAttribute('AccountName');
        $acctName->value = 'http://myaccount.blob.core.windows.net';
        $root->appendChild($acctName);
        $maxResults     = $doc->createElement('MaxResults', 1);
        $containers     = $doc->createElement('Containers');
        $container      = $doc->createElement('Container');
        $containerName  = $doc->createElement('Name', $azureContainer);
        $container->appendChild($containerName);
        $containers->appendChild($container);
        $root->appendChild($maxResults);
        $root->appendChild($containers);
        $doc->appendChild($root);
        $body = $doc->saveXML();

        $resp = HttpResponse::fromString("HTTP/1.1 200 OK\nContent-type: text/xml;charset=UTF-8\nx-ms-request-id: 0\n\n".$body);

        $httptest->setResponse($resp);
        $azureAdapter = Factory::getAdapter($azureConfig);
        $this->assertEquals('Zend\Cloud\StorageService\Adapter\WindowsAzure', get_class($azureAdapter));
         * 
         */
    }

    public function testGetAdapterWithArray()
    {
        // No need to overdo it; we'll test the array config with just one adapter.
        $fileSystemConfig = array(
            Factory::STORAGE_ADAPTER_KEY => 'Zend\Cloud\StorageService\Adapter\FileSystem',
            FileSystem::LOCAL_DIRECTORY  => dirname(__FILE__) ."/_files/data",
        );
        $fileSystemAdapter = Factory::getAdapter($fileSystemConfig);
        $this->assertEquals('Zend\Cloud\StorageService\Adapter\FileSystem', get_class($fileSystemAdapter));
    }
}
