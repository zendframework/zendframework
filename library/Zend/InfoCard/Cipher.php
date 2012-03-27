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
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Cipher
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\InfoCard;

/**
 * Provides an abstraction for encryption ciphers used in an Information Card
 * implementation
 *
 * @uses       \Zend\InfoCard\Cipher\Exception
 * @uses       \Zend\InfoCard\Cipher\PKI\Adapter\RSA
 * @uses       \Zend\InfoCard\Cipher\Symmetric\Adapter\AES128CBC
 * @uses       \Zend\InfoCard\Cipher\Symmetric\Adapter\AES256CBC
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Cipher
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * @return void
     * @codeCoverageIgnoreStart
     */
    protected function __construct()
    {
    }
    // @codeCoverageIgnoreEnd
    /**
     * Returns an instance of a cipher object supported based on the URI provided
     *
     * @throws \Zend\InfoCard\Cipher\Exception
     * @param string $uri The URI of the encryption method wantde
     * @return mixed an Instance of Zend\InfoCard\Cipher\Symmetric or Zend\InfoCard\Cipher\PKI
     *               depending on URI
     */
    static public function getInstanceByURI($uri)
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
