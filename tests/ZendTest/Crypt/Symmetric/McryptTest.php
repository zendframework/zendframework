<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace ZendTest\Crypt\Symmetric;

use Zend\Crypt\Symmetric\Exception;
use Zend\Crypt\Symmetric\Mcrypt;
use Zend\Crypt\Symmetric\Padding\PKCS7;
use Zend\Config\Config;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @group      Zend_Crypt
 */
class McryptTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mcrypt */
    protected $mcrypt;
    protected $key;
    protected $salt;
    protected $plaintext;

    public function setUp()
    {
        try {
            $this->mcrypt = new Mcrypt();
        } catch (Exception\RuntimeException $e) {
            $this->markTestSkipped('Mcrypt is not installed, I cannot execute the BlockCipherTest');
        }
        for ($i = 0; $i < 128; $i++) {
            $this->key .= chr(rand(0, 255));
            $this->salt .= chr(rand(0, 255));
        }
        $this->plaintext = file_get_contents(__DIR__ . '/../_files/plaintext');
    }

    public function testConstructByParams()
    {
        $options = array(
            'algorithm' => 'blowfish',
            'mode'      => 'cfb',
            'key'       => $this->key,
            'salt'      => $this->salt,
            'padding'   => 'pkcs7'
        );
        $mcrypt  = new Mcrypt($options);
        $this->assertTrue($mcrypt instanceof Mcrypt);
        $this->assertEquals($mcrypt->getAlgorithm(), MCRYPT_BLOWFISH);
        $this->assertEquals($mcrypt->getMode(), MCRYPT_MODE_CFB);
        $this->assertEquals($mcrypt->getKey(), substr($this->key, 0, $mcrypt->getKeySize()));
        $this->assertEquals($mcrypt->getSalt(), substr($this->salt, 0, $mcrypt->getSaltSize()));
        $this->assertTrue($mcrypt->getPadding() instanceof PKCS7);
    }

    public function testConstructByConfig()
    {
        $options = array(
            'algorithm' => 'blowfish',
            'mode'      => 'cfb',
            'key'       => $this->key,
            'salt'      => $this->salt,
            'padding'   => 'pkcs7'
        );
        $config  = new Config($options);
        $mcrypt  = new Mcrypt($config);
        $this->assertTrue($mcrypt instanceof Mcrypt);
        $this->assertEquals($mcrypt->getAlgorithm(), MCRYPT_BLOWFISH);
        $this->assertEquals($mcrypt->getMode(), MCRYPT_MODE_CFB);
        $this->assertEquals($mcrypt->getKey(), substr($this->key, 0, $mcrypt->getKeySize()));
        $this->assertEquals($mcrypt->getSalt(), substr($this->salt, 0, $mcrypt->getSaltSize()));
        $this->assertTrue($mcrypt->getPadding() instanceof PKCS7);
    }

    public function testConstructWrongParam()
    {
        $options = 'test';
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException',
                                    'The options parameter must be an array, a Zend\Config\Config object or a Traversable');
        $mcrypt = new Mcrypt($options);
    }

    public function testSetAlgorithm()
    {
        $this->mcrypt->setAlgorithm(MCRYPT_BLOWFISH);
        $this->assertEquals($this->mcrypt->getAlgorithm(), MCRYPT_BLOWFISH);
    }

    public function testSetWrongAlgorithm()
    {
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException',
                                    'The algorithm test is not supported by Zend\Crypt\Symmetric\Mcrypt');
        $this->mcrypt->setAlgorithm('test');
    }

    public function testSetKey()
    {
        $result = $this->mcrypt->setKey($this->key);
        $this->assertInstanceOf('Zend\Crypt\Symmetric\Mcrypt', $result);
        $this->assertEquals($result, $this->mcrypt);
    }

    public function testSetEmptyKey()
    {
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException',
                                    'The key cannot be empty');
        $result = $this->mcrypt->setKey('');
    }

    public function testSetShortKey()
    {
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException');
        $result = $this->mcrypt->setKey('short');
        $output = $this->mcrypt->encrypt('test');
    }

    public function testSetSalt()
    {
        $this->mcrypt->setSalt($this->salt);
        $this->assertEquals(substr($this->salt, 0, $this->mcrypt->getSaltSize()),
                            $this->mcrypt->getSalt());
        $this->assertEquals($this->salt, $this->mcrypt->getOriginalSalt());
    }

    /**
     * @expectedException Zend\Crypt\Symmetric\Exception\InvalidArgumentException
     */
    public function testShortSalt()
    {
        $this->mcrypt->setSalt('short');
    }

    public function testSetMode()
    {
        $this->mcrypt->setMode(MCRYPT_MODE_CFB);
        $this->assertEquals(MCRYPT_MODE_CFB, $this->mcrypt->getMode());
    }

    public function testSetWrongMode()
    {
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException',
                                    'The mode xxx is not supported by Zend\Crypt\Symmetric\Mcrypt');
        $this->mcrypt->setMode('xxx');
    }

    public function testEncryptDecrypt()
    {
        $this->mcrypt->setKey($this->key);
        $this->mcrypt->setPadding(new PKCS7());
        $this->mcrypt->setSalt($this->salt);
        foreach ($this->mcrypt->getSupportedAlgorithms() as $algo) {
            foreach ($this->mcrypt->getSupportedModes() as $mode) {
                $this->mcrypt->setAlgorithm($algo);
                $this->mcrypt->setMode($mode);
                $encrypted = $this->mcrypt->encrypt($this->plaintext);
                $this->assertTrue(!empty($encrypted));
                $decrypted = $this->mcrypt->decrypt($encrypted);
                $this->assertTrue($decrypted !== false);
                $this->assertEquals($decrypted, $this->plaintext);
            }
        }
    }

    public function testEncryptWithoutKey()
    {
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException');
        $ciphertext = $this->mcrypt->encrypt('test');
    }

    public function testEncryptEmptyData()
    {
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException',
                                    'The data to encrypt cannot be empty');
        $ciphertext = $this->mcrypt->encrypt('');
    }

    public function testEncryptWihoutSalt()
    {
        $this->mcrypt->setKey($this->key);
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException',
                                    'The salt (IV) cannot be empty');
        $ciphertext = $this->mcrypt->encrypt($this->plaintext);
    }

    public function testDecryptEmptyData()
    {
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException',
                                    'The data to decrypt cannot be empty');
        $ciphertext = $this->mcrypt->decrypt('');
    }

    public function testDecryptWithoutKey()
    {
        $this->setExpectedException('Zend\Crypt\Symmetric\Exception\InvalidArgumentException');
        $this->mcrypt->decrypt($this->plaintext);
    }
}
