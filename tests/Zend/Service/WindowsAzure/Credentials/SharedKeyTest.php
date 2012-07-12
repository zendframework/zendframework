<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\WindowsAzure\Credentials;

use Zend\Service\WindowsAzure\Credentials\SharedKey;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
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
