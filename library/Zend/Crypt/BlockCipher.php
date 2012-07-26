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

use Zend\Crypt\Hmac;
use Zend\Crypt\Key\Derivation\Pbkdf2;
use Zend\Crypt\Symmetric\SymmetricInterface;
use Zend\Crypt\Utils;
use Zend\Math\Rand;

/**
 * Encrypt using a symmetric cipher then authenticate using HMAC (SHA-256)
 *
 * @category   Zend
 * @package    Zend_Crypt
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
     * Symmetric cipher plugin manager
     *
     * @var SymmetricPluginManager
     */
    protected static $symmetricPlugins = null;

    /**
     * Hash algorithm fot HMAC
     *
     * @var string
     */
    protected $hash = 'sha256';

    /**
     * Salt (IV)
     *
     * @var string
     */
    protected $salt;

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
        $plugins = self::getSymmetricPluginManager();
        $adapter = $plugins->get($adapter, (array) $options);
        return new self($adapter);
    }

    /**
     * Returns the symmetric cipher plugin manager.  If it doesn't exist it's created.
     *
     * @return SymmetricPluginManager
     */
    public static function getSymmetricPluginManager()
    {
        if (self::$symmetricPlugins === null) {
            self::setSymmetricPluginManager(new SymmetricPluginManager());
        }

        return self::$symmetricPlugins;
    }

    /**
     * Set the symmetric cipher plugin manager
     *
     * @param  string|SymmetricPluginManager $plugins
     * @throws Exception\InvalidArgumentException
     */
    public static function setSymmetricPluginManager($plugins)
    {
        if (is_string($plugins)) {
            if (!class_exists($plugins)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Unable to locate symmetric cipher plugins using class "%s"; class does not exist',
                    $plugins
                ));
            }
            $plugins = new $plugins();
        }
        if (!$plugins instanceof SymmetricPluginManager) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an instance or extension of %s\SymmetricPluginManager; received "%s"',
                __NAMESPACE__,
                (is_object($plugins) ? get_class($plugins) : gettype($plugins))
            ));
        }
        self::$symmetricPlugins = $plugins;
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
     * Set the salt (IV)
     *
     * @param string $salt
     * @return BlockCipher
     * @throws Exception\InvalidArgumentException
     */
    public function setSalt($salt)
    {
        if (empty($salt)) {
            throw new Exception\InvalidArgumentException("The salt (IV) cannot be empty");
        }
        $this->salt = $salt;
        return $this;
    }

    /**
     * Get the salt (IV)
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Enable/disable the binary output
     *
     * @param boolean $value
     * @return BlockCipher
     */
    public function setBinaryOutput($value)
    {
        $this->binaryOutput = (boolean)$value;
        return $this;
    }

    /**
     * Get the value of binary output
     *
     * @return boolean
     */
    public function getBinaryOutput()
    {
        return $this->binaryOutput;
    }

    /**
     * Set the encryption/decryption key
     *
     * @param  string $key
     * @return BlockCipher
     * @throws Exception\InvalidArgumentException
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
     * @throws Exception\InvalidArgumentException
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
     * @return string|boolean
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
     * @throws Exception\InvalidArgumentException
     */
    public function setHashAlgorithm($hash)
    {
        if (!Hash::isSupported($hash)) {
            throw new Exception\InvalidArgumentException(
                "The specified hash algorithm '{$hash}' is not supported by Zend\Crypt\Hash"
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
     * @throws Exception\InvalidArgumentException
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
        $salt = $this->getSalt();
        // generate a random salt (IV) if empty
        if (empty($salt)) {
            $salt = Rand::getBytes($this->cipher->getSaltSize(), true);
        }
        $this->cipher->setSalt($salt);
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
     * @throws Exception\InvalidArgumentException
     */
    public function decrypt($data)
    {
        if (!is_string($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt must be a string');
        }
        if ('' === $data) {
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
        if (!Utils::compareStrings($hmacNew, $hmac)) {
            return false;
        }
        return $this->cipher->decrypt($ciphertext);
    }
}
