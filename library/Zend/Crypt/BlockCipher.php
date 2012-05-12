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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Crypt;

use Zend\Crypt\Symmetric\SymmetricInterface,
    Zend\Crypt\Hmac,
    Zend\Crypt\Tool,
    Zend\Crypt\Key\Derivation\PBKDF2,
    Zend\Math\Math;

/**
 * Encrypt using a symmetric cipher then authenticate using HMAC (SHA-256)
 * 
 * @uses       Zend\Crypt\Hmac
 * @uses       Zend\Crypt\Tool
 * @uses       Zend\Crypt\Key\Derivation\PBKDF2
 * @uses       Zend\Math\Math
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BlockCipher
{
    const KEY_DERIV_HMAC  = 'sha256';
    /**
     * Simmetric cipher
     * 
     * @var SymmetricInterface 
     */
    protected $cipher;   
    /**
     * Hash algorithm fot HMAC
     * 
     * @var string 
     */
    protected $hash = 'sha256';
    /**
     * The output is binary?
     * 
     * @var boolean 
     */
    protected $binaryOutput = false;
    /**
     * User's key
     * 
     * @var string 
     */
    protected $key;
    /**
     * Number of iterations for PBKDF2
     * 
     * @var string 
     */
    protected $keyIteration = 5000;
    /**
     * Constructor
     * 
     * @param array $options 
     */
    public function __construct(SymmetricInterface $cipher = null) 
    { 
        $this->cipher = $cipher; 
    }
    /**
     * Set the symmetric cipher
     * 
     * @param  SymmetricInterface $cipher 
     * @return BlockCipher
     */
    public function setCipher(SymmetricInterface $cipher)
    {
        $this->cipher = $cipher; 
        return $this;
    }
    /**
     * Get symmetric cipher
     * 
     * @return SymmetricInterface 
     */
    public function getCipher()
    {
        return $this->cipher;
    }
    /**
     * Set the number of iterations for PBKDF2
     * 
     * @param  integer $num 
     * @return BlockCipher
     */
    public function setKeyIteration($num)
    {
        $this->keyIteration = (integer) $num;
        return $this;
    }
    /**
     * Get the number of iterations for PBKDF2
     * 
     * @return integer 
     */
    public function getKeyIteration()
    {
        return $this->keyIteration;
    }
    /**
     * Enable/disable the binary output
     * 
     * @param boolean $value 
     */
    public function setBinaryOutput($value)
    {
        $this->binary = (boolean) $value;
        return $this;
    }
    /**
     * Get the value of binary output
     * 
     * @return boolean 
     */
    public function getBinaryOutput()
    {
        return $this->binary;
    }
    /**
     * Set the encryption/decryption key
     * 
     * @param  string $key
     * @return BlockCipher 
     */
    public function setKey($key) 
    {
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('The key cannot be empty');
        }    
        $this->key = $key;
        return $this;
    }
    /**
     * Get the key
     * 
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }
    /**
     * Set algorithm of the symmetric cipher
     * 
     * @param  string $algo
     * @return BlockCipher 
     */
    public function setCipherAlgorithm($algo) 
    {
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }
        try {
            $this->cipher->setAlgorithm($algo);
        } catch (\Zend\Crypt\Symmetric\Exception\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        }
        return $this;
    }
    /**
     * Get the cipher algorithm
     * 
     * @return string|false 
     */
    public function getCipherAlgorithm()
    {
        if (!empty($this->cipher)) {
            return $this->cipher->getAlgorithm();
        }
        return false;
    }
    /**
     * Get the supported algorithms of the symmetric cipher
     * 
     * @return array 
     */
    public function getCipherSupportedAlgorithms()
    {
        if (!empty($this->cipher)) {
            return $this->cipher->getSupportedAlgorithms();
        }
        return array();
    }
    /**
     * Set the hash algorithm for HMAC authentication
     * 
     * @param  string $hash
     * @return BlockCipher 
     */
    public function setHashAlgorithm($hash)
    {
        if (!Hash::isSupported($hash)) {
            throw new Exception\InvalidArgumentException(
                "The specified hash algorithm $hash is not supported by Zend\Crypt\Hash"
            );
        }
        $this->hash = $hash;     
        return $this;
    }
    /**
     * Get the hash algorithm for HMAC authentication
     * 
     * @return string 
     */
    public function getHashAlgorithm()
    {
        return $this->hash;
    }
    /**
     * Encrypt then authenticate using HMAC
     *
     * @param  string $data
     * @return string 
     */
    public function encrypt($data)
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }
        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for the encryption');
        }
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }
        $keySize = $this->cipher->getKeySize();
        // generate a random salt (IV)
        $this->cipher->setSalt(Math::randBytes($this->cipher->getSaltSize(), true));
        // generate the encryption key and the HMAC key for the authentication
        $hash = Pbkdf2::calc(self::KEY_DERIV_HMAC, 
                             $this->getKey(),
                             $this->cipher->getSalt(),
                             $this->keyIteration, 
                             $keySize * 2);
        // set the encryption key
        $this->cipher->setKey(substr($hash, 0, $keySize));
        // set the key for HMAC
        $keyHmac = substr($hash, $keySize);
        // encryption
        $ciphertext = $this->cipher->encrypt($data);
        // HMAC 
        $hmac = Hmac::compute($keyHmac,
                              $this->hash,
                              $this->cipher->getAlgorithm() . $ciphertext);
        if (!$this->binaryOutput) {
            $ciphertext = base64_encode($ciphertext);
        }
        return $hmac . $ciphertext;
    }
    /**
     * Decrypt
     * 
     * @param  string $data
     * @return string|boolean
     */
    public function decrypt($data) 
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        }
        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for the decryption');
        }
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }
        $hmacSize   = Hmac::getOutputSize($this->hash);
        $hmac       = substr($data, 0, $hmacSize);
        $ciphertext = substr($data, $hmacSize);
        if (!$this->binaryOutput) {
            $ciphertext = base64_decode($ciphertext);
        } 
        $iv      = substr($ciphertext, 0, $this->cipher->getSaltSize());
        $keySize = $this->cipher->getKeySize();
        // generate the encryption key and the HMAC key for the authentication
        $hash = Pbkdf2::calc(self::KEY_DERIV_HMAC, 
                             $this->getKey(),
                             $iv,
                             $this->keyIteration, 
                             $keySize * 2);
        // set the decryption key
        $this->cipher->setKey(substr($hash, 0, $keySize));
        // set the key for HMAC
        $keyHmac = substr($hash, $keySize);
	$hmacNew = Hmac::compute($keyHmac, 
                                 $this->hash, 
                                 $this->cipher->getAlgorithm() . $ciphertext);
        if (!Tool::compareString($hmacNew, $hmac)) {
            return false;
        }
        return $this->cipher->decrypt($ciphertext);
    }
}