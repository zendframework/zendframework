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
namespace Zend\InfoCard\Cipher\PKI\Adapter;
use Zend\InfoCard\Cipher;

/**
 * RSA Public Key Encryption Cipher Object for the InfoCard component. Relies on OpenSSL
 * to implement the RSA algorithm
 *
 * @uses       \Zend\InfoCard\Cipher\Exception
 * @uses       \Zend\InfoCard\Cipher\PKI\Adapter\AbstractAdapter
 * @uses       \Zend\InfoCard\Cipher\PKI\RSA
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Cipher
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RSA extends AbstractAdapter implements Cipher\PKI\RSA
{

    /**
     * Object Constructor
     *
     * @param integer $padding The type of Padding to use
     */
    public function __construct($padding = AbstractAdapter::NO_PADDING)
    {
        // Can't test this..
        // @codeCoverageIgnoreStart
        if(!extension_loaded('openssl')) {
            throw new Cipher\Exception\ExtensionNotLoadedException("Use of this PKI RSA Adapter requires the openssl extension loaded");
        }
        // @codeCoverageIgnoreEnd

        $this->setPadding($padding);
    }

    /**
     * Decrypts RSA encrypted data using the given private key
     *
     * @throws \Zend\InfoCard\Cipher\Exception
     * @param string $encryptedData The encrypted data in binary format
     * @param string $privateKey The private key in binary format
     * @param string $password The private key passphrase
     * @param integer $padding The padding to use during decryption (of not provided object value will be used)
     * @return string The decrypted data
     */
    public function decrypt($encryptedData, $privateKey, $password = null, $padding = null)
    {
        $private_key = openssl_pkey_get_private(array($privateKey, $password));

        if(!$private_key) {
            throw new Cipher\Exception\RuntimeException("Failed to load private key");
        }

        if($padding !== null) {
            try {
                $this->setPadding($padding);
            } catch(\Exception $e) {
                openssl_free_key($private_key);
                throw $e;
            }
        }

        switch($this->getPadding()) {
            case self::NO_PADDING:
                $openssl_padding = OPENSSL_NO_PADDING;
                break;
            case self::OAEP_PADDING:
                $openssl_padding = OPENSSL_PKCS1_OAEP_PADDING;
                break;
        }

        $result = openssl_private_decrypt($encryptedData, $decryptedData, $private_key, $openssl_padding);

        openssl_free_key($private_key);

        if(!$result) {
            throw new Cipher\Exception\RuntimeException("Unable to Decrypt Value using provided private key");
        }

        if($this->getPadding() == self::NO_PADDING) {
            $decryptedData = substr($decryptedData, 2);
            $start = strpos($decryptedData, 0) + 1;
            $decryptedData = substr($decryptedData, $start);
        }

        return $decryptedData;
    }
}
