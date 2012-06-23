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
 * @package    Zend_InfoCard
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\InfoCard;
use Zend\InfoCard\Cipher\PKI\Adapter;
use Zend\InfoCard\Cipher;


/**
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_InfoCard
 */
class CipherTest extends \PHPUnit_Framework_TestCase
{

    public function testPkiPadding()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The openssl extension is not loaded.');
        }

        $obj = new Adapter\RSA();

        $prv_key = file_get_Contents(__DIR__ . "/_files/ssl_private.cert");

        $result = $obj->decrypt("foo", $prv_key, null, Adapter\AbstractAdapter::NO_PADDING);

        // This is sort of werid, but since we don't have a real PK-encrypted string to test against for NO_PADDING
        // mode we decrypt the string "foo" instead. Mathmatically we will always arrive at the same resultant
        // string so if our hash doesn't match then something broke.
        $this->assertSame(md5($result), "286c1991e1f7040229a6f223065b91b5");
    }
    
    public function testPkiPaddingWithThrowExceptionOnBadInput()
    {
            if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The openssl extension is not loaded.');
        }

        try {
            $obj = new Adapter\RSA("thiswillbreak");
            $this->fail("Exception not thrown as expected");
        } catch(\Exception $e) {
            /* yay */
        }
    }
    
    public function testPkiPaddingWithThrowExceptionOnBadInput2()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The openssl extension is not loaded.');
        }

        $obj = new Adapter\RSA();

        $prv_key = file_get_Contents(__DIR__ . "/_files/ssl_private.cert");

        try {
            $obj->decrypt("Foo", $prv_key, null, "foo");
            $this->fail("Expected Exception Not Thrown");
        } catch(\Exception $e) {
            /* yay */
        }

    }
        
    public function testPKIDecryptThrowsExceptionOnBadKey()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The openssl extension is not loaded.');
        }

        $obj = new Adapter\RSA();

        $this->setExpectedException('Zend\InfoCard\Cipher\Exception\RuntimeException', 'Failed to load private key');
        $obj->decrypt("Foo", "bar");
    }

    public function testCipherFactory()
    {
        if(!extension_loaded('mcrypt') || !extension_loaded('openssl')) {
            $this->markTestSkipped('Use of the Zend_InfoCard component requires the mcrypt and openssl extension to be enabled in PHP');
        }

        $this->assertTrue(Cipher::getInstanceByURI(Cipher::ENC_AES128CBC)
                          instanceof \Zend\InfoCard\Cipher\Symmetric\Adapter\AES128CBC);
        $this->assertTrue(Cipher::getInstanceByURI(Cipher::ENC_RSA)
                          instanceof Adapter\RSA);

    }
    
    public function testCipherFactoryThrowsExceptionOnBadInput()
    {
        $this->setExpectedException('Zend\InfoCard\Cipher\Exception\InvalidArgumentException', 'Unknown Cipher URI');
        Cipher::getInstanceByURI("Broken");
    }
    
}
