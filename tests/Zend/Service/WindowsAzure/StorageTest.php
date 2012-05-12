<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace ZendTest\Service\WindowsAzure;

use Zend\Service\WindowsAzure\Storage\Storage;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test constructor for devstore
     */
    public function testConstructorForDevstore()
    {
        $storage = new Storage();
        $this->assertEquals('http://127.0.0.1:10000/devstoreaccount1', $storage->getBaseUrl());
    }

    /**
     * Test constructor for production
     */
    public function testConstructorForProduction()
    {
        $storage = new Storage(Storage::URL_CLOUD_BLOB, 'testing', '');
        $this->assertEquals('http://testing.blob.core.windows.net', $storage->getBaseUrl());
    }
}
