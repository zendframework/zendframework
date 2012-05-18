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
use Zend\InfoCard;
use Zend\InfoCard\Cipher;
use Zend\InfoCard\Adapter;

/**
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_InfoCard
 */
class ProcessTest extends \PHPUnit_Framework_TestCase
{
    protected $_xmlDocument;



    public function setUp()
    {
        $this->tokenDocument = __DIR__ . '/_files/encryptedtoken.xml';
        $this->sslPubKey     = __DIR__ . '/_files/ssl_pub.cert';
        $this->sslPrvKey     = __DIR__ . '/_files/ssl_private.cert';
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
        $this->requireMcryptAndOpensslOrSkip();

        $infoCard = new InfoCard\InfoCard();
        $key_id = $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);

        $this->assertTrue((bool)$key_id);

        $key_pair = $infoCard->getCertificatePair($key_id);

        $this->assertTrue(!empty($key_pair['public']));
        $this->assertTrue(!empty($key_pair['private']));
        $this->assertTrue(!empty($key_pair['type_uri']));
    }

    public function testGetCertificatePairThrowsExceptionOnMissingKeyId()
    {
        $this->requireMcryptAndOpensslOrSkip();
        $infoCard = new InfoCard\InfoCard();
        $key_id = $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);
        $infoCard->removeCertificatePair($key_id);

        $this->setExpectedException('Zend\InfoCard\Exception\InvalidArgumentException', 'Invalid Certificate Pair ID provided');
        $key_pair = $infoCard->getCertificatePair($key_id);
    }

    public function testAddCertificatePairThrowsExceptionOnMissingCertificates()
    {
        $this->requireMcryptAndOpensslOrSkip();
        $infoCard = new InfoCard\InfoCard();

        $this->setExpectedException('Zend\InfoCard\Exception\InvalidArgumentException', 'Could not locate the public and private certificate pair files');
        $infoCard->addCertificatePair("I don't exist", "I don't exist");
    }

    public function testAddCertificatePairThrowsExceptionOnDuplicateRegistration()
    {
        $this->requireMcryptAndOpensslOrSkip();
        $infoCard = new InfoCard\InfoCard();
        $key_id = $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey, Cipher::ENC_RSA_OAEP_MGF1P, "foo");

        $this->setExpectedException('Zend\InfoCard\Exception\InvalidArgumentException', 'Attempted to add previously existing certificate pair');
        $key_id = $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey, Cipher::ENC_RSA_OAEP_MGF1P, "foo");
    }

    public function testAddCertificatePairThrowsExceptionOnBadCipher()
    {
        $this->requireMcryptAndOpensslOrSkip();

        $this->setExpectedException('Zend\InfoCard\Exception\InvalidArgumentException', 'Invalid Certificate Pair Type specified');
        $infoCard = new InfoCard\InfoCard();
        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey, "Doesn't Exist", "foo");
    }

    public function testStandAloneProcess()
    {
        $this->requireMcryptAndOpensslOrSkip();
        $infoCard = new InfoCard\InfoCard();

        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);

        $claims = $infoCard->process($this->_xmlDocument);

        $this->assertTrue($claims instanceof InfoCard\Claims);
    }

    public function testPlugins()
    {
        $adapter  = new TestAsset\MockAdapter();

        $this->requireMcryptAndOpensslOrSkip();

        $infoCard = new InfoCard\InfoCard();

        $infoCard->setAdapter($adapter);

        $result = ($infoCard->getAdapter() instanceof Adapter\AdapterInterface);

        $this->assertTrue($result);
        $this->assertTrue($infoCard->getAdapter() instanceof TestAsset\MockAdapter);

        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);

        $claims = $infoCard->process($this->_xmlDocument);

        $pki_object = new \Zend\InfoCard\Cipher\PKI\Adapter\RSA(\Zend\InfoCard\Cipher\PKI\Adapter\AbstractAdapter::NO_PADDING);

        $infoCard->setPkiCipherObject($pki_object);

        $this->assertTrue($pki_object === $infoCard->getPkiCipherObject());

        $sym_object = new \Zend\InfoCard\Cipher\Symmetric\Adapter\AES256CBC();

        $infoCard->setSymCipherObject($sym_object);

        $this->assertTrue($sym_object === $infoCard->getSymCipherObject());
    }

    public function testClaims()
    {
        $this->requireMcryptAndOpensslOrSkip();

        $infoCard = new InfoCard\InfoCard();

        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);

        $claims = $infoCard->process($this->_xmlDocument);

        $this->assertTrue($claims instanceof InfoCard\Claims);

        $this->assertFalse($claims->isValid());

        $this->assertSame($claims->getCode(), InfoCard\Claims::RESULT_VALIDATION_FAILURE);

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

        $this->assertTrue(isset($claims->givenname));
    }

    public function testClaimsThrowExceptionOnUnset()
    {
        $this->requireMcryptAndOpensslOrSkip();
        $infoCard = new InfoCard\InfoCard();
        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);
        $claims = $infoCard->process($this->_xmlDocument);

        $this->setExpectedException('Zend\InfoCard\Exception\InvalidArgumentException', 'Claim objects are read-only');
        unset($claims->givenname);
    }

    public function testClaimsThrowsExceptionOnMutation()
    {
        $this->requireMcryptAndOpensslOrSkip();
        $infoCard = new InfoCard\InfoCard();
        $infoCard->addCertificatePair($this->sslPrvKey, $this->sslPubKey);
        $claims = $infoCard->process($this->_xmlDocument);

        $this->setExpectedException('Zend\InfoCard\Exception\InvalidArgumentException', 'Claim objects are read-only');
        $claims->givenname = "Test";
    }

    public function testDefaultAdapter()
    {
        $adapter = new Adapter\DefaultAdapter();

        $this->assertTrue($adapter->storeAssertion(1, 2, array(3)));
        $this->assertFalse($adapter->retrieveAssertion(1, 2));
        $this->assertTrue(is_null($adapter->removeAssertion(1, 2)));
    }

    public function testTransforms()
    {
        $trans = new \Zend\InfoCard\XML\Security\Transform\TransformChain();
        $this->assertTrue(is_array($trans->getTransformList()));
    }

    public function testTransformsClassLoad()
    {
        $trans = new \ZendTest\InfoCard\TestAsset\UserTransformChain();
        $trans->addTransform('http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $this->setExpectedException('Zend\InfoCard\XML\Security\Exception\InvalidArgumentException', 'Transform Class not exist');
        $trans->applyTransforms('');
    }

    public function testTransformsThrowsExceptionOnInvalidInput()
    {
        $trans = new \Zend\InfoCard\XML\Security\Transform\TransformChain();

        $this->setExpectedException('Zend\InfoCard\XML\Security\Exception\InvalidArgumentException', 'Unknown or Unsupported Transformation Requested');
        $trans->addTransform("foo");
    }

    protected function requireMcryptAndOpensslOrSkip()
    {
        if (!extension_loaded('mcrypt') || !extension_loaded('openssl')) {
            $this->markTestSkipped('Extension mcrypt and extension openssl are requred for this test');
        }
    }

}


