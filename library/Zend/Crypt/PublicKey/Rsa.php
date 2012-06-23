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

use Zend\Crypt\PublicKey\RsaOptions;
use Zend\Crypt\PublicKey\Rsa\Exception;
use Traversable;
use ArrayObject;
use Zend\Stdlib\ArrayUtils;

/**
 * Implementation of the RSA public key encryption algorithm.
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Rsa
{
    const DEFAULT_KEY_SIZE = 2048;

    const FORMAT_BINARY = 'binary';
    const FORMAT_BASE64 = 'base64';

    /**
     * @var RsaOptions
     */
    protected $options = null;

    /**
     * Class constructor
     *
     * @param RsaOptions $options
     * @throws Rsa\Exception\RuntimeException
     */
    public function __construct(RsaOptions $options = null)
    {
        if (!extension_loaded('openssl')) {
            throw new Exception\RuntimeException(
                'Zend\Crypt\PublicKey\Rsa requires openssl extension to be loaded.'
            );
        }

        if (null !== $options) {
            $this->setOptions($options);
        } else {
            $this->options = new RsaOptions();
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
     * @return RsaOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sign
     *
     * @param  string         $data
     * @param  Rsa\PrivateKey $privateKey
     * @param  string         $format
     * @return string
     * @throws Rsa\Exception\RuntimeException
     */
    public function sign($data, Rsa\PrivateKey $privateKey = null, $format = null)
    {
        $signature = '';
        if (null === $privateKey) {
            $privateKey = $this->options->getPrivateKey();
        }

        $result = openssl_sign(
            $data,
            $signature,
            $privateKey->getOpensslKeyResource(),
            $this->options->getHashAlgorithm()
        );
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not generate signature; openssl ' . openssl_error_string()
            );
        }

        if ($format == self::FORMAT_BASE64) {
            return base64_encode($signature);
        } else {
            return $signature;
        }
    }

    /**
     * Verify signature
     *
     * @param string $data
     * @param string $signature
     * @param null|Rsa\PublicKey $publicKey
     * @param null|string $format
     * @return bool
     * @throws Rsa\Exception\RuntimeException
     */
    public function verify($data, $signature, Rsa\PublicKey $publicKey = null,  $format = null)
    {
        if ($format == self::FORMAT_BASE64) {
            $signature = base64_decode($signature);
        }
        if (null === $publicKey) {
            $publicKey = $this->options->getPublicKey();
        }

        $result = openssl_verify(
            $data,
            $signature,
            $publicKey->getOpensslKeyResource(),
            $this->options->getHashAlgorithm()
        );
        if (-1 === $result) {
            throw new Exception\RuntimeException(
                'Can not verify signature; openssl ' . openssl_error_string()
            );
        }

        return ($result === 1);
    }

    /**
     * Encrypt
     *
     * @param string          $data
     * @param Rsa\AbstractKey $key
     * @param string          $format
     * @return string
     * @throws Rsa\Exception\RuntimeException
     */
    public function encrypt($data, Rsa\AbstractKey $key = null, $format = null)
    {
        if (null === $key) {
            $key = $this->options->getPublicKey();
        }

        $encrypted = $key->encrypt($data);

        if ($format == self::FORMAT_BASE64) {
            return base64_encode($encrypted);
        } else {
            return $encrypted;
        }
    }

    /**
     * Decrypt
     *
     * @param string          $data
     * @param Rsa\AbstractKey $key
     * @param string          $format
     * @return string
     * @throws Rsa\Exception\RuntimeException
     */
    public function decrypt($data, Rsa\AbstractKey $key = null, $format = null)
    {
        if ($format == self::FORMAT_BASE64) {
            $data = base64_decode($data);
        }

        if (null === $key) {
            $key = $this->options->getPrivateKey();
        }

        return $key->decrypt($data);
    }

    /**
     * Generate keys
     *
     * @param  array $options
     * @return ArrayObject
     * @throws Rsa\Exception\RuntimeException
     */
    public function generateKeys(array $options = null)
    {
        $config = array(
            'private_key_bits' => self::DEFAULT_KEY_SIZE,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        );

        if (isset($options['pass_phrase'])) {
            $passPhrase = $options['pass_phrase'];
        } else {
            $passPhrase = $this->options->getPassPhrase();
        }

        if (isset($options['private_key_bits'])) {
            $config['private_key_bits'] = $options['private_key_bits'];
        }

        // generate
        $privateKey = null;
        $publicKey  = null;
        $resource   = openssl_pkey_new($config);
        $result     = openssl_pkey_export($resource, $private, $passPhrase);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not export key; openssl ' . openssl_error_string()
            );
        }

        $privateKey = new Rsa\PrivateKey($private, $passPhrase);
        $details    = openssl_pkey_get_details($resource);
        $publicKey  = new Rsa\PublicKey($details['key']);

        return new ArrayObject(array(
            'privateKey' => $privateKey,
            'publicKey' => $publicKey
        ), ArrayObject::ARRAY_AS_PROPS);
    }
}
