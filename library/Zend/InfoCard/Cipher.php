<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard;

/**
 * Provides an abstraction for encryption ciphers used in an Information Card
 * implementation
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Cipher
 */
class Cipher
{
    /**
     * AES 256 Encryption with CBC
     */
    const ENC_AES256CBC      = 'http://www.w3.org/2001/04/xmlenc#aes256-cbc';

    /**
     * AES 128 Encryption with CBC
     */
    const ENC_AES128CBC      = 'http://www.w3.org/2001/04/xmlenc#aes128-cbc';

    /**
     * RSA Public Key Encryption with OAEP Padding
     */
    const ENC_RSA_OAEP_MGF1P = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';

    /**
     * RSA Public Key Encryption with no padding
     */
    const ENC_RSA            = 'http://www.w3.org/2001/04/xmlenc#rsa-1_5';

    /**
     * Constructor (disabled)
     *
     * @codeCoverageIgnoreStart
     */
    protected function __construct()
    {
    }
    // @codeCoverageIgnoreEnd
    /**
     * Returns an instance of a cipher object supported based on the URI provided
     *
     * @throws Cipher\Exception\InvalidArgumentException
     * @param string $uri The URI of the encryption method wantde
     * @return mixed an Instance of Zend\InfoCard\Cipher\Symmetric or Zend\InfoCard\Cipher\PKI
     *               depending on URI
     */
    public static function getInstanceByURI($uri)
    {
        switch($uri) {
            case self::ENC_AES256CBC:
                return new Cipher\Symmetric\Adapter\AES256CBC();

            case self::ENC_AES128CBC:
                return new Cipher\Symmetric\Adapter\AES128CBC();

            case self::ENC_RSA_OAEP_MGF1P:
                return new Cipher\PKI\Adapter\RSA(Cipher\PKI\Adapter\RSA::OAEP_PADDING);
                break;

            case self::ENC_RSA:
                return new Cipher\PKI\Adapter\RSA(Cipher\PKI\Adapter\RSA::NO_PADDING);
                break;

            default:
                throw new Cipher\Exception\InvalidArgumentException("Unknown Cipher URI");
        }
    }
}
