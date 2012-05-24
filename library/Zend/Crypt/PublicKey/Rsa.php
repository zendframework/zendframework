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
use Zend\Crypt\Exception;
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
            throw new Rsa\Exception\RuntimeException(
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
     * Get the private key
     *
     * @return Rsa\PrivateKey
     */
    public function getPrivateKey()
    {
        return $this->options->getPrivateKey();
    }

    /**
     * Get the public key
     *
     * @return Rsa\PublicKey
     */
    public function getPublicKey()
    {
        return $this->options->getPublicKey();
    }

    /**
     * Sign
     *
     * @param  string         $data
     * @param  Rsa\PrivateKey $privateKey
     * @param  string         $format
     * @return string
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

        if ($format == self::FORMAT_BASE64) {
            return base64_encode($signature);
        } else {
            return $signature;
        }
    }

    /**
     * Verify signature
     *
     * @param  string $data
     * @param  string $signature
     * @param  string $format
     * @return boolean
     */
    public function verifySignature($data, $signature, $format = null)
    {
        if ($format == self::FORMAT_BASE64) {
            $signature = base64_decode($signature);
        }
        $result = openssl_verify(
            $data,
            $signature,
            $this->options->getPublicKey()->getOpensslKeyResource(),
            $this->options->getHashAlgorithm()
        );

        return ($result === 1);
    }

    /**
     * Encrypt
     *
     * @param string  $data
     * @param Rsa\AbstractKey $key
     * @param string  $format
     * @return string
     */
    public function encrypt($data, Rsa\AbstractKey $key, $format = null)
    {
        $encrypted = '';
        if ($key instanceof Rsa\PrivateKey) {
            openssl_private_encrypt($data, $encrypted, $key->getOpensslKeyResource());
        } else {
            openssl_public_encrypt($data, $encrypted, $key->getOpensslKeyResource());
        }

        if ($format == self::FORMAT_BASE64) {
            return base64_encode($encrypted);
        }

        return $encrypted;
    }

    /**
     * Decrypt
     *
     * @param string  $data
     * @param Rsa\AbstractKey $key
     * @param string  $format
     * @return string
     */
    public function decrypt($data, Rsa\AbstractKey $key, $format = null)
    {
        $decrypted = '';
        if ($format == self::FORMAT_BASE64) {
            $data = base64_decode($data);
        }

        if ($key instanceof Rsa\PublicKey) {
            openssl_public_decrypt($data, $decrypted, $key->getOpensslKeyResource());
        } else {
            openssl_private_decrypt($data, $decrypted, $key->getOpensslKeyResource());
        }

        return $decrypted;
    }

    /**
     * Generate keys
     *
     * @param  array $options
     * @return \ArrayObject
     */
    public function generateKeys(array $options = null)
    {
        if (null === $options) {
            $options = array();
        }

        if (isset($options['pass_phrase'])) {
            $passPhrase = $options['pass_phrase'];
        } else {
            $passPhrase = $this->options->getPassPhrase();
        }

        $options['private_key_type'] = OPENSSL_KEYTYPE_RSA;

        $privateKey = null;
        $publicKey  = null;
        $resource   = openssl_pkey_new($options);

        openssl_pkey_export($resource, $private, $passPhrase);

        $privateKey = new Rsa\PrivateKey($private, $passPhrase);
        $details    = openssl_pkey_get_details($resource);
        $publicKey  = new Rsa\PublicKey($details['key']);
        $return     = new ArrayObject(array(
            'privateKey' => $privateKey,
            'publicKey'  => $publicKey
        ), ArrayObject::ARRAY_AS_PROPS);

        return $return;
    }
}
