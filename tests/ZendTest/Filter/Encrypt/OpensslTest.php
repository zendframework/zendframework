<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter\Encrypt;

use Zend\Filter\Encrypt\Openssl as OpensslEncryption;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class OpensslTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('This filter needs the openssl extension');
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasicOpenssl()
    {
        $filter = new OpensslEncryption(__DIR__ . '/../_files/publickey.pem');
        $valuesExpected = array(
            'STRING' => 'STRING',
            'ABC1@3' => 'ABC1@3',
            'A b C'  => 'A B C'
        );

        $key = $filter->getPublicKey();
        $this->assertEquals(
            array(__DIR__ . '/../_files/publickey.pem' =>
                  '-----BEGIN CERTIFICATE-----
MIIC3jCCAkegAwIBAgIBADANBgkqhkiG9w0BAQQFADCBtDELMAkGA1UEBhMCTkwx
FjAUBgNVBAgTDU5vb3JkLUhvbGxhbmQxEDAOBgNVBAcTB1phYW5kYW0xFzAVBgNV
BAoTDk1vYmlsZWZpc2guY29tMR8wHQYDVQQLExZDZXJ0aWZpY2F0aW9uIFNlcnZp
Y2VzMRowGAYDVQQDExFNb2JpbGVmaXNoLmNvbSBDQTElMCMGCSqGSIb3DQEJARYW
Y29udGFjdEBtb2JpbGVmaXNoLmNvbTAeFw0wNzA2MDcxNzM1NTNaFw0wODA2MDYx
NzM1NTNaMIG0MQswCQYDVQQGEwJOTDEWMBQGA1UECBMNTm9vcmQtSG9sbGFuZDEQ
MA4GA1UEBxMHWmFhbmRhbTEXMBUGA1UEChMOTW9iaWxlZmlzaC5jb20xHzAdBgNV
BAsTFkNlcnRpZmljYXRpb24gU2VydmljZXMxGjAYBgNVBAMTEU1vYmlsZWZpc2gu
Y29tIENBMSUwIwYJKoZIhvcNAQkBFhZjb250YWN0QG1vYmlsZWZpc2guY29tMIGf
MA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDKTIp7FntJt1BioBZ0lmWBE8Cyznge
GCHNMcAC4JLbi1Y0LwT4CSaQarbvAqBRmc+joHX+rcURm89wOibRaThrrZcvgl2p
omzu7shJc0ObiRZC8H7pxTkZ1HHjN8cRSQlOHkcdtE9yoiSGSO+zZ9K5ReU1DOsF
FDD4V7XpcNU63QIDAQABMA0GCSqGSIb3DQEBBAUAA4GBAFQ22OU/PAN7rRDr23NS
2XkpSngwZWeHoFW1D2gRvHHRlqg5Q8KZHQAALd5PEFakehdn03NG6yEdnhXpqKT/
5jYy6v3b+zwEvY82EUieMldovdnpsS1EScjjvPfQ1lSgcTHT2QX5MjNv13xLnOgh
PIDs9E7uuizAKDhRRRvho8BS
-----END CERTIFICATE-----
'),
            $key);
        foreach ($valuesExpected as $input => $output) {
            $this->assertNotEquals($output, $filter->encrypt($input));
        }
    }

    /**
     * Ensures that the filter allows de/encryption
     *
     * @return void
     */
    public function testEncryptionWithDecryptionOpenssl()
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

        $filter = new OpensslEncryption();
        $filter->setPublicKey(__DIR__ . '/../_files/publickey.pem');
        $output = $filter->encrypt('teststring');
        $envelopekeys = $filter->getEnvelopeKey();
        $this->assertNotEquals('teststring', $output);

        $filter->setPassphrase('zPUp9mCzIrM7xQOEnPJZiDkBwPBV9UlITY0Xd3v4bfIwzJ12yPQCAkcR5BsePGVw
RK6GS5RwXSLrJu9Qj8+fk0wPj6IPY5HvA9Dgwh+dptPlXppeBm3JZJ+92l0DqR2M
ccL43V3Z4JN9OXRAfGWXyrBJNmwURkq7a2EyFElBBWK03OLYVMevQyRJcMKY0ai+
tmnFUSkH2zwnkXQfPUxg9aV7TmGQv/3TkK1SziyDyNm7GwtyIlfcigCCRz3uc77U
Izcez5wgmkpNElg/D7/VCd9E+grTfPYNmuTVccGOes+n8ISJJdW0vYX1xwWv5l
bK22CwD/l7SMBOz4M9XH0Jb0OhNxLza4XMDu0ANMIpnkn1KOcmQ4gB8fmAbBt');
        $filter->setPrivateKey(__DIR__ . '/../_files/privatekey.pem');
        $filter->setEnvelopeKey($envelopekeys);
        $input = $filter->decrypt($output);
        $this->assertEquals('teststring', trim($input));
    }

    /**
     * Ensures that the filter allows de/encryption
     *
     * @return void
     */
    public function testEncryptionWithDecryptionSingleOptionOpenssl()
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

        $filter = new OpensslEncryption();
        $filter->setPublicKey(__DIR__ . '/../_files/publickey.pem');
        $output = $filter->encrypt('teststring');
        $envelopekeys = $filter->getEnvelopeKey();
        $this->assertNotEquals('teststring', $output);

        $phrase = 'zPUp9mCzIrM7xQOEnPJZiDkBwPBV9UlITY0Xd3v4bfIwzJ12yPQCAkcR5BsePGVw
RK6GS5RwXSLrJu9Qj8+fk0wPj6IPY5HvA9Dgwh+dptPlXppeBm3JZJ+92l0DqR2M
ccL43V3Z4JN9OXRAfGWXyrBJNmwURkq7a2EyFElBBWK03OLYVMevQyRJcMKY0ai+
tmnFUSkH2zwnkXQfPUxg9aV7TmGQv/3TkK1SziyDyNm7GwtyIlfcigCCRz3uc77U
Izcez5wgmkpNElg/D7/VCd9E+grTfPYNmuTVccGOes+n8ISJJdW0vYX1xwWv5l
bK22CwD/l7SMBOz4M9XH0Jb0OhNxLza4XMDu0ANMIpnkn1KOcmQ4gB8fmAbBt';
        $filter->setPrivateKey(__DIR__ . '/../_files/privatekey.pem', $phrase);
        $filter->setEnvelopeKey($envelopekeys);
        $input = $filter->decrypt($output);
        $this->assertEquals('teststring', trim($input));
    }

    /**
     * @return void
     */
    public function testSetPublicKey()
    {
        $filter = new OpensslEncryption();

        $r = $filter->setPublicKey(array('private' => __DIR__ . '/../_files/publickey.pem'));
        $this->assertSame($filter, $r);

        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'not valid');
        $filter->setPublicKey(123);

    }

    /**
     * @return void
     */
    public function testSetPrivateKey()
    {
        $filter = new OpensslEncryption();

        $filter->setPrivateKey(array('public' => __DIR__ . '/../_files/privatekey.pem'));
        $test = $filter->getPrivateKey();
        $this->assertEquals(array(
            __DIR__ . '/../_files/privatekey.pem' => '-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQDKTIp7FntJt1BioBZ0lmWBE8CyzngeGCHNMcAC4JLbi1Y0LwT4
CSaQarbvAqBRmc+joHX+rcURm89wOibRaThrrZcvgl2pomzu7shJc0ObiRZC8H7p
xTkZ1HHjN8cRSQlOHkcdtE9yoiSGSO+zZ9K5ReU1DOsFFDD4V7XpcNU63QIDAQAB
AoGBALr0XY4/SpTnmpxqwhXg39GYBZ+5e/yj5KkTbxW5oT7P2EzFn1vyaPdSB9l+
ndaLxP68zg8dXGBXlC9tLm6dRQtocGupUPB1HOEQbUIlQdiKF/W7/8w6uzLNXdid
qCSLrSJ4cfkYKtS29Xi6qooRw2DOvUFngXy/ELtmTeiBcihpAkEA8+oUesTET+TO
IYM0+l5JrTOpCPZt+aY4JPmWoKz9bshJT/DP2KPgmqd8/Vy+i23yIfOwUxbpwbna
aKzNPi/uywJBANRSl7RNL7jh1BJRQC7+mvUVTE8iQwbyGtIipcLC7bxwhNQzuPKS
P4o/a1+HEVB9Nv1Em7DqKTwBnlkJvaFZ3/cCQQCcvx0SGEkgHqXpG2x8SQOH7t7+
B399I7iI6mxGLWVgQA389YBcdFPujxvfpi49ZBZqgzQY8WyfNlSJWCM9h4gpAkAu
qxzHN7QGmjSn9g36hmH+/rhwKGK9MxfsGkt+/KOOqNi5X8kGIFkxBPGP5LtMisk8
cAkcoMuBcgWhIn/46C1PAkEAzLK/ibrdMQLOdO4SuDgj/2nc53NZ3agl61ew8Os6
d/fxzPfuO/bLpADozTAnYT9Hu3wPrQVLeAfCp0ojqH7DYg==
-----END RSA PRIVATE KEY-----
'), $test);


        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'not valid');
        $filter->setPrivateKey(123);

    }

    /**
     * @return void
     */
    public function testToString()
    {
        $filter = new OpensslEncryption();
        $this->assertEquals('Openssl', $filter->toString());
    }

    /**
     * @return void
     */
    public function testInvalidDecryption()
    {
        $filter = new OpensslEncryption();
        try {
            $filter->decrypt('unknown');
            $this->fail();
        } catch (\Zend\Filter\Exception\RuntimeException $e) {
            $this->assertContains('Please give a private key', $e->getMessage());
        }

        $filter->setPrivateKey(array('public' => __DIR__ . '/../_files/privatekey.pem'));
        try {
            $filter->decrypt('unknown');
            $this->fail();
        } catch (\Zend\Filter\Exception\RuntimeException $e) {
            $this->assertContains('Please give an envelope key', $e->getMessage());
        }

        $filter->setEnvelopeKey('unknown');
        try {
            $filter->decrypt('unknown');
            $this->fail();
        } catch (\Zend\Filter\Exception\RuntimeException $e) {
            $this->assertContains('was not able to decrypt', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testEncryptionWithoutPublicKey()
    {
        $filter = new OpensslEncryption();

        $this->setExpectedException('\Zend\Filter\Exception\RuntimeException', 'without public key');
        $filter->encrypt('unknown');
    }

    /**
     * @return void
     */
    public function testMultipleOptionsAtInitiation()
    {
        $passphrase = 'zPUp9mCzIrM7xQOEnPJZiDkBwPBV9UlITY0Xd3v4bfIwzJ12yPQCAkcR5BsePGVw
RK6GS5RwXSLrJu9Qj8+fk0wPj6IPY5HvA9Dgwh+dptPlXppeBm3JZJ+92l0DqR2M
ccL43V3Z4JN9OXRAfGWXyrBJNmwURkq7a2EyFElBBWK03OLYVMevQyRJcMKY0ai+
tmnFUSkH2zwnkXQfPUxg9aV7TmGQv/3TkK1SziyDyNm7GwtyIlfcigCCRz3uc77U
Izcez5wgmkpNElg/D7/VCd9E+grTfPYNmuTVccGOes+n8ISJJdW0vYX1xwWv5l
bK22CwD/l7SMBOz4M9XH0Jb0OhNxLza4XMDu0ANMIpnkn1KOcmQ4gB8fmAbBt';
        $filter = new OpensslEncryption(array(
            'public' => __DIR__ . '/../_files/publickey.pem',
            'passphrase' => $passphrase,
            'private' => __DIR__ . '/../_files/privatekey.pem'));
        $public = $filter->getPublicKey();
        $this->assertFalse(empty($public));
        $this->assertEquals($passphrase, $filter->getPassphrase());
    }

    /**
     * Ensures that the filter allows de/encryption
     *
     * @return void
     */
    public function testEncryptionWithDecryptionWithPackagedKeys()
    {
        $filter = new OpensslEncryption();
        $filter->setPublicKey(__DIR__ . '/../_files/publickey.pem');
        $filter->setPackage(true);
        $output = $filter->encrypt('teststring');
        $this->assertNotEquals('teststring', $output);

        $phrase = 'zPUp9mCzIrM7xQOEnPJZiDkBwPBV9UlITY0Xd3v4bfIwzJ12yPQCAkcR5BsePGVw
RK6GS5RwXSLrJu9Qj8+fk0wPj6IPY5HvA9Dgwh+dptPlXppeBm3JZJ+92l0DqR2M
ccL43V3Z4JN9OXRAfGWXyrBJNmwURkq7a2EyFElBBWK03OLYVMevQyRJcMKY0ai+
tmnFUSkH2zwnkXQfPUxg9aV7TmGQv/3TkK1SziyDyNm7GwtyIlfcigCCRz3uc77U
Izcez5wgmkpNElg/D7/VCd9E+grTfPYNmuTVccGOes+n8ISJJdW0vYX1xwWv5l
bK22CwD/l7SMBOz4M9XH0Jb0OhNxLza4XMDu0ANMIpnkn1KOcmQ4gB8fmAbBt';
        $filter->setPrivateKey(__DIR__ . '/../_files/privatekey.pem', $phrase);
        $input = $filter->decrypt($output);
        $this->assertEquals('teststring', trim($input));
    }

    /**
     * Ensures that the filter allows de/encryption
     *
     * @return void
     */
    public function testEncryptionWithDecryptionAndCompressionWithPackagedKeys()
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

        if (!extension_loaded('bz2')) {
            $this->markTestSkipped('Bz2 extension for compression test needed');
        }

        $filter = new OpensslEncryption();
        $filter->setPublicKey(__DIR__ . '/../_files/publickey.pem');
        $filter->setPackage(true);
        $filter->setCompression('bz2');
        $output = $filter->encrypt('teststring');
        $this->assertNotEquals('teststring', $output);

        $phrase = 'zPUp9mCzIrM7xQOEnPJZiDkBwPBV9UlITY0Xd3v4bfIwzJ12yPQCAkcR5BsePGVw
RK6GS5RwXSLrJu9Qj8+fk0wPj6IPY5HvA9Dgwh+dptPlXppeBm3JZJ+92l0DqR2M
ccL43V3Z4JN9OXRAfGWXyrBJNmwURkq7a2EyFElBBWK03OLYVMevQyRJcMKY0ai+
tmnFUSkH2zwnkXQfPUxg9aV7TmGQv/3TkK1SziyDyNm7GwtyIlfcigCCRz3uc77U
Izcez5wgmkpNElg/D7/VCd9E+grTfPYNmuTVccGOes+n8ISJJdW0vYX1xwWv5l
bK22CwD/l7SMBOz4M9XH0Jb0OhNxLza4XMDu0ANMIpnkn1KOcmQ4gB8fmAbBt';
        $filter->setPrivateKey(__DIR__ . '/../_files/privatekey.pem', $phrase);
        $input = $filter->decrypt($output);
        $this->assertEquals('teststring', trim($input));
    }
}
