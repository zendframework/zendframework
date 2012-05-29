<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */
namespace Zend\Crypt\PublicKey\Rsa;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PrivateKey extends AbstractKey
{
    /**
     * Public key
     *
     * @var PublicKey
     */
    protected $publicKey = null;

    /**
     * Constructor
     *
     * @param string $pemString
     * @param string $passPhrase
     * @throws  Exception\RuntimeException
     */
    public function __construct($pemString, $passPhrase = null)
    {
        $result = openssl_pkey_get_private($pemString, $passPhrase);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Unable to load private key; openssl ' . openssl_error_string()
            );
        }

        $this->pemString          = $pemString;
        $this->opensslKeyResource = $result;
        $this->details            = openssl_pkey_get_details($this->opensslKeyResource);
    }

    /**
     * Get the public key
     *
     * @return PublicKey
     */
    public function getPublicKey()
    {
        if ($this->publicKey === null) {
            $this->publicKey = new PublicKey($this->details['key']);
        }

        return $this->publicKey;
    }

    /**
     * Encrypt using this key
     *
     * @param string $data
     * @return string
     * @throws Exception\RuntimeException
     */
    public function encrypt($data)
    {
        $encrypted = '';
        $result = openssl_private_encrypt($data, $encrypted, $this->getOpensslKeyResource());
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not encrypt; openssl ' . openssl_error_string()
            );
        }

        return $encrypted;
    }


    /**
     * Decrypt using this key
     *
     * @param string $data
     * @return string
     * @throws Exception\RuntimeException
     */
    public function decrypt($data)
    {
        $decrypted = '';
        $result = openssl_private_decrypt($data, $decrypted, $this->getOpensslKeyResource());
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not decrypt; openssl ' . openssl_error_string()
            );
        }

        return $decrypted;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->pemString;
    }
}
