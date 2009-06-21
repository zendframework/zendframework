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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_InfoCard_ProcessTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_InfoCard_CipherTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/InfoCard.php';
require_once 'Zend/InfoCard/Cipher/Pki/Adapter/Rsa.php';

class Zend_InfoCard_CipherTest extends PHPUnit_Framework_TestCase
{

    public function testPkiPadding()
    {
    	if (!extension_loaded('openssl')) {
    		$this->markTestSkipped('The openssl extension is not loaded.');
    	}

        try {
            $obj = new Zend_InfoCard_Cipher_Pki_Adapter_Rsa("thiswillbreak");
            $this->fail("Exception not thrown as expected");
        } catch(Exception $e) {
            /* yay */
        }

        $obj = new Zend_InfoCard_Cipher_Pki_Adapter_Rsa();

        $prv_key = file_get_Contents(dirname(__FILE__) . "/_files/ssl_private.cert");

        try {
            $obj->decrypt("Foo", $prv_key, null, "foo");
            $this->fail("Expected Exception Not Thrown");
        } catch(Exception $e) {
            /* yay */
        }

        $result = $obj->decrypt("foo", $prv_key, null, Zend_InfoCard_Cipher_Pki_Adapter_Abstract::NO_PADDING);

        // This is sort of werid, but since we don't have a real PK-encrypted string to test against for NO_PADDING
        // mode we decrypt the string "foo" instead. Mathmatically we will always arrive at the same resultant
        // string so if our hash doesn't match then something broke.
        $this->assertSame(md5($result), "286c1991e1f7040229a6f223065b91b5");
    }

    public function testPKIDecryptBadKey()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The openssl extension is not loaded.');
        }

        $obj = new Zend_InfoCard_Cipher_Pki_Adapter_Rsa();

        try {
            $obj->decrypt("Foo", "bar");
            $this->fail("Exception not thrown as expected");
        } catch(Exception $e) {
            /* yay */
        }

    }

    public function testCipherFactory()
    {
        if (!defined('MCRYPT_RIJNDAEL_128')) {
            $this->markTestSkipped('Use of the Zend_InfoCard component requires the mcrypt extension to be enabled in PHP');
        }

        $this->assertTrue(Zend_InfoCard_Cipher::getInstanceByURI(Zend_InfoCard_Cipher::ENC_AES128CBC)
                          instanceof Zend_InfoCard_Cipher_Symmetric_Adapter_Aes128cbc);
        $this->assertTrue(Zend_InfoCard_Cipher::getInstanceByURI(Zend_InfoCard_Cipher::ENC_RSA)
                          instanceof Zend_InfoCard_Cipher_Pki_Adapter_Rsa);

        try {
            Zend_InfoCard_Cipher::getInstanceByURI("Broken");
            $this->fail("Exception not thrown as expected");
        } catch(Exception $e) {
            /* yay */
        }
    }
}