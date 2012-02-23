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

/**
 * @namespace
 */
namespace Zend\Crypt;

use Zend\Crypt\Exception;

/**
 * @uses       Zend\Crypt\Rsa\PrivateKey
 * @uses       Zend\Crypt\Rsa\PublicKey
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Rsa
{
    const BINARY = 'binary';
    const BASE64 = 'base64';

    protected $_privateKey;

    protected $_publicKey;

    /**
     * @var string
     */
    protected $_pemString;

    protected $_pemPath;

    protected $_certificateString;

    protected $_certificatePath;

    protected $_hashAlgorithm;

    protected $_passPhrase;

    /**
     * Class constructor
     *
     * @param array $options
     * @throws Zend\Crypt\Rsa\Exception
     */
    public function __construct(array $options = null)
    {
        if (!extension_loaded('openssl')) {
            throw new \Zend\Crypt\Rsa\Exception('Zend_Crypt_Rsa requires openssl extention to be loaded.');
        }

        // Set _hashAlgorithm property when we are sure, that openssl extension is loaded
        // and OPENSSL_ALGO_SHA1 constant is available
        $this->_hashAlgorithm = OPENSSL_ALGO_SHA1;

        if (isset($options)) {
            $this->setOptions($options);
        }
    }

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

    public function getPrivateKey()
    {
        return $this->_privateKey;
    }

    public function getPublicKey()
    {
        return $this->_publicKey;
    }

    /**
     * @param  string $data
     * @param  Zend\Crypt\Rsa\PrivateKey $privateKey
     * @param  string $format
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
     * @param string $data
     * @param Zend\Crypt\Rsa\Key $key
     * @param string $format
     * @return string
     */
    public function encrypt($data, Rsa\Key $key, $format = null)
    {
        $encrypted = '';
        $function = 'openssl_public_encrypt';
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
     * @param string $data
     * @param \Zend\Crypt\Rsa\Key $key
     * @param string $format
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
     * @param string $value
     */
    public function setPemString($value)
    {
        $this->_pemString = $value;
        try {
            $this->_privateKey = new Rsa\PrivateKey($this->_pemString, $this->_passPhrase);
            $this->_publicKey = $this->_privateKey->getPublicKey();
        } catch (Exception $e) {
            $this->_privateKey = null;
            $this->_publicKey = new Rsa\PublicKey($this->_pemString);
        }
    }

    public function setPemPath($value)
    {
        $this->_pemPath = $value;
        $this->setPemString(file_get_contents($this->_pemPath));
    }

    public function setCertificateString($value)
    {
        $this->_certificateString = $value;
        $this->_publicKey = new Rsa\PublicKey($this->_certificateString, $this->_passPhrase);
    }

    public function setCertificatePath($value)
    {
        $this->_certificatePath = $value;
        $this->setCertificateString(file_get_contents($this->_certificatePath));
    }

    /**
     * @param string $name
     * @throws \Zend\Crypt\Exception
     */
    public function setHashAlgorithm($name)
    {
        switch (strtolower($name)) {
            case 'md2':
                // check if md2 digest is enabled on openssl just for backwards compatibility
                $digests = openssl_get_md_methods();
                if (!in_array(strtoupper($name), $digests)) {
                    throw new Exception('Openssl md2 digest is not enabled  (deprecated)');
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
     * @return string
     */
    public function getPemString()
    {
        return $this->_pemString;
    }

    public function getPemPath()
    {
        return $this->_pemPath;
    }

    public function getCertificateString()
    {
        return $this->_certificateString;
    }

    public function getCertificatePath()
    {
        return $this->_certificatePath;
    }

    public function getHashAlgorithm()
    {
        return $this->_hashAlgorithm;
    }

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
