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
     * @var string
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
        if (!$result) {
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
     * @return string
     */
    public function getPublicKey()
    {
        if ($this->publicKey === null) {
            $this->publicKey = new PublicKey($this->details['key']);
        }
        return $this->publicKey;
    }
}
