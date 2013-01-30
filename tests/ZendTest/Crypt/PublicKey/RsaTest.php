<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace ZendTest\Crypt;

use Zend\Crypt\PublicKey\Rsa;
use Zend\Crypt\PublicKey\RsaOptions;
use Zend\Crypt\PublicKey\Rsa\Exception;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @group      Zend_Crypt
 */
class RsaTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $testPemString = null;

    /** @var string */
    protected $testPemFile = null;

    /** @var string */
    protected $testPemStringPublic;

    /** @var string */
    protected $testCertificateString;

    /** @var string */
    protected $testCertificateFile;

    /** @var string */
    protected $openSslConf;

    /** @var string */
    protected $userOpenSslConf;


    /** @var Rsa */
    protected $rsa;

    /** @var Rsa */
    protected $rsaBase64Out;

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
        } catch (Rsa\Exception\RuntimeException $e) {
            if (strpos($e->getMessage(), 'requires openssl extension') !== false) {
                $this->markTestSkipped($e->getMessage());
            } else {
                throw $e;
            }
        }

        $this->testPemString = <<<RSAKEY
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

        $this->testPemStringPublic   = <<<RSAKEY
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBANDiE2+Xi/WnO+s120NiiJhNyIButVu6
zxqlVzz0wy2j4kQVUC4ZRZD80IY+4wIiX2YxKBZKGnd2TtPkcJ/ljkUCAwEAAQ==
-----END PUBLIC KEY-----

