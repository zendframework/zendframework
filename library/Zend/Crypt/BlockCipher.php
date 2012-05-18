<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */
namespace Zend\Crypt;

use Zend\Crypt\Symmetric\SymmetricInterface;
use Zend\Crypt\Hmac;
use Zend\Crypt\Tool;
use Zend\Crypt\Key\Derivation\Pbkdf2;
use Zend\Math\Math;

/**
 * Encrypt using a symmetric cipher then authenticate using HMAC (SHA-256)
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BlockCipher
{
    const KEY_DERIV_HMAC = 'sha256';
    /**
     * Symmetric cipher
     *
     * @var SymmetricInterface
     */
    protected $cipher;
    /**
     * Symmetric cipher broker
     *
     * @var SymmetricBroker
     */
    protected static $symmetricBroker = null;
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
     * Number of iterations for Pbkdf2
     *
     * @var string
     */
    protected $keyIteration = 5000;

    /**
     * Constructor
     *
     * @param SymmetricInterface $cipher
     */
    public function __construct(SymmetricInterface $cipher)
    {
        $this->cipher = $cipher;
    }

    /**
     * Factory.
     *
     * @param  string $adapter
     * @param  array  $options
     * @return BlockCipher
     */
    public static function factory($adapter, $options = array())
    {
        $broker  = self::getSymmetricBroker();
        $adapter = $broker->load($adapter, array($options));
        return new self($adapter);
    }

    /**
     * Returns the symmetric cipher broker.  If it doesn't exist it's created.
     *
     * @return SymmetricBroker
     */
    public static function getSymmetricBroker()
    {
        if (self::$symmetricBroker === null) {
            self::setSymmetricBroker(new SymmetricBroker());
        }

        return self::$symmetricBroker;
    }

    /**
     * Set the symmetric cipher broker
     *
     * @param  string|SymmetricBroker $broker
     */
    public static function setSymmetricBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception\InvalidArgumentException(
                    sprintf(
                        'Unable to locate symmetric cipher broker of class "%s"',
                        $broker
                    ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof SymmetricBroker) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Symmetric cipher broker must extend SymmetricBroker; received "%s"',
                    (is_object($broker) ? get_class($broker) : gettype($broker))
                ));
        }
        self::$symmetricBroker = $broker;
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
     * Set the number of iterations for Pbkdf2
     *
     * @param  integer $num
     * @return BlockCipher
     */
    public function setKeyIteration($num)
    {
        $this->keyIteration = (integer)$num;
        return $this;
    }

    /**
     * Get the number of iterations for Pbkdf2
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
     * @return BlockCipher
     */
    public function setBinaryOutput($value)
    {
        $this->binary = (boolean)$value;
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
        } catch (Symmetric\Exception\InvalidArgumentException $e) {
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
