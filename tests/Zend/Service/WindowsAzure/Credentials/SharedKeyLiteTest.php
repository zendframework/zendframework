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

use Zend\Service\WindowsAzure\Credentials\AbstractCredentials;
use Zend\Service\WindowsAzure\Credentials\SharedKeyLite;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SharedKeyLiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test signing for devstore with root path
     */
    public function testSignForDevstoreWithRootPath()
    {
        $credentials = new SharedKeyLite(AbstractCredentials::DEVSTORE_ACCOUNT, AbstractCredentials::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite devstoreaccount1:iRQpXGzlMRb1A57bkcryX7Bg/3Uf5YOfNCG+XIingJI=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for devstore with other path
     */
    public function testSignForDevstoreWithOtherPath()
    {
        $credentials = new SharedKeyLite(AbstractCredentials::DEVSTORE_ACCOUNT, AbstractCredentials::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/test',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite devstoreaccount1:MsC5SIbFB4M4UZd83CiMaL8ibUhaS5H9CcJBJpsnWqo=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for devstore with query string
     */
    public function testSignForDevstoreWithQueryString()
    {
        $credentials = new SharedKeyLite(AbstractCredentials::DEVSTORE_ACCOUNT, AbstractCredentials::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '?test=true',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite devstoreaccount1:iRQpXGzlMRb1A57bkcryX7Bg/3Uf5YOfNCG+XIingJI=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for production with root path
     */
    public function testSignForProductionWithRootPath()
    {
        $credentials = new SharedKeyLite('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite testing:vZdOn/j0gW5FG0kAUG9NhSBO9eBjZqfe6RwALPYUtqU=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for production with other path
     */
    public function testSignForProductionWithOtherPath()
    {
        $credentials = new SharedKeyLite('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/test',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite testing:HJTSiRDtMsQVsFVispSHkcODeFykLO+WEuOepwmh51o=", $signedHeaders["Authorization"]);
    }

    /**
     * Test signing for production with query string
     */
    public function testSignForProductionWithQueryString()
    {
        $credentials = new SharedKeyLite('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '?test=true',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );

        $this->assertInternalType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite testing:vZdOn/j0gW5FG0kAUG9NhSBO9eBjZqfe6RwALPYUtqU=", $signedHeaders["Authorization"]);
    }
}
