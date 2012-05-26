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
class PublicKey extends AbstractKey
{
    const CERT_START = '-----BEGIN CERTIFICATE-----';

    /**
     * @var string
     */
    protected $certificateString = null;

    /**
     * @param string $string
     * @throws Exception\RuntimeException
     */
    public function __construct($string)
    {
        $result = openssl_pkey_get_public($string);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Unable to load public key; openssl ' . openssl_error_string()
            );
        }

        if (strpos($string, self::CERT_START) !== false) {
            $this->certificateString = $string;
        } else {
            $this->pemString = $string;
        }

        $this->opensslKeyResource = $result;
        $this->details            = openssl_pkey_get_details($this->opensslKeyResource);
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
        $result = openssl_public_encrypt($data, $encrypted, $this->getOpensslKeyResource());
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
        $result = openssl_public_decrypt($data, $decrypted, $this->getOpensslKeyResource());
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not decrypt; openssl ' . openssl_error_string()
            );
        }

        return $decrypted;
    }

    /**
     * Get certificate string
     *
     * @return string
     */
    public function getCertificate()
    {
        return $this->certificateString;
    }

    /**
     * To string
     *
     * @return string
     * @throws Exception\RuntimeException
     */
    public function toString()
    {
        if (!empty($this->certificateString)) {
            return $this->certificateString;
        } elseif (!empty($this->pemString)) {
            return $this->pemString;
        }
        throw new Exception\RuntimeException('No public key string representation is available');
    }
}
