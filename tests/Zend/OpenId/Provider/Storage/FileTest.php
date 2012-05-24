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
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @see Zend\OpenId\Provider\Storage\File
 */

namespace ZendTest\OpenId\Provider\Storage;

use Zend\OpenId,
    Zend\OpenId\Provider\Storage;

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_OpenId
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    const HANDLE   = "d41d8cd98f00b204e9800998ecf8427e";
    const MAC_FUNC = "sha256";
    const SECRET   = "4fa03202081808bd19f92b667a291873";
    const USER     = "test_user";
    const PASSWORD = "01234567890abcdef";
    const SITE1    = "http://www.php.net/";
    const SITE2    = "http://www.yahoo.com/";

    /**
     * testing __construct
     *
     */
    public function testConstruct()
    {
        $tmp = __DIR__."/_files";
        $dir = $tmp . '/openid_provider';
        @rmdir($dir);
        $storage = new Storage\File($dir);
        $this->assertTrue( is_dir($dir) );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        chmod($dir, 0);
        $dir2 = $dir . '/test';
        try {
            $storage = new Storage\File($dir2);
            $ex = null;
        } catch (\Exception $e) {
            $ex = $e;
        }
        $this->assertTrue( $ex instanceof \Zend\OpenId\Exception\ExceptionInterface );
        $this->assertSame( \Zend\OpenId\Exception\ExceptionInterface::ERROR_STORAGE, $ex->getCode() );
        $this->assertContains( 'Cannot access storage directory', $ex->getMessage() );
        chmod($dir, 0777);
        $this->assertFalse( is_dir($dir2) );
        @rmdir($dir);
    }

    /**
     * testing getAssociation
     *
     */
    public function testGetAssociation()
    {
        $expiresIn = time() + 600;
        $storage = new Storage\File(__DIR__."/_files");
        $storage->delAssociation(self::HANDLE);
        $this->assertTrue( $storage->addAssociation(self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        $this->assertTrue( $storage->getAssociation(self::HANDLE, $macFunc, $secret, $expires) );
        $this->assertSame( self::MAC_FUNC, $macFunc );
        $this->assertSame( self::SECRET, $secret );
        $this->assertSame( $expiresIn, $expires );
        $this->assertTrue( $storage->delAssociation(self::HANDLE) );
        $this->assertFalse( $storage->getAssociation(self::HANDLE, $macFunc, $secret, $expires) );

        $tmp = __DIR__."/_files";
        $dir = $tmp . '/openid_consumer';
        @rmdir($dir);
        $storage = new Storage\File($dir);
        $this->assertTrue( is_dir($dir) );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        chmod($dir, 0);
        $this->assertFalse( $storage->addAssociation(self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        chmod($dir, 0777);
        @rmdir($dir);
    }

    /**
     * testing getAssociation
     *
     */
    public function testGetAssociationExpiratin()
    {
        $expiresIn = time() + 1;
        $storage = new Storage\File(__DIR__."/_files");
        $storage->delAssociation(self::HANDLE);
        $this->assertTrue( $storage->addAssociation(self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        sleep(2);
        $this->assertFalse( $storage->getAssociation(self::HANDLE, $macFunc, $secret, $expires) );
    }

    /**
     * testing addUser
     *
     */
    public function testAddUser()
    {
        $storage = new Storage\File(__DIR__."/_files");
        $storage->delUser(self::USER);
        $this->assertTrue( $storage->addUser(self::USER, self::PASSWORD) );
        $this->assertFalse( $storage->addUser(self::USER, self::PASSWORD) );
        $this->assertTrue( $storage->delUser(self::USER) );
        $this->assertTrue( $storage->addUser(self::USER, self::PASSWORD) );
        $this->assertTrue( $storage->delUser(self::USER) );
    }

    /**
     * testing hasUser
     *
     */
    public function testHasUser()
    {
        $storage = new Storage\File(__DIR__."/_files");
        $storage->delUser(self::USER);
        $this->assertTrue( $storage->addUser(self::USER, self::PASSWORD) );
        $this->assertTrue( $storage->hasUser(self::USER) );
        $this->assertTrue( $storage->delUser(self::USER) );
        $this->assertFalse( $storage->hasUser(self::USER) );
    }

    /**
     * testing checkUser
     *
     */
    public function testCheckUser()
    {
        $storage = new Storage\File(__DIR__."/_files");
        $storage->delUser(self::USER);
        $this->assertTrue( $storage->addUser(self::USER, self::PASSWORD) );
        $this->assertTrue( $storage->checkUser(self::USER, self::PASSWORD) );
        $this->assertFalse( $storage->checkUser(self::USER, self::USER) );
        $this->assertTrue( $storage->delUser(self::USER) );
        $this->assertFalse( $storage->checkUser(self::USER, self::PASSWORD) );
    }

    /**
     * testing addSite
     *
     */
    public function testAddSite()
    {
        $storage = new Storage\File(__DIR__."/_files");
        $storage->delUser(self::USER);
        $this->assertTrue( $storage->addUser(self::USER, self::PASSWORD) );
        $this->assertTrue( $storage->addSite(self::USER, self::SITE1, true) );
        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 1, count($trusted) );
        reset($trusted);
        $this->assertSame( self::SITE1, key($trusted) );
        $this->assertSame( true, current($trusted) );
        $this->assertTrue( $storage->delUser(self::USER) );
        $this->assertFalse( $storage->addSite(self::USER, self::SITE1, true) );
        $this->assertTrue( $storage->addUser(self::USER, self::PASSWORD) );
        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 0, count($trusted) );
        $this->assertTrue( $storage->addSite(self::USER, self::SITE1, self::SITE1) );
        $this->assertTrue( $storage->addSite(self::USER, self::SITE2, self::SITE2) );
        $this->assertTrue( $storage->addSite(self::USER, self::SITE1, self::USER) );
        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 2, count($trusted) );
        $this->assertSame( self::USER, $trusted[self::SITE1] );
        $this->assertSame( self::SITE2, $trusted[self::SITE2] );
        $this->assertTrue( $storage->addSite(self::USER, self::SITE2, null) );
        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 1, count($trusted) );
        $this->assertSame( self::USER, $trusted[self::SITE1] );
        $this->assertTrue( $storage->addSite(self::USER, self::SITE1, null) );
        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 0, count($trusted) );
        $this->assertTrue( $storage->delUser(self::USER) );
        $storage->delUser(self::USER);
        $this->assertFalse( $storage->getTrustedSites(self::USER) );
    }
}
