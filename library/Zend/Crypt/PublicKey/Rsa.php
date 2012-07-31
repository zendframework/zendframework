<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\PublicKey;

use Traversable;
use Zend\Crypt\PublicKey\RsaOptions;
use Zend\Crypt\PublicKey\Rsa\Exception;
use Zend\Stdlib\ArrayUtils;

/**
 * Implementation of the RSA public key encryption algorithm.
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage PublicKey
 */
class Rsa
{
    /**
     * @var RsaOptions
     */
    protected $options = null;

    /**
     * RSA instance factory
     *
     * @param  array|Traversable $options
     * @return Rsa
     * @throws Rsa\Exception\RuntimeException
     * @throws Rsa\Exception\InvalidArgumentException
     */
    public static function factory($options)
    {
        if (!extension_loaded('openssl')) {
            throw new Exception\RuntimeException(
                'Can not create Zend\Crypt\PublicKey\Rsa; openssl extension to be loaded'
            );
        }

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }

        $privateKey = null;
        $passPhrase = isset($options['pass_phrase']) ? $options['pass_phrase'] : null;
        if (isset($options['private_key'])) {
            if (is_file($options['private_key'])) {
                $privateKey = Rsa\PrivateKey::fromFile($options['private_key'], $passPhrase);
            } elseif (is_string($options['private_key'])) {
                $privateKey = new Rsa\PrivateKey($options['private_key'], $passPhrase);
            } else {
                throw new Exception\InvalidArgumentException(
                    'Parameter "private_key" must be PEM formatted string or path to key file'
                );
            }
            unset($options['private_key']);
        }

        $publicKey = null;
        if (isset($options['public_key'])) {
            if (is_file($options['public_key'])) {
                $publicKey = Rsa\PublicKey::fromFile($options['public_key']);
            } elseif (is_string($options['public_key'])) {
                $publicKey = new Rsa\PublicKey($options['public_key']);
            } else {
                throw new Exception\InvalidArgumentException(
                    'Parameter "public_key" must be PEM/certificate string or path to key/certificate file'
                );
            }
            unset($options['public_key']);
        }

        $options = new RsaOptions($options);
        if ($privateKey instanceof Rsa\PrivateKey) {
            $options->setPrivateKey($privateKey);
        }
        if ($publicKey instanceof Rsa\PublicKey) {
            $options->setPublicKey($publicKey);
        }

        return new Rsa($options);
    }

    /**
     * Class constructor
     *
     * @param  RsaOptions $options
     * @throws Rsa\Exception\RuntimeException
     */
    public function __construct(RsaOptions $options = null)
    {
        if (!extension_loaded('openssl')) {
            throw new Exception\RuntimeException(
                'Zend\Crypt\PublicKey\Rsa requires openssl extension to be loaded'
            );
        }

        if ($options === null) {
            $this->options = new RsaOptions();
        } else {
            $this->options = $options;
        }
    }

    /**
     * Set options
     *
     * @param RsaOptions $options
     * @return Rsa
     */
    public function setOptions(RsaOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get options
     *
     * @return RsaOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sign with private key
     *
     * @param  string     $data
     * @param  Rsa\PrivateKey $privateKey
     * @return string
     * @throws Rsa\Exception\RuntimeException
     */
    public function sign($data, Rsa\PrivateKey $privateKey = null)
    {
        $signature = '';
        if (null === $privateKey) {
            $privateKey = $this->options->getPrivateKey();
        }

        $result = openssl_sign(
            $data,
            $signature,
            $privateKey->getOpensslKeyResource(),
            $this->options->getOpensslSignatureAlgorithm()
        );
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not generate signature; openssl ' . openssl_error_string()
            );
        }

        if ($this->options->getBinaryOutput()) {
            return $signature;
        } else {
            return base64_encode($signature);
        }
    }

    /**
     * Verify signature with public key
     *
     * @param  string $data
     * @param  string $signature
     * @param  null|Rsa\PublicKey $publicKey
     * @return bool
     * @throws Rsa\Exception\RuntimeException
     */
    public function verify($data, $signature, Rsa\PublicKey $publicKey = null)
    {
        if (null === $publicKey) {
            $publicKey = $this->options->getPublicKey();
        }

        // check if signature is encoded in Base64
        $output = base64_decode($signature, true);
        if (false !== $output) {
            $signature = $output;
        }

        $result = openssl_verify(
            $data,
            $signature,
            $publicKey->getOpensslKeyResource(),
            $this->options->getOpensslSignatureAlgorithm()
        );
        if (-1 === $result) {
            throw new Exception\RuntimeException(
                'Can not verify signature; openssl ' . openssl_error_string()
            );
        }

        return ($result === 1);
    }

    /**
     * Encrypt with private/public key
     *
     * @param  string          $data
     * @param  Rsa\AbstractKey $key
     * @return string
     * @throws Rsa\Exception\InvalidArgumentException
     */
    public function encrypt($data, Rsa\AbstractKey $key = null)
    {
        if (null === $key) {
            $key = $this->options->getPublicKey();
        }

        if (null === $key) {
            throw new Exception\InvalidArgumentException('No key specified for the decryption');
        }

        $encrypted = $key->encrypt($data);

        if ($this->options->getBinaryOutput()) {
            return $encrypted;
        } else {
            return base64_encode($encrypted);
        }
    }

    /**
     * Decrypt with private/public key
     *
     * @param  string          $data
     * @param  Rsa\AbstractKey $key
     * @return string
     * @throws Rsa\Exception\InvalidArgumentException
     */
    public function decrypt($data, Rsa\AbstractKey $key = null)
    {
        if (null === $key) {
            $key = $this->options->getPrivateKey();
        }

        if (null === $key) {
            throw new Exception\InvalidArgumentException('No key specified for the decryption');
        }

        // check if data is encoded in Base64
        $output = base64_decode($data, true);
        if (false !== $output) {
            $data = $output;
        }

        return $key->decrypt($data);
    }

    /**
     * Generate new private/public key pair
     * @see RsaOptions::generateKeys()
     *
     * @param  array $opensslConfig
     * @return Rsa
     * @throws Rsa\Exception\RuntimeException
     */
    public function generateKeys(array $opensslConfig = array())
    {
        $this->options->generateKeys($opensslConfig);
        return $this;
    }
}
