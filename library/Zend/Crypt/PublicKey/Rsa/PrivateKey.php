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
class PrivateKey extends Key
{
    /**
     * Public key
     *
     * @var string
     */
    protected $_publicKey = null;

    /**
     * Constructor
     *
     * @param string $pemString
     * @param string $passPhrase
     */
    public function __construct($pemString, $passPhrase = null)
    {
        $this->_pemString = $pemString;
        $this->_parse($passPhrase);
    }

    /**
     * @param string $passPhrase
     * @throws Exception\RuntimeException
     */
    protected function _parse($passPhrase)
    {
        $result = openssl_pkey_get_private($this->_pemString, $passPhrase);
        if (!$result) {
            throw new Exception\RuntimeException('Unable to load private key');
        }
        $this->_opensslKeyResource = $result;
        $this->_details            = openssl_pkey_get_details($this->_opensslKeyResource);
    }

    /**
     * Get the public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        if ($this->_publicKey === null) {
            $this->_publicKey = new PublicKey($this->_details['key']);
        }
        return $this->_publicKey;
    }
}
