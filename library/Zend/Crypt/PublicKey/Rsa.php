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

use Zend\Crypt\Exception;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Rsa
{
    const BINARY = 'binary';
    const BASE64 = 'base64';
    /**
     * @var string|Rsa\PrivateKey
     */
    protected $_privateKey;
    /**
     * @var Rsa\PublicKey
     */
    protected $_publicKey;
    /**
     * @var string
     */
    protected $_pemString;
    /**
     * @var string
     */
    protected $_pemPath;
    /**
     * @var string
     */
    protected $_certificateString;
    /**
     * @var string
     */
    protected $_certificatePath;
    /**
     * @var string
     */
    protected $_hashAlgorithm;
    /**
     * @var string
     */
    protected $_passPhrase;

    /**
     * Class constructor
     *
     * @param array $options
     * @throws Rsa\Exception\RuntimeException
     */
    public function __construct(array $options = null)
    {
        if (!extension_loaded('openssl')) {
            throw new Rsa\Exception\RuntimeException('Zend\Crypt\PublicKey\Rsa requires openssl extension to be loaded.');
        }

        // Set _hashAlgorithm property when we are sure, that openssl extension is loaded
        // and OPENSSL_ALGO_SHA1 constant is available
        $this->_hashAlgorithm = OPENSSL_ALGO_SHA1;

        if (isset($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (isset($options['passPhrase'])) {
            $this->_passPhrase = $options['passPhrase'];
        }
        foreach ($options as $option => $value) {
            switch ($option) {
                case 'pemString':
                    $this->setPemString($value);
                    break;
                case 'pemPath':
                    $this->setPemPath($value);
                    break;
                case 'certificateString':
                    $this->setCertificateString($value);
                    break;
                case 'certificatePath':
                    $this->setCertificatePath($value);
                    break;
                case 'hashAlgorithm':
                    $this->setHashAlgorithm($value);
                    break;
            }
        }
    }

    /**
     * Get the private key
     *
     * @return Rsa\PrivateKey
     */
    public function getPrivateKey()
    {
        return $this->_privateKey;
    }

    /**
     * Get the public key
     *
     * @return Rsa\PublicKey
     */
    public function getPublicKey()
    {
        return $this->_publicKey;
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
        if (isset($privateKey)) {
            $opensslKeyResource = $privateKey->getOpensslKeyResource();
        } else {
            $opensslKeyResource = $this->_privateKey->getOpensslKeyResource();
        }
        $result = openssl_sign(
            $data, $signature,
            $opensslKeyResource,
            $this->getHashAlgorithm()
        );
        if ($format == self::BASE64) {
            return base64_encode($signature);
        }
        return $signature;
    }

    /**
     * Verify signature
     *
     * @param  string $data
     * @param  string $signature
     * @param  string $format
     * @return string
     */
    public function verifySignature($data, $signature, $format = null)
    {
        if ($format == self::BASE64) {
            $signature = base64_decode($signature);
        }
        $result = openssl_verify($data, $signature,
                                 $this->getPublicKey()->getOpensslKeyResource(),
                                 $this->getHashAlgorithm());
        return $result;
    }

    /**
     * Encrypt
     *
     * @param string  $data
     * @param Rsa\Key $key
     * @param string  $format
     * @return string
     */
    public function encrypt($data, Rsa\Key $key, $format = null)
    {
        $encrypted = '';
        $function  = 'openssl_public_encrypt';
        if ($key instanceof Rsa\PrivateKey) {
            $function = 'openssl_private_encrypt';
        }
        $function($data, $encrypted, $key->getOpensslKeyResource());
        if ($format == self::BASE64) {
            return base64_encode($encrypted);
        }
        return $encrypted;
    }

    /**
     * Decrypt
     *
     * @param string  $data
     * @param Rsa\Key $key
     * @param string  $format
     * @return string
     */
    public function decrypt($data, Rsa\Key $key, $format = null)
    {
        $decrypted = '';
        if ($format == self::BASE64) {
            $data = base64_decode($data);
        }
        $function = 'openssl_private_decrypt';
        if ($key instanceof Rsa\PublicKey) {
            $function = 'openssl_public_decrypt';
        }
        $function($data, $decrypted, $key->getOpensslKeyResource());
        return $decrypted;
    }

    /**
     * Generate keys
     *
     * @param  array $configargs
     * @return \ArrayObject
     */
    public function generateKeys(array $configargs = null)
    {
        $config     = array();
        $passPhrase = $this->_passPhrase;
        $private    = $this->getPrivateKey();
        if ($configargs !== null) {
            if (isset($configargs['passPhrase'])) {
                $passPhrase = $configargs['passPhrase'];
                unset($configargs['passPhrase']);
            }
            $config = $this->_parseConfigArgs($configargs);
        }

        $privateKey = null;
        $publicKey  = null;
        $resource   = openssl_pkey_new($config);
        // above fails on PHP 5.3

        openssl_pkey_export($resource, $private, $passPhrase);

        $privateKey = new Rsa\PrivateKey($private, $passPhrase);
        $details    = openssl_pkey_get_details($resource);
        $publicKey  = new Rsa\PublicKey($details['key']);
        $return     = new \ArrayObject(array(
                                            'privateKey' => $privateKey,
                                            'publicKey'  => $publicKey
                                       ), \ArrayObject::ARRAY_AS_PROPS);
        return $return;
    }

    /**
     * Set PEM string
     *
     * @param string $value
     */
    public function setPemString($value)
    {
        $this->_pemString = $value;
        try {
            $this->_privateKey = new Rsa\PrivateKey($this->_pemString, $this->_passPhrase);
            $this->_publicKey  = $this->_privateKey->getPublicKey();
        } catch (Rsa\Exception\RuntimeException $e) {
            $this->_privateKey = null;
            $this->_publicKey  = new Rsa\PublicKey($this->_pemString);
        }
    }

    /**
     * Set PEM path
     *
     * @param string $value
     */
    public function setPemPath($value)
    {
        $this->_pemPath = $value;
        $this->setPemString(file_get_contents($this->_pemPath));
    }

    /**
     * Set certificate string
     *
     * @param string $value
     */
    public function setCertificateString($value)
    {
        $this->_certificateString = $value;
        $this->_publicKey         = new Rsa\PublicKey($this->_certificateString, $this->_passPhrase);
    }

    /**
     * Set certificate path
     *
     * @param string $value
     */
    public function setCertificatePath($value)
    {
        $this->_certificatePath = $value;
        $this->setCertificateString(file_get_contents($this->_certificatePath));
    }

    /**
     * Set hash algorithm
     *
     * @param  string $name
     * @throws Exception\RuntimeException
     */
    public function setHashAlgorithm($name)
    {
        switch (strtolower($name)) {
            case 'md2':
                // check if md2 digest is enabled on openssl just for backwards compatibility
                $digests = openssl_get_md_methods();
                if (!in_array(strtoupper($name), $digests)) {
                    throw new Exception\RuntimeException('Openssl md2 digest is not enabled  (deprecated)');
                }
                $this->_hashAlgorithm = OPENSSL_ALGO_MD2;
                break;
            case 'md4':
                $this->_hashAlgorithm = OPENSSL_ALGO_MD4;
                break;
            case 'md5':
                $this->_hashAlgorithm = OPENSSL_ALGO_MD5;
                break;
            case 'sha1':
                $this->_hashAlgorithm = OPENSSL_ALGO_SHA1;
                break;
            case 'dss1':
                $this->_hashAlgorithm = OPENSSL_ALGO_DSS1;
                break;
        }
    }

    /**
     * Get PEM string
     *
     * @return string
     */
    public function getPemString()
    {
        return $this->_pemString;
    }

    /**
     * Get PEM path
     *
     * @return string
     */
    public function getPemPath()
    {
        return $this->_pemPath;
    }

    /**
     * Get certificate string
     *
     * @return string
     */
    public function getCertificateString()
    {
        return $this->_certificateString;
    }

    /**
     * Get certificate path
     *
     * @return string
     */
    public function getCertificatePath()
    {
        return $this->_certificatePath;
    }

    /**
     * Get hash algorithm
     *
     * @return string
     */
    public function getHashAlgorithm()
    {
        return $this->_hashAlgorithm;
    }

    /**
     * Parse config arguments
     *
     * @param  array $config
     * @return array
     */
    protected function _parseConfigArgs(array $config = null)
    {
        $configs = array();
        if (isset($config['privateKeyBits'])) {
            $configs['private_key_bits'] = $config['privateKeyBits'];
        }
        if (!empty($configs)) {
            return $configs;
        }
        return null;
    }
}
