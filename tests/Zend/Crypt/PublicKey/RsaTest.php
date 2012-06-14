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
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Crypt;

use Zend\Crypt\PublicKey\Rsa;
use Zend\Crypt\PublicKey\RsaOptions;
use Zend\Crypt\PublicKey\Rsa\Exception;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Crypt
 */
class RsaTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $_testPemString = null;

    /** @var string */
    protected $_testPemPath = null;

    /** @var string */
    public $openSslConf;

    /** @var string */
    public $_testPemStringPublic;

    /** @var string */
    public $_testCertificateString;

    /** @var string */
    public $_testCertificatePath;

    public function setUp()
    {
        $openSslConf = false;
        if (isset($_ENV['OPENSSL_CONF'])) {
            $openSslConf = $_ENV['OPENSSL_CONF'];
        } elseif (isset($_ENV['SSLEAY_CONF'])) {
            $openSslConf = $_ENV['SSLEAY_CONF'];
        } elseif (constant('TESTS_ZEND_CRYPT_OPENSSL_CONF')) {
            $openSslConf = constant('TESTS_ZEND_CRYPT_OPENSSL_CONF');
        }
        $this->openSslConf = $openSslConf;

        try {
            $rsa = new Rsa();
        } catch (Exception\RuntimeException $e) {
            if (strpos($e->getMessage(), 'requires openssl extension') !== false) {
                $this->markTestSkipped($e->getMessage());
            } else {
                throw $e;
            }
        }

        $this->_testPemString = <<<RSAKEY
-----BEGIN RSA PRIVATE KEY-----
MIIBOgIBAAJBANDiE2+Xi/WnO+s120NiiJhNyIButVu6zxqlVzz0wy2j4kQVUC4Z
RZD80IY+4wIiX2YxKBZKGnd2TtPkcJ/ljkUCAwEAAQJAL151ZeMKHEU2c1qdRKS9
sTxCcc2pVwoAGVzRccNX16tfmCf8FjxuM3WmLdsPxYoHrwb1LFNxiNk1MXrxjH3R
6QIhAPB7edmcjH4bhMaJBztcbNE1VRCEi/bisAwiPPMq9/2nAiEA3lyc5+f6DEIJ
h1y6BWkdVULDSM+jpi1XiV/DevxuijMCIQCAEPGqHsF+4v7Jj+3HAgh9PU6otj2n
Y79nJtCYmvhoHwIgNDePaS4inApN7omp7WdXyhPZhBmulnGDYvEoGJN66d0CIHra
I2SvDkQ5CmrzkW5qPaE2oO7BSqAhRZxiYpZFb5CI
-----END RSA PRIVATE KEY-----

RSAKEY;

        $this->_testPemStringPublic   = <<<RSAKEY
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBANDiE2+Xi/WnO+s120NiiJhNyIButVu6
zxqlVzz0wy2j4kQVUC4ZRZD80IY+4wIiX2YxKBZKGnd2TtPkcJ/ljkUCAwEAAQ==
-----END PUBLIC KEY-----

RSAKEY;
        $this->_testCertificateString = <<<CERT
-----BEGIN CERTIFICATE-----
MIIC6TCCApOgAwIBAgIBADANBgkqhkiG9w0BAQQFADCBhzELMAkGA1UEBhMCSUUx
DzANBgNVBAgTBkR1YmxpbjEPMA0GA1UEBxMGRHVibGluMQ4wDAYDVQQKEwVHcm91
cDERMA8GA1UECxMIU3ViZ3JvdXAxEzARBgNVBAMTCkpvZSBCbG9nZ3MxHjAcBgkq
hkiG9w0BCQEWD2pvZUBleGFtcGxlLmNvbTAeFw0wODA2MTMwOTQ4NDlaFw0xMTA2
MTMwOTQ4NDlaMIGHMQswCQYDVQQGEwJJRTEPMA0GA1UECBMGRHVibGluMQ8wDQYD
VQQHEwZEdWJsaW4xDjAMBgNVBAoTBUdyb3VwMREwDwYDVQQLEwhTdWJncm91cDET
MBEGA1UEAxMKSm9lIEJsb2dnczEeMBwGCSqGSIb3DQEJARYPam9lQGV4YW1wbGUu
Y29tMFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBANDiE2+Xi/WnO+s120NiiJhNyIBu
tVu6zxqlVzz0wy2j4kQVUC4ZRZD80IY+4wIiX2YxKBZKGnd2TtPkcJ/ljkUCAwEA
AaOB5zCB5DAdBgNVHQ4EFgQUxpguR0f4g+502IxAp3aMZvJ6asMwgbQGA1UdIwSB
rDCBqYAUxpguR0f4g+502IxAp3aMZvJ6asOhgY2kgYowgYcxCzAJBgNVBAYTAklF
MQ8wDQYDVQQIEwZEdWJsaW4xDzANBgNVBAcTBkR1YmxpbjEOMAwGA1UEChMFR3Jv
dXAxETAPBgNVBAsTCFN1Ymdyb3VwMRMwEQYDVQQDEwpKb2UgQmxvZ2dzMR4wHAYJ
KoZIhvcNAQkBFg9qb2VAZXhhbXBsZS5jb22CAQAwDAYDVR0TBAUwAwEB/zANBgkq
hkiG9w0BAQQFAANBAE4M7ZXJTDLHEFguGaP5g64lbmLmLtYX22ZaNY891FmxhtKm
l9Nwj3KnPKFdqzJchujP2TLNwSYoQnxgyoMxdho=
-----END CERTIFICATE-----

CERT;

        $this->_testPemPath = realpath(__DIR__ . '/../_files/test.pem');

        $this->_testCertificatePath = realpath(__DIR__ . '/../_files/test.cert');
    }


    public function testConstructorSetsPemString()
    {
        $rsa = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $this->assertEquals($this->_testPemString, $rsa->getOptions()->getPemString());
    }

    public function testConstructorSetsPemPath()
    {
        $rsa = new Rsa(new RsaOptions(array('pem_path' => $this->_testPemPath)));
        $this->assertEquals($this->_testPemPath, $rsa->getOptions()->getPemPath());
    }

    public function testSetPemPathLoadsPemString()
    {
        $rsa = new Rsa(new RsaOptions(array('pem_path' => $this->_testPemPath)));
        $this->assertEquals($this->_testPemString, $rsa->getOptions()->getPemString());
    }

    public function testConstructorSetsCertificateString()
    {
        $rsa = new Rsa(new RsaOptions(array('certificate_string' => $this->_testCertificateString)));
        $this->assertEquals($this->_testCertificateString, $rsa->getOptions()->getCertificateString());
    }

    public function testConstructorSetsCertificatePath()
    {
        $rsa = new Rsa(new RsaOptions(array('certificate_path' => $this->_testCertificatePath)));
        $this->assertEquals($this->_testCertificatePath, $rsa->getOptions()->getCertificatePath());
    }

    public function testSetCertificatePathLoadsCertificateString()
    {
        $rsa = new Rsa(new RsaOptions(array('certificate_path' => $this->_testCertificatePath)));
        $this->assertEquals($this->_testCertificateString, $rsa->getOptions()->getCertificateString());
    }

    public function testConstructorSetsHashOption()
    {
        $rsa = new Rsa(new RsaOptions(array('hash_algorithm' => 'md5')));
        $this->assertEquals(OPENSSL_ALGO_MD5, $rsa->getOptions()->getHashAlgorithm());
    }

    public function testSetPemStringParsesPemForPrivateKey()
    {
        $rsa = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $this->assertInstanceOf('Zend\\Crypt\\PublicKey\\RSA\\PrivateKey', $rsa->getOptions()->getPrivateKey());
    }


    public function testSetPemStringParsesPemForPublicKey()
    {
        $rsa = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $this->assertInstanceOf('Zend\\Crypt\\PublicKey\\RSA\\PublicKey', $rsa->getOptions()->getPublicKey());

    }

    public function testSetCertificateStringParsesCertificateForNullPrivateKey()
    {
        $rsa = new Rsa(new RsaOptions(array('certificate_string' => $this->_testCertificateString)));
        $this->assertSame(null, $rsa->getOptions()->getPrivateKey());
    }

    public function testSetCertificateStringParsesCertificateForPublicKey()
    {
        $rsa = new Rsa(new RsaOptions(array('certificate_string' => $this->_testCertificateString)));
        $this->assertInstanceOf('Zend\\Crypt\\PublicKey\\RSA\\PublicKey', $rsa->getOptions()->getPublicKey());
    }

    public function testSignGeneratesExpectedBinarySignature()
    {
        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $signature = $rsa->sign('1234567890');
        $this->assertEquals(
            'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
            base64_encode($signature));
    }

    public function testSignGeneratesExpectedBinarySignatureUsingExternalKey()
    {
        $rsa        = new Rsa(new RsaOptions(array('certificate_string' => $this->_testCertificateString)));
        $privateKey = new Rsa\PrivateKey($this->_testPemString);
        $signature  = $rsa->sign('1234567890', $privateKey);
        $this->assertEquals(
            'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
            base64_encode($signature));
    }

    public function testSignGeneratesExpectedBase64Signature()
    {

        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $signature = $rsa->sign('1234567890', null, Rsa::FORMAT_BASE64);
        $this->assertEquals(
            'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
            $signature);
    }

    public function testVerifyVerifiesBinarySignatures()
    {
        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $signature = $rsa->sign('1234567890');
        $result    = $rsa->verify('1234567890', $signature);

        $this->assertSame(true, $result);
    }

    public function testVerifyVerifiesBinarySignaturesUsingCertificate()
    {
        $rsa        = new Rsa(new RsaOptions(array('certificate_string' => $this->_testCertificateString)));
        $privateKey = new Rsa\PrivateKey($this->_testPemString);
        $signature  = $rsa->sign('1234567890', $privateKey);
        $result     = $rsa->verify('1234567890', $signature);

        $this->assertSame(true, $result);
    }

    public function testVerifyVerifiesBase64Signatures()
    {
        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $signature = $rsa->sign('1234567890', null, Rsa::FORMAT_BASE64);
        $result    = $rsa->verify('1234567890', $signature, null, Rsa::FORMAT_BASE64);

        $this->assertSame(true, $result);
    }

    public function testEncryptionWithPublicKey()
    {
        $publicKey  = new Rsa\PublicKey($this->_testCertificateString);
        $privateKey = new Rsa\PrivateKey($this->_testPemString);

        $encrypted = $publicKey->encrypt('1234567890');

        $this->assertEquals('1234567890', $privateKey->decrypt($encrypted));
    }

    public function testEncryptionWithPrivateKey()
    {
        $publicKey  = new Rsa\PublicKey($this->_testCertificateString);
        $privateKey = new Rsa\PrivateKey($this->_testPemString);

        $encrypted = $privateKey->encrypt('1234567890');

        $this->assertEquals('1234567890', $publicKey->decrypt($encrypted));
    }

    public function testEncryptionWithOwnKeys()
    {
        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $encrypted = $rsa->encrypt('1234567890');

        $this->assertEquals('1234567890', $rsa->decrypt($encrypted));
    }

    public function testEncryptionUsingPublicKeyEncryption()
    {
        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getOptions()->getPublicKey());

        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa->getOptions()->getPrivateKey())
        );
    }

    public function testEncryptionUsingPublicKeyBase64Encryption()
    {
        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getOptions()->getPublicKey(), Rsa::FORMAT_BASE64);

        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa->getOptions()->getPrivateKey(), Rsa::FORMAT_BASE64)
        );
    }

    public function testBase64EncryptionUsingCertificatePublicKeyEncryption()
    {
        $rsa       = new Rsa(new RsaOptions(array('certificate_string' => $this->_testCertificateString)));
        $rsa2      = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getOptions()->getPublicKey(), Rsa::FORMAT_BASE64);

        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa2->getOptions()->getPrivateKey(), Rsa::FORMAT_BASE64)
        );
    }

    public function testEncryptionUsingPrivateKeyEncryption()
    {
        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getOptions()->getPrivateKey());

        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa->getOptions()->getPublicKey())
        );
    }

    public function testEncryptionUsingPrivateKeyBase64Encryption()
    {
        $rsa       = new Rsa(new RsaOptions(array('pem_string' => $this->_testPemString)));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getOptions()->getPrivateKey(), Rsa::FORMAT_BASE64);
        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa->getOptions()->getPublicKey(), Rsa::FORMAT_BASE64)
        );
    }

    public function testKeyGeneration()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }

        $rsa  = new Rsa();
        $keys = $rsa->generateKeys(array(
            'config'           => $this->openSslConf,
            'private_key_bits' => 512,
        ));

        $this->assertInstanceOf('Zend\\Crypt\\PublicKey\\Rsa\\PrivateKey', $keys->privateKey);
        $this->assertInstanceOf('Zend\\Crypt\\PublicKey\\Rsa\\PublicKey', $keys->publicKey);
    }

    public function testKeyGenerationCreatesPassphrasedPrivateKey()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }

        $rsa  = new Rsa();
        $keys = $rsa->generateKeys(array(
            'config'           => $this->openSslConf,
            'private_key_bits' => 512,
            'pass_phrase'     => '0987654321'
        ));

        try {
            $rsa = new Rsa(new RsaOptions(array(
                'pass_phrase' => '1234567890',
                'pem_string'  => $keys->privateKey->toString()
            )));
            $this->fail('Expected exception not thrown');
        } catch (Exception\ExceptionInterface $e) {
        }
    }

    public function testConstructorLoadsPassphrasedKeys()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }

        $rsa  = new Rsa();
        $keys = $rsa->generateKeys(array(
            'config'           => $this->openSslConf,
            'private_key_bits' => 512,
            'pass_phrase'     => '0987654321'
        ));

        try {
            $rsa = new Rsa(new RsaOptions(array(
                'pass_phrase' => '0987654321',
                'pem_string'  => $keys->privateKey->toString()
            )));
        } catch (Exception\ExceptionInterface $e) {
            $this->fail('Passphrase loading failed of a private key');
        }
    }


    /**
     * @group ZF-8846
     */
    /*
    public function testLoadsPublicKeyFromPEMWithoutPrivateKeyAndThrowsNoException()
    {
        $rsa = new Rsa;
        $rsa->setPemString($this->_testPemStringPublic);
    }
     */
}
