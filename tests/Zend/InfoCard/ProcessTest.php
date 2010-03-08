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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_InfoCard_ProcessTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_InfoCard_ProcessTest::main");
}

/**
 * Test helper
 */



/**
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_InfoCard
 */
class Zend_InfoCard_ProcessTest extends PHPUnit_Framework_TestCase
{
    protected $_xmlDocument;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_InfoCard_ProcessTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->tokenDocument = dirname(__FILE__) . '/_files/encryptedtoken.xml';
        $this->sslPubKey     = dirname(__FILE__) . '/_files/ssl_pub.cert';
        $this->sslPrvKey     = dirname(__FILE__) . '/_files/ssl_private.cert';
        $this->loadXmlDocument();
        $_SERVER['SERVER_NAME'] = "192.168.1.105";
        $_SERVER['SERVER_PORT'] = 80;
    }

    public function loadXmlDocument()
    {
        $this->_xmlDocument = file_get_contents($this->tokenDocument);
    }

    public function testCertificatePairs()
    {
        try {
            $infoCard = new Zend_InfoCard();
        } catch (Zend_InfoCard_Exception $e) {
            $message = $e->getMessage();
            if (preg_match('/requires.+mcrypt/', $message)) {
                $this->markTestSkipped($message);
            } else {
                throw $e;
            }
        }

        $key_id = $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);

        $this->assertTrue((bool)$key_id);

        $key_pair = $infoCard->getCertificatePair($key_id);

        $this->assertTrue(!empty($key_pair['public']));
        $this->assertTrue(!empty($key_pair['private']));
        $this->assertTrue(!empty($key_pair['type_uri']));

        $infoCard->removeCertificatePair($key_id);

        $failed = false;

        try {
            $key_pair = $infoCard->getCertificatePair($key_id);
        } catch(Zend_InfoCard_Exception $e) {
            $failed = true;
        }

        $this->assertTrue($failed);

        try {
            $infoCard->addCertificatePair("I don't exist", "I don't exist");
        } catch(Zend_InfoCard_Exception $e) {
            $this->assertTrue(true);
        } catch(Exception $e) {
            $this->assertFalse(true);
        }

        $key_id = $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey, Zend_InfoCard_Cipher::ENC_RSA_OAEP_MGF1P, "foo");

        try {
            $key_id = $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey, Zend_InfoCard_Cipher::ENC_RSA_OAEP_MGF1P, "foo");
        } catch(Zend_InfoCard_Exception $e) {
            $this->assertTrue(true);
        } catch(Exception $e) {
            $this->assertFalse(true);
        }

        $this->assertTrue(!empty($key_id));

        try {
            $infoCard->removeCertificatePair($key_id);
            $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey, "Doesn't Exist", "foo");
        } catch(Zend_InfoCard_Exception $e) {
            $this->assertTrue(true);
        } catch(Exception $e) {
            $this->assertFalse(true);
        }
    }

    public function testStandAloneProcess()
    {
        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            $this->markTestSkipped('DOMDocument::C14N() not available until PHP 5.2.0');
        }

        try {
            $infoCard = new Zend_InfoCard();
        } catch (Zend_InfoCard_Exception $e) {
            $message = $e->getMessage();
            if (preg_match('/requires.+mcrypt/', $message)) {
                $this->markTestSkipped($message);
            } else {
                throw $e;
            }
        }

        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);

        $claims = $infoCard->process($this->_xmlDocument);

        $this->assertTrue($claims instanceof Zend_InfoCard_Claims);
    }

    public function testPlugins()
    {
        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            $this->markTestSkipped('DOMDocument::C14N() not available until PHP 5.2.0');
        }

        $adapter  = new _Zend_InfoCard_Test_Adapter();

        try {
            $infoCard = new Zend_InfoCard();
        } catch (Zend_InfoCard_Exception $e) {
            $message = $e->getMessage();
            if (preg_match('/requires.+mcrypt/', $message)) {
                $this->markTestSkipped($message);
            } else {
                throw $e;
            }
        }

        $infoCard->setAdapter($adapter);

        $result = $infoCard->getAdapter() instanceof Zend_InfoCard_Adapter_Interface;

        $this->assertTrue($result);
        $this->assertTrue($infoCard->getAdapter() instanceof _Zend_InfoCard_Test_Adapter);

        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);

        $claims = $infoCard->process($this->_xmlDocument);

        $pki_object = new Zend_InfoCard_Cipher_Pki_Adapter_Rsa(Zend_InfoCard_Cipher_Pki_Adapter_Abstract::NO_PADDING);

        $infoCard->setPkiCipherObject($pki_object);

        $this->assertTrue($pki_object === $infoCard->getPkiCipherObject());

        $sym_object = new Zend_InfoCard_Cipher_Symmetric_Adapter_Aes256cbc();

        $infoCard->setSymCipherObject($sym_object);

        $this->assertTrue($sym_object === $infoCard->getSymCipherObject());
    }

    public function testClaims()
    {
        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            $this->markTestSkipped('DOMDocument::C14N() not available until PHP 5.2.0');
        }

        try {
            $infoCard = new Zend_InfoCard();
        } catch (Zend_InfoCard_Exception $e) {
            $message = $e->getMessage();
            if (preg_match('/requires.+mcrypt/', $message)) {
                $this->markTestSkipped($message);
            } else {
                throw $e;
            }
        }

        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);

        $claims = $infoCard->process($this->_xmlDocument);

        $this->assertTrue($claims instanceof Zend_InfoCard_Claims);

        $this->assertFalse($claims->isValid());

        $this->assertSame($claims->getCode(), Zend_InfoCard_Claims::RESULT_VALIDATION_FAILURE);

        $errormsg = $claims->getErrorMsg();
        $this->assertTrue(!empty($errormsg));


        @$claims->forceValid();

        $this->assertTrue($claims->isValid());

        $this->assertSame($claims->emailaddress, "john@zend.com");
        $this->assertSame($claims->givenname, "John");
        $this->assertSame($claims->surname, "Coggeshall");
        $this->assertSame($claims->getCardID(), "rW1/y9BuncoBK4WSipF2hHYParxxgMHk6ANBrhz1Zr4=");
        $this->assertSame($claims->getClaim("http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"), "john@zend.com");
        $this->assertSame($claims->getDefaultNamespace(), "http://schemas.xmlsoap.org/ws/2005/05/identity/claims");

        try {
            unset($claims->givenname);
        } catch(Zend_InfoCard_Exception $e) {

        } catch(Exception $e) {
            $this->assertFalse(true);
        }


        try {
            $claims->givenname = "Test";
        } catch(Zend_InfoCard_Exception $e) {

        } catch(Exception $e) {
            $this->assertFalse(true);
        }

        $this->assertTrue(isset($claims->givenname));
    }

    public function testDefaultAdapter()
    {
        $adapter = new Zend_InfoCard_Adapter_Default();

        $this->assertTrue($adapter->storeAssertion(1, 2, array(3)));
        $this->assertFalse($adapter->retrieveAssertion(1, 2));
        $this->assertTrue(is_null($adapter->removeAssertion(1, 2)));
    }

    public function testTransforms()
    {
        $trans = new Zend_InfoCard_Xml_Security_Transform();

        try {
            $trans->addTransform("foo");
            $this->fail("Expected Exception Not Thrown");
        } catch(Exception $e) {
            /* yay */
        }

        $this->assertTrue(is_array($trans->getTransformList()));

    }
}

class _Zend_InfoCard_Test_Adapter
    extends PHPUnit_Framework_TestCase
    implements Zend_InfoCard_Adapter_Interface
{
    public function storeAssertion($assertionURI, $assertionID, $conditions)
    {
        $this->assertTrue(!empty($assertionURI));
        $this->assertTrue(!empty($assertionID));
        $this->assertTrue(!empty($conditions));
        return true;
    }

    public function retrieveAssertion($assertionURI, $assertionID)
    {
        $this->assertTrue(!empty($assertionURI));
        $this->assertTrue(!empty($assertionID));
        return false;
    }

    public function removeAssertion($asserionURI, $assertionID)
    {
        $this->assertTrue(!empty($assertionURI));
        $this->asserTrue(!empty($assertionID));
    }
}

// Call Zend_InfoCard_ProcessTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_InfoCard_ProcessTest::main") {
    Zend_InfoCard_ProcessTest::main();
}
