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

namespace ZendTest\Service\WindowsAzure\Credentials;

use Zend\Service\WindowsAzure\Credentials\SharedKey;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SharedKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test signing for devstore with root path
     */
    public function testSignForDevstoreWithRootPath()
    {
        $credentials = new SharedKey(SharedKey::DEVSTORE_ACCOUNT, SharedKey::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKey devstoreaccount1:ijwRTxfJgqvmfdWPSLCpgxfvHpl6Kbbo/qJTzlI7wUw=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for devstore with other path
     */
    public function testSignForDevstoreWithOtherPath()
    {
        $credentials = new SharedKey(SharedKey::DEVSTORE_ACCOUNT, SharedKey::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/test',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKey devstoreaccount1:ZLs/nBsEaoyCqHpqcQUfXO5zIHBTMcrzVaIxwQNBL9k=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for devstore with query string
     */
    public function testSignForDevstoreWithQueryString()
    {
        $credentials = new SharedKey(SharedKey::DEVSTORE_ACCOUNT, SharedKey::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '?test=true',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKey devstoreaccount1:ijwRTxfJgqvmfdWPSLCpgxfvHpl6Kbbo/qJTzlI7wUw=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for production with root path
     */
    public function testSignForProductionWithRootPath()
    {
        $credentials = new SharedKey('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKey testing:TEYBENKs+6laykL+zCxlIbUT9v019rtMWECYwgP/OuU=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for production with other path
     */
    public function testSignForProductionWithOtherPath()
    {
        $credentials = new SharedKey('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/test',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKey testing:d2kcDGCQ603wPuZ3KHbeILtNhIXMXyTNVn2x9d5aF60=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for production with query string
     */
    public function testSignForProductionWithQueryString()
    {
        $credentials = new SharedKey('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '?test=true',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKey testing:TEYBENKs+6laykL+zCxlIbUT9v019rtMWECYwgP/OuU=", $signedHeaders["Authorization"]);
    }
}
