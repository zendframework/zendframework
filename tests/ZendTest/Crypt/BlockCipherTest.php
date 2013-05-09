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

use Zend\Crypt\BlockCipher;
use Zend\Crypt\Symmetric\Mcrypt;
use Zend\Crypt\Symmetric\Exception;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @group      Zend_Crypt
 */
class BlockCipherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockCipher
     */
    protected $blockCipher;
    protected $plaintext;

    public function setUp()
    {
        try {
            $cipher = new Mcrypt(array(
                'algorithm' => 'aes',
                'mode'      => 'cbc',
                'padding'   => 'pkcs7'
            ));
            $this->blockCipher = new BlockCipher($cipher);
        } catch (Exception\RuntimeException $e) {
            $this->markTestSkipped('Mcrypt is not installed, I cannot execute the BlockCipherTest');
        }
        $this->plaintext = file_get_contents(__DIR__ . '/_files/plaintext');
    }

    public function testSetCipher()
    {
        $mcrypt = new Mcrypt();
        $result = $this->blockCipher->setCipher($mcrypt);
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals($mcrypt, $this->blockCipher->getCipher());
    }

    public function testFactory()
    {
        $this->blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'blowfish'));
        $this->assertTrue($this->blockCipher->getCipher() instanceof Mcrypt);
        $this->assertEquals('blowfish', $this->blockCipher->getCipher()->getAlgorithm());
    }

    public function testFactoryEmptyOptions()
    {
        $this->blockCipher = BlockCipher::factory('mcrypt');
        $this->assertTrue($this->blockCipher->getCipher() instanceof Mcrypt);
    }

    public function testSetKey()
    {
        $result = $this->blockCipher->setKey('test');
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals('test', $this->blockCipher->getKey());
    }

    public function testSetSalt()
    {
        $salt = str_repeat('a', $this->blockCipher->getCipher()->getSaltSize() + 2);
        $result = $this->blockCipher->setSalt($salt);
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals(substr($salt, 0,  $this->blockCipher->getCipher()->getSaltSize()),
                            $this->blockCipher->getSalt());
        $this->assertEquals($salt, $this->blockCipher->getOriginalSalt());
    }

    public function testSetAlgorithm()
    {
        $result = $this->blockCipher->setCipherAlgorithm('blowfish');
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals('blowfish', $this->blockCipher->getCipherAlgorithm());
    }

    public function testSetAlgorithmFail()
    {
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'The algorithm unknown is not supported by Zend\Crypt\Symmetric\Mcrypt');
        $result = $this->blockCipher->setCipherAlgorithm('unknown');

    }

    public function testSetHashAlgorithm()
    {
        $result = $this->blockCipher->setHashAlgorithm('sha1');
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals('sha1', $this->blockCipher->getHashAlgorithm());
    }

    public function testSetKeyIteration()
    {
        $result = $this->blockCipher->setKeyIteration(1000);
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals(1000, $this->blockCipher->getKeyIteration());
    }

    public function testEncryptWithoutData()
    {
        $plaintext = '';
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'The data to encrypt cannot be empty');
        $ciphertext = $this->blockCipher->encrypt($plaintext);
    }

    public function testEncryptErrorKey()
    {
        $plaintext = 'test';
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'No key specified for the encryption');
        $ciphertext = $this->blockCipher->encrypt($plaintext);
    }

    public function testEncryptDecrypt()
    {
        $this->blockCipher->setKey('test');
        $this->blockCipher->setKeyIteration(1000);
        foreach ($this->blockCipher->getCipherSupportedAlgorithms() as $algo) {
            $this->blockCipher->setCipherAlgorithm($algo);
            $encrypted = $this->blockCipher->encrypt($this->plaintext);
            $this->assertTrue(!empty($encrypted));
            $decrypted = $this->blockCipher->decrypt($encrypted);
            $this->assertEquals($decrypted, $this->plaintext);
        }
    }

    public function testEncryptDecryptUsingBinary()
    {
        $this->blockCipher->setKey('test');
        $this->blockCipher->setKeyIteration(1000);
        $this->blockCipher->setBinaryOutput(true);
        foreach ($this->blockCipher->getCipherSupportedAlgorithms() as $algo) {
            $this->blockCipher->setCipherAlgorithm($algo);
            $encrypted = $this->blockCipher->encrypt($this->plaintext);
            $this->assertTrue(!empty($encrypted));
            $decrypted = $this->blockCipher->decrypt($encrypted);
            $this->assertEquals($decrypted, $this->plaintext);
        }
    }

    public function testDecryptAuthFail()
    {
        $this->blockCipher->setKey('test');
        $this->blockCipher->setKeyIteration(1000);
        $encrypted = $this->blockCipher->encrypt($this->plaintext);
        $this->assertTrue(!empty($encrypted));
        // tamper the encrypted data
        $encrypted = substr($encrypted, -1);
        $decrypted = $this->blockCipher->decrypt($encrypted);
        $this->assertTrue($decrypted === false);
    }
}
