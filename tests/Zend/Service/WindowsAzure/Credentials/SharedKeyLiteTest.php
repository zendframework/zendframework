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
 * @version    $Id: SharedKeyCredentialsTest.php 14561 2009-05-07 08:05:12Z unknown $
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helpers
 */

/**
 * @see Zend_Service_WindowsAzure_Credentials_SharedKeyLite 
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_WindowsAzure_SharedKeyLiteCredentialsTest::main');
}

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @version    $Id: SharedKeyCredentialsTest.php 14561 2009-05-07 08:05:12Z unknown $
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_Credentials_SharedKeyLiteTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Service_WindowsAzure_Credentials_SharedKeyLiteTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Test signing for devstore with root path
     */
    public function testSignForDevstoreWithRootPath()
    {
        $credentials = new Zend_Service_WindowsAzure_Credentials_SharedKeyLite(Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_ACCOUNT, Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );
                          
        $this->assertType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite devstoreaccount1:iRQpXGzlMRb1A57bkcryX7Bg/3Uf5YOfNCG+XIingJI=", $signedHeaders["Authorization"]);
    }
    
    /**
     * Test signing for devstore with other path
     */
    public function testSignForDevstoreWithOtherPath()
    {
        $credentials = new Zend_Service_WindowsAzure_Credentials_SharedKeyLite(Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_ACCOUNT, Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/test',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );
  
        $this->assertType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite devstoreaccount1:MsC5SIbFB4M4UZd83CiMaL8ibUhaS5H9CcJBJpsnWqo=", $signedHeaders["Authorization"]);
    }
    
    /**
     * Test signing for devstore with query string
     */
    public function testSignForDevstoreWithQueryString()
    {
        $credentials = new Zend_Service_WindowsAzure_Credentials_SharedKeyLite(Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_ACCOUNT, Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_KEY, true);
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '?test=true',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );
  
        $this->assertType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite devstoreaccount1:iRQpXGzlMRb1A57bkcryX7Bg/3Uf5YOfNCG+XIingJI=", $signedHeaders["Authorization"]);
    }
    
    /**
     * Test signing for production with root path
     */
    public function testSignForProductionWithRootPath()
    {
        $credentials = new Zend_Service_WindowsAzure_Credentials_SharedKeyLite('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );
                          
        $this->assertType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite testing:vZdOn/j0gW5FG0kAUG9NhSBO9eBjZqfe6RwALPYUtqU=", $signedHeaders["Authorization"]);
    }
    
    /**
     * Test signing for production with other path
     */
    public function testSignForProductionWithOtherPath()
    {
        $credentials = new Zend_Service_WindowsAzure_Credentials_SharedKeyLite('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/test',
                              '',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );
  
        $this->assertType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite testing:HJTSiRDtMsQVsFVispSHkcODeFykLO+WEuOepwmh51o=", $signedHeaders["Authorization"]);
    }
    
    /**
     * Test signing for production with query string
     */
    public function testSignForProductionWithQueryString()
    {
        $credentials = new Zend_Service_WindowsAzure_Credentials_SharedKeyLite('testing', 'abcdefg');
        $signedHeaders = $credentials->signRequestHeaders(
                              'GET',
                              '/',
                              '?test=true',
                              array("x-ms-date" => "Wed, 29 Apr 2009 13:12:47 GMT"),
                              false
                          );
  
        $this->assertType('array', $signedHeaders);
        $this->assertEquals(2, count($signedHeaders));
        $this->assertEquals("SharedKeyLite testing:vZdOn/j0gW5FG0kAUG9NhSBO9eBjZqfe6RwALPYUtqU=", $signedHeaders["Authorization"]);
    }
}

// Call Zend_Service_WindowsAzure_Credentials_SharedKeyLiteTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Service_WindowsAzure_Credentials_SharedKeyLiteTest::main") {
    Zend_Service_WindowsAzure_Credentials_SharedKeyLiteTest::main();
}
