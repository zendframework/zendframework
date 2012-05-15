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


namespace ZendTest\OpenId\Consumer\Storage;

use Zend\OpenId\OpenId,
    Zend\OpenId\Consumer\Storage;

/**
 * @see Storage\File
 */

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
    const URL      = "http://www.myopenid.com/";
    const HANDLE   = "d41d8cd98f00b204e9800998ecf8427e";
    const MAC_FUNC = "sha256";
    const SECRET   = "4fa03202081808bd19f92b667a291873";

    const ID       = "http://id.myopenid.com/";
    const REAL_ID  = "http://real_id.myopenid.com/";
    const SERVER   = "http://www.myopenid.com/";
    const SERVER2  = "http://www.myopenid2.com/";
    const VERSION  = 1.0;

    protected $_tmpDir;

    /**
     * Remove directory recursively
     *
     * @param string $dir
     */
    private static function _rmDir($dirName)
    {
        if (!file_exists($dirName)) {
            return;
        }

        // remove files from temporary direcytory
        $dir = opendir($dirName);
        while (($file = readdir($dir)) !== false) {
            if (is_dir($dirName . '/' . $file)) {
                if ($file == '.'  ||  $file == '..') {
                    continue;
                }

                self::_rmDir($dirName . '/' . $file);
            } else {
                unlink($dirName . '/' . $file);
            }
        }
        closedir($dir);

        @rmdir($dirName);
    }

    public function setUp()
    {
        $this->_tmpDir = __DIR__ . "/_files";

        // Clear directory
        self::_rmDir($this->_tmpDir);
        mkdir($this->_tmpDir);
    }

    public function tearDown()
    {
        self::_rmDir($this->_tmpDir);
    }

    /**
     * testing __construct
     *
     */
    public function testConstruct()
    {
        $tmp = $this->_tmpDir;
        $dir = $tmp . '/openid_consumer';

        $storage = new Storage\File($dir);
        $this->assertTrue( is_dir($dir) );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        chmod($dir, 0400);
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
        self::_rmDir($dir);
    }

    /**
     * testing getAssociation
     *
     */
    public function testGetAssociation()
    {
        $tmp = $this->_tmpDir;
        $dir = $tmp . '/openid_consumer';

        $expiresIn = time() + 600;
        $storage = new Storage\File($tmp);
        $storage->delAssociation(self::URL);
        $this->assertTrue( $storage->addAssociation(self::URL, self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        $this->assertTrue( $storage->getAssociation(self::URL, $handle, $macFunc, $secret, $expires) );
        $this->assertSame( self::HANDLE, $handle );
        $this->assertSame( self::MAC_FUNC, $macFunc );
        $this->assertSame( self::SECRET, $secret );
        $this->assertSame( $expiresIn, $expires );
        $this->assertTrue( $storage->delAssociation(self::URL) );
        $this->assertFalse( $storage->getAssociation(self::URL, $handle, $macFunc, $secret, $expires) );

        $storage = new Storage\File($dir);
        $this->assertTrue( is_dir($dir) );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        chmod($dir, 0);
        $this->assertFalse( $storage->addAssociation(self::URL, self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        chmod($dir, 0777);
    }

    /**
     * testing getAssociationByHandle
     *
     */
    public function testGetAssociationByHandle()
    {
        $tmp = $this->_tmpDir;
        $dir = $tmp . '/openid_consumer';

        $expiresIn = time() + 600;
        $storage = new Storage\File($tmp);
        $storage->delAssociation(self::URL);
        $this->assertTrue( $storage->addAssociation(self::URL, self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        $this->assertTrue( $storage->getAssociationByHandle(self::HANDLE, $url, $macFunc, $secret, $expires) );
        $this->assertSame( self::URL, $url );
        $this->assertSame( self::MAC_FUNC, $macFunc );
        $this->assertSame( self::SECRET, $secret );
        $this->assertSame( $expiresIn, $expires );
        $this->assertTrue( $storage->delAssociation(self::URL) );
        $this->assertFalse( $storage->getAssociationByHandle(self::HANDLE, $url, $macFunc, $secret, $expires) );
    }

    /**
     * testing getAssociation
     *
     */
    public function testGetAssociationExpiratin()
    {
        $tmp = $this->_tmpDir;
        $dir = $tmp . '/openid_consumer';

        $expiresIn = time() + 1;
        $storage = new Storage\File($tmp);
        $storage->delAssociation(self::URL);
        $this->assertTrue( $storage->addAssociation(self::URL, self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        sleep(2);
        $this->assertFalse( $storage->getAssociation(self::URL, $handle, $macFunc, $secret, $expires) );
    }

    /**
     * testing getAssociationByHandle
     *
     */
    public function testGetAssociationByHandleExpiration()
    {
        $tmp = $this->_tmpDir;
        $dir = $tmp . '/openid_consumer';

        $expiresIn = time() + 1;
        $storage = new Storage\File($tmp);
        $storage->delAssociation(self::URL);
        $this->assertTrue( $storage->addAssociation(self::URL, self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        sleep(2);
        $this->assertFalse( $storage->getAssociationByHandle(self::HANDLE, $url, $macFunc, $secret, $expires) );
    }

    /**
     * testing getDiscoveryInfo
     *
     */
    public function testGetDiscoveryInfo()
    {
        $tmp = $this->_tmpDir;
        $dir = $tmp . '/openid_consumer';

        $expiresIn = time() + 600;
        $storage = new Storage\File($tmp);
        $storage->delDiscoveryInfo(self::ID);
        $this->assertTrue( $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, self::VERSION, $expiresIn) );
        $this->assertTrue( $storage->getDiscoveryInfo(self::ID, $realId, $server, $version, $expires) );
        $this->assertSame( self::REAL_ID, $realId );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( self::VERSION, $version );
        $this->assertSame( $expiresIn, $expires );
        $this->assertTrue( $storage->delDiscoveryInfo(self::ID) );
        $this->assertFalse( $storage->getDiscoveryInfo(self::ID, $realId, $server, $version, $expires) );

        self::_rmDir($dir);
        $storage = new Storage\File($dir);
        $this->assertTrue( is_dir($dir) );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        chmod($dir, 0);
        $this->assertFalse( $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, self::VERSION, $expiresIn) );
        chmod($dir, 0777);
        @rmdir($dir);
    }

    /**
     * testing getDiscoveryInfo
     *
     */
    public function testGetDiscoveryInfoExpiration()
    {
        $tmp = $this->_tmpDir;
        $dir = $tmp . '/openid_consumer';

        $expiresIn = time() + 1;
        $storage = new Storage\File($tmp);
        $storage->delDiscoveryInfo(self::ID);
        $this->assertTrue( $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, self::VERSION, $expiresIn) );
        sleep(2);
        $this->assertFalse( $storage->getDiscoveryInfo(self::ID, $realId, $server, $version, $expires) );
    }

    /**
     * testing isUniqueNonce
     *
     */
    public function testIsUniqueNonce()
    {
        $tmp = $this->_tmpDir;
        $dir = $tmp . '/openid_consumer';

        $storage = new Storage\File($tmp);
        $storage->purgeNonces();
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '1') );
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '2') );
        $this->assertFalse( $storage->isUniqueNonce(self::SERVER, '1') );
        $this->assertFalse( $storage->isUniqueNonce(self::SERVER, '2') );
        $storage->purgeNonces();
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '1') );
        sleep(2);
        $date = @date("r", time());
        sleep(2);
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '2') );
        $storage->purgeNonces($date);
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '1') );
        $this->assertFalse( $storage->isUniqueNonce(self::SERVER, '2') );
        $storage->purgeNonces();
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '1') );
        sleep(2);
        $date = time();
        sleep(2);
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '2') );
        $storage->purgeNonces($date);
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '1') );
        $this->assertFalse( $storage->isUniqueNonce(self::SERVER, '2') );
        $storage->purgeNonces();
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER, '1') );
        $this->assertTrue( $storage->isUniqueNonce(self::SERVER2, '1') );
        $storage->purgeNonces();
    }
}
