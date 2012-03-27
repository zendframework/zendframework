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

/**
 * @namespace
 */
namespace ZendTest\Crypt;
use Zend\Crypt\Rsa as RSA,
    Zend\Crypt;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Crypt
 */
class RSATest extends \PHPUnit_Framework_TestCase
{

    protected $_testPemString = null;

    protected $_testPemPath = null;

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
            $math = new \Zend\Crypt\Rsa();
        } catch (\Zend\Crypt\Rsa\Exception $e) {
            if (strpos($e->getMessage(), 'requires openssl extention') !== false) {
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

        $this->_testPemStringPublic = <<<RSAKEY
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

        $this->_testPemPath = __DIR__ . '/_files/test.pem';

        $this->_testCertificatePath = __DIR__ . '/_files/test.cert';
    }

    public function testConstructorSetsPemString()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $this->assertEquals($this->_testPemString, $rsa->getPemString());
    }

    public function testConstructorSetsPemPath()
    {
        $rsa = new RSA(array('pemPath'=>$this->_testPemPath));
        $this->assertEquals($this->_testPemPath, $rsa->getPemPath());
    }

    public function testSetPemPathLoadsPemString()
    {
        $rsa = new RSA(array('pemPath'=>$this->_testPemPath));
        $this->assertEquals($this->_testPemString, $rsa->getPemString());
    }

    public function testConstructorSetsCertificateString()
    {
        $rsa = new RSA(array('certificateString'=>$this->_testCertificateString));
        $this->assertEquals($this->_testCertificateString, $rsa->getCertificateString());
    }

    public function testConstructorSetsCertificatePath()
    {
        $rsa = new RSA(array('certificatePath'=>$this->_testCertificatePath));
        $this->assertEquals($this->_testCertificatePath, $rsa->getCertificatePath());
    }

    public function testSetCertificatePathLoadsCertificateString()
    {
        $rsa = new RSA(array('certificatePath'=>$this->_testCertificatePath));
        $this->assertEquals($this->_testCertificateString, $rsa->getCertificateString());
    }

    public function testConstructorSetsHashOption()
    {
        $rsa = new RSA(array('hashAlgorithm'=>'md5'));
        $this->assertEquals(OPENSSL_ALGO_MD5, $rsa->getHashAlgorithm());
    }

    public function testSetPemStringParsesPemForPrivateKey()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $this->assertInstanceOf('Zend\\Crypt\\RSA\\PrivateKey', $rsa->getPrivateKey());
    }

    public function testSetPemStringParsesPemForPublicKey()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $this->assertInstanceOf('Zend\\Crypt\\RSA\\PublicKey', $rsa->getPublicKey());
    }

    public function testSetCertificateStringParsesCertificateForNullPrivateKey()
    {
        $rsa = new RSA(array('certificateString'=>$this->_testCertificateString));
        $this->assertEquals(null, $rsa->getPrivateKey());
    }

    public function testSetCertificateStringParsesCertificateForPublicKey()
    {
        $rsa = new RSA(array('certificateString'=>$this->_testCertificateString));
        $this->assertInstanceOf('Zend\\Crypt\\RSA\\PublicKey', $rsa->getPublicKey());
    }

    public function testSignGeneratesExpectedBinarySignature()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $signature = $rsa->sign('1234567890');
        $this->assertEquals(
        'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
        base64_encode($signature));
    }

    public function testSignGeneratesExpectedBinarySignatureUsingExternalKey()
    {
        $privateKey = new RSA\PrivateKey($this->_testPemString);
        $rsa = new RSA(array('certificateString'=>$this->_testCertificateString));
        $signature = $rsa->sign('1234567890', $privateKey);
        $this->assertEquals(
        'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
        base64_encode($signature));
    }

    public function testSignGeneratesExpectedBase64Signature()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $signature = $rsa->sign('1234567890', null, RSA::BASE64);
        $this->assertEquals(
        'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
        $signature);
    }

    public function testVerifyVerifiesBinarySignatures()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $signature = $rsa->sign('1234567890');
        $result = $rsa->verifySignature('1234567890', $signature);
        $this->assertEquals(1, $result);
    }

    public function testVerifyVerifiesBinarySignaturesUsingCertificate()
    {
        $privateKey = new RSA\PrivateKey($this->_testPemString);
        $rsa = new RSA(array('certificateString'=>$this->_testCertificateString));
        $signature = $rsa->sign('1234567890', $privateKey);
        $result = $rsa->verifySignature('1234567890', $signature);
        $this->assertEquals(1, $result);
    }

    public function testVerifyVerifiesBase64Signatures()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $signature = $rsa->sign('1234567890', null, RSA::BASE64);
        $result = $rsa->verifySignature('1234567890', $signature, RSA::BASE64);
        $this->assertEquals(1, $result);
    }

    public function testEncryptionUsingPublicKeyEncryption()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getPublicKey());
        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa->getPrivateKey())
        );
    }

    public function testEncryptionUsingPublicKeyBase64Encryption()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getPublicKey(), RSA::BASE64);
        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa->getPrivateKey(), RSA::BASE64)
        );
    }

    public function testBase64EncryptionUsingCertificatePublicKeyEncryption()
    {
        $rsa = new RSA(array('certificateString'=>$this->_testCertificateString));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getPublicKey(), RSA::BASE64);
        $rsa2 = new RSA(array('pemString'=>$this->_testPemString));
        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa2->getPrivateKey(), RSA::BASE64)
        );
    }

    public function testEncryptionUsingPrivateKeyEncryption()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getPrivateKey());
        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa->getPublicKey())
        );
    }

    public function testEncryptionUsingPrivateKeyBase64Encryption()
    {
        $rsa = new RSA(array('pemString'=>$this->_testPemString));
        $encrypted = $rsa->encrypt('1234567890', $rsa->getPrivateKey(), RSA::BASE64);
        $this->assertEquals(
            '1234567890',
            $rsa->decrypt($encrypted, $rsa->getPublicKey(), RSA::BASE64)
        );
    }

    public function testKeyGenerationCreatesArrayObjectResult()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }
        $rsa  = new RSA;
        $keys = $rsa->generateKeys(array(
            'config'           => $this->openSslConf,
            'private_key_bits' => 512,
        ));
        $this->assertInstanceOf('ArrayObject', $keys);
    }

    public function testKeyGenerationCreatesPrivateKeyInArrayObject()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }
        $rsa = new RSA;
        $keys = $rsa->generateKeys(array(
            'config'           => $this->openSslConf,
            'private_key_bits' => 512,
        ));
        $this->assertInstanceOf('Zend\\Crypt\\RSA\\PrivateKey', $keys->privateKey);
    }

    public function testKeyGenerationCreatesPublicKeyInArrayObject()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }
        $rsa = new RSA;
        $keys = $rsa->generateKeys(array(
            'config'         => $this->openSslConf,
            'privateKeyBits' => 512,
        ));
        $this->assertInstanceOf('Zend\\Crypt\\RSA\\PublicKey', $keys->publicKey);
    }

    public function testKeyGenerationCreatesPassphrasedPrivateKey()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }
        $rsa = new RSA;
        $config = array(
            'config'         => $this->openSslConf,
            'privateKeyBits' => 512,
            'passPhrase'     => '0987654321'
        );
        $keys = $rsa->generateKeys($config);
        try {
            $rsa = new RSA(array(
                'passPhrase' => '1234567890',
                'pemString'  => $keys->privateKey->toString()
            ));
            $this->fail('Expected exception not thrown');
        } catch (Crypt\Exception $e) {
        }
    }

    public function testConstructorLoadsPassphrasedKeys()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }
        $rsa = new RSA;
        $config = array(
            'config'         => $this->openSslConf,
            'privateKeyBits' => 512,
            'passPhrase'     => '0987654321'
        );
        $keys = $rsa->generateKeys($config);
        try {
            $rsa = new RSA(array(
                'passPhrase' => '0987654321',
                'pemString'  => $keys->privateKey->toString()
            ));
        } catch (Crypt\Exception $e) {
            $this->fail('Passphrase loading failed of a private key');
        }
    }

    /**
     * @group ZF-8846
     */
    public function testLoadsPublicKeyFromPEMWithoutPrivateKeyAndThrowsNoException()
    {
        $rsa = new RSA;
        $rsa->setPemString($this->_testPemStringPublic);
    }
}