RSAKEY;
        $this->testCertificateString = <<<CERT
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

        $this->testPemFile = realpath(__DIR__ . '/../_files/test.pem');

        $this->testCertificateFile = realpath(__DIR__ . '/../_files/test.cert');

        $this->userOpenSslConf = realpath(__DIR__ . '/../_files/openssl.cnf');

        $rsaOptions = new RsaOptions(array(
            'private_key'   => new Rsa\PrivateKey($this->testPemString),
        ));
        $this->rsa = new Rsa($rsaOptions);

        $rsaOptions = new RsaOptions(array(
            'private_key'   => new Rsa\PrivateKey($this->testPemString),
            'binary_output' => false
        ));
        $this->rsaBase64Out = new Rsa($rsaOptions);
    }

    public function testFacrotyCreatesInstance()
    {
        $rsa = Rsa::factory(array(
            'hash_algorithm' => 'sha1',
            'binary_output'  => false,
            'private_key'    => $this->testPemString
        ));
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa', $rsa);
        $this->assertInstanceOf('Zend\Crypt\PublicKey\RsaOptions', $rsa->getOptions());
    }

    public function testFacrotyCreatesKeys()
    {
        $rsa = Rsa::factory(array(
            'private_key'    => $this->testPemString,
            'public_key'     => $this->testCertificateString,
        ));
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PrivateKey', $rsa->getOptions()->getPrivateKey());
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PublicKey', $rsa->getOptions()->getPublicKey());
    }

    public function testFacrotyCreatesKeysFromFiles()
    {
        $rsa = Rsa::factory(array(
            'private_key'    => $this->testPemFile,
        ));
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PrivateKey', $rsa->getOptions()->getPrivateKey());
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PublicKey', $rsa->getOptions()->getPublicKey());
    }

    public function testFacrotyCreatesJustPublicKey()
    {
        $rsa = Rsa::factory(array(
            'public_key'     => $this->testCertificateString,
        ));
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PublicKey', $rsa->getOptions()->getPublicKey());
        $this->assertNull($rsa->getOptions()->getPrivateKey());
    }

    public function testConstructorCreatesInstanceWithDefaultOptions()
    {
        $rsa = new Rsa();
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa', $rsa);
        $this->assertEquals('sha1', $rsa->getOptions()->getHashAlgorithm());
        $this->assertEquals(OPENSSL_ALGO_SHA1, $rsa->getOptions()->getOpensslSignatureAlgorithm());
        $this->assertTrue($rsa->getOptions()->getBinaryOutput());
    }

    public function testPrivateKeyInstanceCreation()
    {
        $privateKey = Rsa\PrivateKey::fromFile($this->testPemFile);
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PrivateKey', $privateKey);

        $privateKey = new Rsa\PrivateKey($this->testPemString);
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PrivateKey', $privateKey);
    }

    public function testPublicKeyInstanceCreation()
    {
        $publicKey = new Rsa\PublicKey($this->testPemStringPublic);
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PublicKey', $publicKey);

        $publicKey = Rsa\PublicKey::fromFile($this->testCertificateFile);
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PublicKey', $publicKey);

        $publicKey = new Rsa\PublicKey($this->testCertificateString);
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PublicKey', $publicKey);
    }

    public function testSignGeneratesExpectedBinarySignature()
    {
        $signature = $this->rsa->sign('1234567890');
        $this->assertEquals(
            'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
            base64_encode($signature)
        );
    }

    public function testSignGeneratesExpectedBinarySignatureUsingExternalKey()
    {
        $rsaOptions = new RsaOptions(array(
            'public_key'    => new Rsa\PublicKey($this->testCertificateString),
            'binary_output' => true, // output as binary
        ));

        $rsa        = new Rsa($rsaOptions);
        $privateKey = new Rsa\PrivateKey($this->testPemString);
        $signature  = $rsa->sign('1234567890', $privateKey);
        $this->assertEquals(
            'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
            base64_encode($signature)
        );
    }

    public function testSignGeneratesExpectedBase64Signature()
    {
        $signature = $this->rsaBase64Out->sign('1234567890');
        $this->assertEquals(
            'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==',
            $signature
        );
    }

    public function testVerifyVerifiesBinarySignatures()
    {
        $signature = $this->rsa->sign('1234567890');
        $result    = $this->rsa->verify('1234567890', $signature);

        $this->assertTrue($result);
    }

    public function testVerifyVerifiesBinarySignaturesUsingCertificate()
    {
        $rsaOptions = new RsaOptions(array(
            'public_key'   => new Rsa\PublicKey($this->testCertificateString),
            'binary_output' => true,
        ));

        $rsa        = new Rsa($rsaOptions);
        $privateKey = new Rsa\PrivateKey($this->testPemString);
        $signature  = $rsa->sign('1234567890', $privateKey);
        $result     = $rsa->verify('1234567890', $signature);

        $this->assertTrue($result);
    }

    public function testVerifyVerifiesBase64Signatures()
    {
        $signature = $this->rsaBase64Out->sign('1234567890');
        $result    = $this->rsaBase64Out->verify('1234567890', $signature);

        $this->assertSame(true, $result);
    }

    public function testEncryptionWithPublicKey()
    {
        $publicKey  = new Rsa\PublicKey($this->testCertificateString);
        $privateKey = new Rsa\PrivateKey($this->testPemString);
        $encrypted  = $publicKey->encrypt('1234567890');

        $this->assertEquals('1234567890', $privateKey->decrypt($encrypted));
    }

    public function testEncryptionWithPrivateKey()
    {
        $publicKey  = new Rsa\PublicKey($this->testCertificateString);
        $privateKey = new Rsa\PrivateKey($this->testPemString);
        $encrypted  = $privateKey->encrypt('1234567890');

        $this->assertEquals('1234567890', $publicKey->decrypt($encrypted));
    }

    public function testEncryptionWithOwnKeys()
    {
        $encrypted = $this->rsa->encrypt('1234567890');

        $this->assertEquals('1234567890', $this->rsa->decrypt($encrypted));
    }

    public function testEncryptionUsingPublicKeyEncryption()
    {
        $encrypted = $this->rsa->encrypt('1234567890', $this->rsa->getOptions()->getPublicKey());

        $this->assertEquals(
            '1234567890',
            $this->rsa->decrypt($encrypted, $this->rsa->getOptions()->getPrivateKey())
        );
    }

    public function testEncryptionUsingPublicKeyBase64Encryption()
    {
        $encrypted = $this->rsaBase64Out->encrypt('1234567890', $this->rsaBase64Out->getOptions()->getPublicKey());

        $this->assertEquals(
            '1234567890',
            $this->rsaBase64Out->decrypt(
                $encrypted,
                $this->rsaBase64Out->getOptions()->getPrivateKey()
            )
        );
    }

    public function testBase64EncryptionUsingCertificatePublicKeyEncryption()
    {
        $rsa1 = new Rsa(new RsaOptions(array(
            'public_key'    => new Rsa\PublicKey($this->testCertificateString),
            'binary_output' => false, // output as base 64
        )));

        $rsa2 = new Rsa(new RsaOptions(array(
            'private_key'   => new Rsa\PrivateKey($this->testPemString),
            'binary_output' => false, // output as base 64
        )));

        $encrypted = $rsa1->encrypt('1234567890', $rsa1->getOptions()->getPublicKey());

        $this->assertEquals(
            '1234567890',
            $rsa1->decrypt(base64_decode($encrypted), $rsa2->getOptions()->getPrivateKey())
        );
    }

    public function testEncryptionUsingPrivateKeyEncryption()
    {
        $encrypted = $this->rsa->encrypt('1234567890', $this->rsa->getOptions()->getPrivateKey());
        $decrypted = $this->rsa->decrypt($encrypted, $this->rsa->getOptions()->getPublicKey());

        $this->assertEquals('1234567890', $decrypted);
    }

    public function testEncryptionUsingPrivateKeyBase64Encryption()
    {
        $encrypted = $this->rsaBase64Out->encrypt('1234567890', $this->rsaBase64Out->getOptions()->getPrivateKey());
        $decrypted = $this->rsaBase64Out->decrypt(
            base64_decode($encrypted),
            $this->rsaBase64Out->getOptions()->getPublicKey()
        );

        $this->assertEquals('1234567890', $decrypted);
    }

    public function testKeyGenerationWithDefaults()
    {
        if (!$this->openSslConf) {
            $this->markTestSkipped('No openssl.cnf found or defined; cannot generate keys');
        }

        $rsa = new Rsa();
        $rsa->getOptions()->generateKeys();

        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PrivateKey', $rsa->getOptions()->getPrivateKey());
        $this->assertInstanceOf('Zend\Crypt\PublicKey\Rsa\PublicKey', $rsa->getOptions()->getPublicKey());
    }

    public function testKeyGenerationWithUserOpensslConfig()
    {
        $rsaOptions  = new RsaOptions();
        $rsaOptions->generateKeys(array(
            'config'           => $this->userOpenSslConf,
            'private_key_bits' => 512,
        ));

        $this->assertInstanceOf('Zend\\Crypt\\PublicKey\\Rsa\\PrivateKey', $rsaOptions->getPrivateKey());
        $this->assertInstanceOf('Zend\\Crypt\\PublicKey\\Rsa\\PublicKey', $rsaOptions->getPublicKey());
    }

    public function testKeyGenerationCreatesPassphrasedPrivateKey()
    {
        $rsaOptions  = new RsaOptions(array(
            'pass_phrase' => '0987654321'
        ));
        $rsaOptions->generateKeys(array(
            'config'           => $this->userOpenSslConf,
            'private_key_bits' => 512,
        ));

        try {
            $rsa = Rsa::factory(array(
                'pass_phrase' => '1234567890',
                'private_key' => $rsaOptions->getPrivateKey()->toString()
            ));
            $this->fail('Expected passphrase mismatch exception not thrown');
        } catch (Exception\RuntimeException $e) {
        }
    }

    public function testRsaLoadsPassphrasedKeys()
    {
        $rsaOptions  = new RsaOptions(array(
            'pass_phrase' => '0987654321'
        ));
        $rsaOptions->generateKeys(array(
            'config'           => $this->userOpenSslConf,
            'private_key_bits' => 512,
        ));

        Rsa::factory(array(
            'pass_phrase' => '0987654321',
            'private_key' => $rsaOptions->getPrivateKey()->toString(),
        ));
    }

    public function testZf3492Base64DetectDecrypt()
    {
        $data = 'vNKINbWV6qUKGsmawN8ii0mak7PPNoVQPC7fwXJOgMNfCgdT+9W4PUte4fic6U4A6fMra4gv7NCTESxap2qpBQ==';
        $this->assertEquals('1234567890', $this->rsa->decrypt($data));
    }

    public function testZf3492Base64DetectVerify()
    {
        $data = 'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==';
        $this->assertTrue($this->rsa->verify('1234567890', $data));
    }

    public function testDecryptBase64()
    {
        $data = 'vNKINbWV6qUKGsmawN8ii0mak7PPNoVQPC7fwXJOgMNfCgdT+9W4PUte4fic6U4A6fMra4gv7NCTESxap2qpBQ==';
        $this->assertEquals('1234567890', $this->rsa->decrypt($data, null, Rsa::MODE_BASE64));
    }

    public function testDecryptCorruptBase64()
    {
        $data = 'vNKINbWV6qUKGsmawN8ii0mak7PPNoVQPC7fwXJOgMNfCgdT+9W4PUte4fic6U4A6fMra4gv7NCTESxap2qpBQ==';
        $this->setExpectedException('Zend\Crypt\PublicKey\Rsa\Exception\RuntimeException');
        $this->rsa->decrypt(base64_decode($data), null, Rsa::MODE_BASE64);
    }

    public function testDecryptRaw()
    {
        $data = 'vNKINbWV6qUKGsmawN8ii0mak7PPNoVQPC7fwXJOgMNfCgdT+9W4PUte4fic6U4A6fMra4gv7NCTESxap2qpBQ==';
        $this->assertEquals('1234567890', $this->rsa->decrypt(base64_decode($data), null, Rsa::MODE_RAW));
    }

    public function testDecryptCorruptRaw()
    {
        $data = 'vNKINbWV6qUKGsmawN8ii0mak7PPNoVQPC7fwXJOgMNfCgdT+9W4PUte4fic6U4A6fMra4gv7NCTESxap2qpBQ==';
        $this->setExpectedException('Zend\Crypt\PublicKey\Rsa\Exception\RuntimeException');
        $this->rsa->decrypt($data, null, Rsa::MODE_RAW);
    }

    public function testVerifyBase64()
    {
        $data = 'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==';
        $this->assertTrue($this->rsa->verify('1234567890', $data, null, Rsa::MODE_BASE64));
    }

    public function testVerifyCorruptBase64()
    {
        $data = 'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==';
        $this->assertFalse($this->rsa->verify('1234567890', base64_decode($data), null, Rsa::MODE_BASE64));
    }

    public function testVerifyRaw()
    {
        $data = 'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==';
        $this->assertTrue($this->rsa->verify('1234567890', base64_decode($data), null, Rsa::MODE_RAW));
    }

    public function testVerifyCorruptRaw()
    {
        $data = 'sMHpp3u6DNecIm5RIkDD3xyKaH6qqP8roUWDs215iOGHehfK1ypqwoETKNP7NaksGS2C1Up813ixlGXkipPVbQ==';
        $this->assertFalse($this->rsa->verify('1234567890', $data, null, Rsa::MODE_RAW));
    }
}
