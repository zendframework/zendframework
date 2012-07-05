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

use Zend\Filter\Decrypt as DecryptFilter;
use Zend\Filter\Encrypt as EncryptFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class DecryptTest extends \PHPUnit_Framework_TestCase
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

        $filter = new DecryptFilter(array('adapter' => 'Mcrypt'));
        $valuesExpected = array(
            'STRING' => 'STRING',
            'ABC1@3' => 'ABC1@3',
            'A b C'  => 'A B C'
        );

        $enc = $filter->getEncryption();
        $filter->setVector('testvect');
        $this->assertEquals('ZendFramework', $enc['key']);
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

        $filter = new DecryptFilter(array('adapter' => 'Openssl'));
        $filter->setPassphrase('zPUp9mCzIrM7xQOEnPJZiDkBwPBV9UlITY0Xd3v4bfIwzJ12yPQCAkcR5BsePGVw
RK6GS5RwXSLrJu9Qj8+fk0wPj6IPY5HvA9Dgwh+dptPlXppeBm3JZJ+92l0DqR2M
ccL43V3Z4JN9OXRAfGWXyrBJNmwURkq7a2EyFElBBWK03OLYVMevQyRJcMKY0ai+
tmnFUSkH2zwnkXQfPUxg9aV7TmGQv/3TkK1SziyDyNm7GwtyIlfcigCCRz3uc77U
Izcez5wgmkpNElg/D7/VCd9E+grTfPYNmuTVccGOes+n8ISJJdW0vYX1xwWv5l
bK22CwD/l7SMBOz4M9XH0Jb0OhNxLza4XMDu0ANMIpnkn1KOcmQ4gB8fmAbBt');
        $filter->setPrivateKey(__DIR__ . '/_files/privatekey.pem');

        $key = $filter->getPrivateKey();
        $this->assertEquals(
            array(__DIR__ . '/_files/privatekey.pem' =>
                  '-----BEGIN RSA PRIVATE KEY-----
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
'),
            $key);
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

        $filter = new DecryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $filter->setVector('testvect');
        $this->assertEquals('testvect', $filter->getVector());
    }

    /**
     * Ensures that the filter allows default encryption
     *
     * @return void
     */
    public function testDefaultDecryption()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Mcrypt extension not installed');
        }

        $filter = new DecryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $filter->setVector('testvect');
        $this->assertEquals(
            array(
                'key'                 => 'testkey',
                'algorithm'           => MCRYPT_BLOWFISH,
                'algorithm_directory' => '',
                'mode'                => MCRYPT_MODE_CBC,
                'mode_directory'      => '',
                'vector'              => 'testvect',
                'salt'                => '',
            ),
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

        $filter = new DecryptFilter(array('adapter' => 'Mcrypt', 'key' => 'testkey'));
        $filter->setVector('testvect');
        $filter->setEncryption(
            array('mode' => MCRYPT_MODE_ECB,
                  'algorithm' => MCRYPT_3DES));
        $this->assertEquals(
            array(
                'mode'                => MCRYPT_MODE_ECB,
                'algorithm'           => MCRYPT_3DES,
                'key'                 => 'testkey',
                'algorithm_directory' => '',
                'mode_directory'      => '',
                'vector'              => 'testvect',
                'salt'                => '',
            ),
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
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

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

        $filter = new DecryptFilter();
        $filter->setAdapter('Openssl');
        $this->assertEquals('Openssl', $filter->getAdapter());

        $filter->setAdapter('Mcrypt');
        $this->assertEquals('Mcrypt', $filter->getAdapter());

        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'does not implement');
        $filter->setAdapter('\\ZendTest\\Filter\\TestAdapter');
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
        $filter = new DecryptFilter();
        $filter->getUnknownMethod();
    }
}

class TestAdapter
{
}
