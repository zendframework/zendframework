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

use Zend\Stdlib\Options;
use Zend\Crypt\PublicKey\Rsa\Exception;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RsaOptions extends Options
{
    /**
     * @var string|Rsa\PrivateKey
     */
    protected $privateKey = null;

    /**
     * @var Rsa\PublicKey
     */
    protected $publicKey = null;

    /**
     * @var string
     */
    protected $pemString;

    /**
     * @var string
     */
    protected $pemPath;

    /**
     * @var string
     */
    protected $certificateString;

    /**
     * @var string
     */
    protected $certificatePath;

    /**
     * @var string
     */
    protected $hashAlgorithm;

    /**
     * @var string
     */
    protected $passPhrase;

    /**
     * Get the private key
     *
     * @return Rsa\PrivateKey
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Get the public key
     *
     * @return Rsa\PublicKey
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function setPassPhrase($phrase)
    {
        $this->passPhrase = (string) $phrase;
        return $this;
    }

    public function getPassPhrase()
    {
        return $this->passPhrase;
    }

    /**
     * Set PEM string
     *
     * @param string $value
     */
    public function setPemString($value)
    {
        $this->pemString = $value;
        //try {
            $this->privateKey = new Rsa\PrivateKey($this->pemString, $this->passPhrase);
            $this->publicKey  = $this->privateKey->getPublicKey();
        //} catch (Rsa\Exception\RuntimeException $e) {
        //    echo "\t", $e->getMessage(), "\n";
        //    $this->privateKey = null;
        //    $this->publicKey  = new Rsa\PublicKey($this->pemString);
        //}
    }

    /**
     * Get PEM string
     *
     * @return string
     */
    public function getPemString()
    {
        return $this->pemString;
    }

    /**
     * Set PEM path
     *
     * @param string $value
     */
    public function setPemPath($value)
    {
        $this->pemPath = $value;
        $this->setPemString(file_get_contents($this->pemPath));
    }

    /**
     * Get PEM path
     *
     * @return string
     */
    public function getPemPath()
    {
        return $this->pemPath;
    }

    /**
     * Set hash algorithm
     *
     * @param $name
     * @throws Rsa\Exception\RuntimeException
     * @throws Rsa\Exception\InvalidArgumentException
     */
    public function setHashAlgorithm($name)
    {
        switch (strtolower($name)) {
            case 'md2':
                // check if md2 digest is enabled on openssl just for backwards compatibility
                $digests = openssl_get_md_methods();
                if (!in_array(strtoupper($name), $digests)) {
                    throw new Exception\RuntimeException(
                        'Openssl md2 digest is not enabled  (deprecated)'
                    );
                }
                $this->hashAlgorithm = OPENSSL_ALGO_MD2;
                break;
            case 'md4':
                $this->hashAlgorithm = OPENSSL_ALGO_MD4;
                break;
            case 'md5':
                $this->hashAlgorithm = OPENSSL_ALGO_MD5;
                break;
            case 'sha1':
                $this->hashAlgorithm = OPENSSL_ALGO_SHA1;
                break;
            case 'dss1':
                $this->hashAlgorithm = OPENSSL_ALGO_DSS1;
                break;
            default:
                throw new Exception\InvalidArgumentException(
                    "Hash algorithm '{$name}' is not supported"
                );
                break;
        }
    }

    /**
     * Get hash algorithm
     *
     * @return string
     */
    public function getHashAlgorithm()
    {
        if (!isset($this->hashAlgorithm)) {
            $this->hashAlgorithm = OPENSSL_ALGO_SHA1;
        }

        return $this->hashAlgorithm;
    }

    /**
     * Set certificate string
     *
     * @param string $value
     */
    public function setCertificateString($value)
    {
        $this->certificateString = $value;
        $this->publicKey         = new Rsa\PublicKey($this->certificateString, $this->passPhrase);
    }

    /**
     * Get certificate string
     *
     * @return string
     */
    public function getCertificateString()
    {
        return $this->certificateString;
    }

    /**
     * Set certificate path
     *
     * @param string $value
     */
    public function setCertificatePath($value)
    {
        $this->certificatePath = $value;
        $this->setCertificateString(file_get_contents($this->certificatePath));
    }


    /**
     * Get certificate path
     *
     * @return string
     */
    public function getCertificatePath()
    {
        return $this->certificatePath;
    }
}
