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
 * @see Zend_Service_WindowsAzure_Storage
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
class Zend_Service_WindowsAzure_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test constructor for devstore
     */
    public function testConstructorForDevstore()
    {
        $storage = new Zend_Service_WindowsAzure_Storage();
        $this->assertEquals('http://127.0.0.1:10000/devstoreaccount1', $storage->getBaseUrl());
    }
    
    /**
     * Test constructor for production
     */
    public function testConstructorForProduction()
    {
        $storage = new Zend_Service_WindowsAzure_Storage(Zend_Service_WindowsAzure_Storage::URL_CLOUD_BLOB, 'testing', '');
        $this->assertEquals('http://testing.blob.core.windows.net', $storage->getBaseUrl());
    }
}
