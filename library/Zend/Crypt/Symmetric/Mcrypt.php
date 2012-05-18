<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\Symmetric;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Symmetric encryption using the Mcrypt extension
 *
 * NOTE: DO NOT USE only this class to encrypt data.
 * This class doesn't provide authentication and integrity check over the data.
 * PLEASE USE Zend\Crypt\BlockCipher instead!
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Mcrypt implements SymmetricInterface
{
    const DEFAULT_PADDING = 'pkcs7';
    /**
     * Key
     *
     * @var string
     */
    protected $key;
    /**
     * IV
     *
     * @var string
     */
    protected $iv;
    /**
     * Encryption algorithm
     *
     * @var string
     */
    protected $algo = 'aes';
    /**
     * Encryption mode
     *
     * @var string
     */
    protected $mode = 'cbc';
    /**
     * Padding
     *
     * @var Padding\PaddingInterface
     */
    protected $padding;
    /**
     * Padding broker
     *
     * @var PaddingBroker
     */
    protected static $paddingBroker = null;
    /**
     * Supported cipher algorithms
     *
     * @var array
     */
    protected $supportedAlgos = array(
        'aes'          => MCRYPT_RIJNDAEL_128,
        'blowfish'     => MCRYPT_BLOWFISH,
        'des'          => MCRYPT_DES,
        '3des'         => MCRYPT_TRIPLEDES,
        'tripledes'    => MCRYPT_TRIPLEDES,
        'cast-128'     => MCRYPT_CAST_128,
        'cast-256'     => MCRYPT_CAST_256,
        'rijndael-128' => MCRYPT_RIJNDAEL_128,
        'rijndael-192' => MCRYPT_RIJNDAEL_192,
        'rijndael-256' => MCRYPT_RIJNDAEL_256,
        'saferplus'    => MCRYPT_SAFERPLUS,
        'serpent'      => MCRYPT_SERPENT,
        'twofish'      => MCRYPT_TWOFISH
    );
    /**
     * Supported encryption modes
     *
     * @var array
     */
    protected $supportedModes = array(
        'cbc'  => MCRYPT_MODE_CBC,
        'cfb'  => MCRYPT_MODE_CFB,
        'ofb'  => MCRYPT_MODE_OFB,
        'nofb' => MCRYPT_MODE_NOFB
    );

    /**
     * Constructor
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('mcrypt')) {
            throw new Exception\RuntimeException(
                'You cannot use ' . __CLASS__ . ' without the Mcrypt extension'
            );
        }
        if (!empty($options)) {
            if ($options instanceof Traversable) {
                $options = ArrayUtils::iteratorToArray($options);
            } elseif (!is_array($options)) {
                throw new Exception\InvalidArgumentException(
                    'The options parameter must be an array, a Zend\Config\Config object or a Traversable'
                );
            }
            foreach ($options as $key => $value) {
                switch (strtolower($key)) {
                    case 'algo':
                    case 'algorithm':
                        $this->setAlgorithm($value);
                        break;
                    case 'mode':
                        $this->setMode($value);
                        break;
                    case 'key':
                        $this->setKey($value);
                        break;
                    case 'iv':
                    case 'salt':
                        $this->setSalt($value);
                        break;
                    case 'padding':
                        $broker        = self::getPaddingBroker();
                        $padding       = $broker->load($value, array());
                        $this->padding = $padding;
                        break;
                }
            }
        }
        $this->setDefaultOptions($options);
    }

    /**
     * Set default options
     *
     * @param  array $options
     * @return void
     */
    protected function setDefaultOptions($options = array())
    {
        if (empty($options)) {
            return;
        }
        if (!isset($options['padding'])) {
            $broker        = self::getPaddingBroker();
            $padding       = $broker->load(self::DEFAULT_PADDING, array());
            $this->padding = $padding;
        }
    }

    /**
     * Returns the padding broker.  If it doesn't exist it's created.
     *
     * @return PaddingBroker
     */
    public static function getPaddingBroker()
    {
        if (self::$paddingBroker === null) {
            self::setPaddingBroker(new PaddingBroker());
        }

        return self::$paddingBroker;
    }

    /**
     * Set the symmetric cipher broker
     *
     * @param  string|PaddingBroker $broker
     * @return void
     */
    public static function setPaddingBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception\InvalidArgumentException(sprintf(
                                                                 'Unable to locate padding broker of class "%s"',
                                                                 $broker
                                                             ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof PaddingBroker) {
            throw new Exception\InvalidArgumentException(sprintf(
                                                             'Padding broker must extend PaddingBroker; received "%s"',
                                                             (is_object($broker) ? get_class($broker) : gettype($broker))
                                                         ));
        }
        self::$paddingBroker = $broker;
    }

    /**
     * Get the maximum key size for the selected cipher and mode of operation
     *
     * @return integer
     */
    public function getKeySize()
    {
        return mcrypt_get_key_size($this->supportedAlgos[$this->algo],
                                   $this->supportedModes[$this->mode]);
    }

    /**
     * Set the encryption key
     *
     * @param  string $key
     * @return Mcrypt
     */
    public function setKey($key)
    {
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('The key cannot be empty');
        }
        if (strlen($key) < $this->getKeySize()) {
            throw new Exception\InvalidArgumentException('The key is not long enough for the cipher');
        }
        $this->key = $key;
        return $this;
    }

    /**
     * Get the encryption key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the encryption algorithm (cipher)
     *
     * @param  string $algo
     * @return Mcrypt
     */
    public function setAlgorithm($algo)
    {
        if (!array_key_exists($algo, $this->supportedAlgos)) {
            throw new Exception\InvalidArgumentException(
                "The algorithm $algo is not supported by " . __CLASS__
            );
        }
        $this->algo = $algo;
        return $this;
    }

    /**
     * Get the encryption algorithm
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algo;
    }

    /**
     * Set the padding object
     *
     * @param  Padding\PaddingInterface $padding
     * @return Mcrypt
     */
    public function setPadding(Padding\PaddingInterface $padding)
    {
        $this->padding = $padding;
        return $this;
    }

    /**
     * Get the padding object
     *
     * @return Padding\PaddingInterface
     */
    public function getPadding()
    {
        return $this->padding;
    }

    /**
     * Encrypt
     *
     * @param  string $data
     * @return string
     */
    public function encrypt($data)
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }
        if (null === $this->getKey()) {
            throw new Exception\InvalidArgumentException('No key specified for the encryption');
        }
        if (null === $this->getSalt()) {
            throw new Exception\InvalidArgumentException('The salt (IV) cannot be empty');
        }
        if (null === $this->getPadding()) {
            throw new Exception\InvalidArgumentException('You have to specify a padding method');
        }
        // padding
        $data = $this->padding->pad($data, $this->getBlockSize());
        // get the correct iv size
        $iv = substr($this->iv, 0, $this->getSaltSize());
        // encryption
        $result = mcrypt_encrypt(
            $this->supportedAlgos[$this->algo],
            substr($this->key, 0, $this->getKeySize()),
            $data,
            $this->supportedModes[$this->mode],
            $iv
        );
        return $iv . $result;
    }

    /**
     * Decrypt
     *
     * @param  string $data
     * @return string
     */
    public function decrypt($data)
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        }
        if (null === $this->getKey()) {
            throw new Exception\InvalidArgumentException('No key specified for the decryption');
        }
        if (null === $this->getPadding()) {
            throw new Exception\InvalidArgumentException('You have to specify a padding method');
        }
        $iv         = substr($data, 0, $this->getSaltSize());
        $ciphertext = substr($data, $this->getSaltSize());
        $result     = mcrypt_decrypt(
            $this->supportedAlgos[$this->algo],
            substr($this->key, 0, $this->getKeySize()),
            $ciphertext,
            $this->supportedModes[$this->mode],
            $iv
        );
        // unpadding
        return $this->padding->strip($result);
    }

    /**
     * Get the salt (IV) size
     *
     * @return integer
     */
    public function getSaltSize()
    {
        return mcrypt_get_iv_size($this->supportedAlgos[$this->algo],
                                  $this->supportedModes[$this->mode]);
    }

    /**
     * Get the supported algorithms
     *
     * @return array
     */
    public function getSupportedAlgorithms()
    {
        return array_keys($this->supportedAlgos);
    }

    /**
     * Set the salt (IV)
     *
     * @param  string $salt
     * @return Mcrypt
     */
    public function setSalt($salt)
    {
        if (!empty($salt)) {
            $ivSize = $this->getSaltSize();
            if (strlen($salt) < $ivSize) {
                throw new Exception\InvalidArgumentException(
                    "The size of the salt (IV) is not enough. You need $ivSize bytes"
                );
            }
            $this->iv = $salt;
        }
        return $this;
    }

    /**
     * Get the salt (IV)
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->iv;
    }

    /**
     * Set the cipher mode
     *
     * @param  string $mode
     * @return Mcrypt
     */
    public function setMode($mode)
    {
        if (!empty($mode)) {
            $mode = strtolower($mode);
            if (!array_key_exists($mode, $this->supportedModes)) {
                throw new Exception\InvalidArgumentException(
                    "The mode $mode is not supported by " . __CLASS__
                );
            }
            $this->mode = $mode;
        }
        return $this;
    }

    /**
     * Get the cipher mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Get all supported encryption modes
     *
     * @return array
     */
    public function getSupportedModes()
    {
        return array_keys($this->supportedModes);
    }

    /**
     * Get the block size
     *
     * @return integer
     */
    public function getBlockSize()
    {
        return mcrypt_get_block_size($this->supportedAlgos[$this->algo],
                                     $this->supportedModes[$this->mode]);
    }
}
