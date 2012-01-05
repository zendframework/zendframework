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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Encrypt as EncryptFilter,
    Zend\Filter\Decrypt as DecryptFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class EncryptTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('mcrypt') and !extension_loaded('openssl')) {
            $this->markTestSkipped('This filter needs the mcrypt or openssl extension');
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasicMcrypt()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Mcrypt extension not installed');
        }

        $filter = new EncryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $valuesExpected = array(
            'STRING' => 'STRING',
            'ABC1@3' => 'ABC1@3',
            'A b C'  => 'A B C'
        );

        $enc = $filter->getEncryption();
        $filter->setVector('testvect');
        $this->assertEquals('testkey', $enc['key']);
        foreach ($valuesExpected as $input => $output) {
            $this->assertNotEquals($output, $filter($input));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasicOpenssl()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('Openssl extension not installed');
        }

        $filter = new EncryptFilter(array('adapter' => 'Openssl'));
        $valuesExpected = array(
            'STRING' => 'STRING',
            'ABC1@3' => 'ABC1@3',
            'A b C'  => 'A B C'
        );

        $filter->setPublicKey(__DIR__ . '/_files/publickey.pem');
        $key = $filter->getPublicKey();
        $this->assertEquals(
            array(__DIR__ . '/_files/publickey.pem' =>
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
            $this->assertNotEquals($output, $filter($input));
        }
    }


    /**
     * Ensures that the vector can be set / returned
     *
     * @return void
     */
    public function testGetSetVector()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Mcrypt extension not installed');
        }

        $filter = new EncryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $filter->setVector('testvect');
        $this->assertEquals('testvect', $filter->getVector());
    }

    /**
     * Ensures that the filter allows default encryption
     *
     * @return void
     */
    public function testDefaultEncryption()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Mcrypt extension not installed');
        }

        $filter = new EncryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $filter->setVector('testvect');
        $this->assertEquals(
            array('key' => 'testkey',
                  'algorithm' => MCRYPT_BLOWFISH,
                  'algorithm_directory' => '',
                  'mode' => MCRYPT_MODE_CBC,
                  'mode_directory' => '',
                  'vector' => 'testvect',
                  'salt' => false),
            $filter->getEncryption()
        );
    }

    /**
     * Ensures that the filter allows setting options de/encryption
     *
     * @return void
     */
    public function testGetSetEncryption()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Mcrypt extension not installed');
        }

        $filter = new EncryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $filter->setVector('testvect');
        $filter->setEncryption(
            array('mode' => MCRYPT_MODE_ECB,
                  'algorithm' => MCRYPT_3DES));
        $this->assertEquals(
            array('key' => 'testkey',
                  'algorithm' => MCRYPT_3DES,
                  'algorithm_directory' => '',
                  'mode' => MCRYPT_MODE_ECB,
                  'mode_directory' => '',
                  'vector' => 'testvect',
                  'salt' => false),
            $filter->getEncryption()
        );
    }

    /**
     * Ensures that the filter allows de/encryption
     *
     * @return void
     */
    public function testEncryptionWithDecryptionMcrypt()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Mcrypt extension not installed');
        }

        $filter = new EncryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $filter->setVector('testvect');
        $output = $filter('teststring');

        $this->assertNotEquals('teststring', $output);

        $filter = new DecryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $filter->setVector('testvect');
        $input = $filter($output);
        $this->assertEquals('teststring', trim($input));
    }

    /**
     * Ensures that the filter allows de/encryption
     *
     * @return void
     */
    public function testEncryptionWithDecryptionOpenssl()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('Openssl extension not installed');
        }

        $filter = new EncryptFilter(array('adapter' => 'Openssl'));
        $filter->setPublicKey(__DIR__ . '/_files/publickey.pem');
        $output = $filter('teststring');
        $envelopekeys = $filter->getEnvelopeKey();
        $this->assertNotEquals('teststring', $output);

        $filter = new DecryptFilter(array('adapter' => 'Openssl'));
        $filter->setPassphrase('zPUp9mCzIrM7xQOEnPJZiDkBwPBV9UlITY0Xd3v4bfIwzJ12yPQCAkcR5BsePGVw
RK6GS5RwXSLrJu9Qj8+fk0wPj6IPY5HvA9Dgwh+dptPlXppeBm3JZJ+92l0DqR2M
ccL43V3Z4JN9OXRAfGWXyrBJNmwURkq7a2EyFElBBWK03OLYVMevQyRJcMKY0ai+
tmnFUSkH2zwnkXQfPUxg9aV7TmGQv/3TkK1SziyDyNm7GwtyIlfcigCCRz3uc77U
Izcez5wgmkpNElg/D7/VCd9E+grTfPYNmuTVccGOes+n8ISJJdW0vYX1xwWv5l
bK22CwD/l7SMBOz4M9XH0Jb0OhNxLza4XMDu0ANMIpnkn1KOcmQ4gB8fmAbBt');
        $filter->setPrivateKey(__DIR__ . '/_files/privatekey.pem');
        $filter->setEnvelopeKey($envelopekeys);
        $input = $filter($output);
        $this->assertEquals('teststring', trim($input));
    }

    /**
     * @return void
     */
    public function testSettingAdapterManually()
    {
        if (!extension_loaded('mcrypt') or !extension_loaded('openssl')) {
            $this->markTestSkipped('Mcrypt or Openssl extension not installed');
        }

        $filter = new EncryptFilter();
        $filter->setAdapter('Openssl');
        $this->assertEquals('Openssl', $filter->getAdapter());

        $filter->setAdapter('Mcrypt');
        $this->assertEquals('Mcrypt', $filter->getAdapter());

        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException', 'does not implement');
        $filter->setAdapter('\ZendTest\Filter\TestAdapter2');
    }

    /**
     * @return void
     */
    public function testCallingUnknownMethod()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Mcrypt extension not installed');
        }

        $this->setExpectedException('\Zend\Filter\Exception\BadMethodCallException', 'Unknown method');
        $filter = new EncryptFilter();
        $filter->getUnknownMethod();
    }
}

class TestAdapter2
{
}
