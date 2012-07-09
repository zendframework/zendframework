<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\Cipher\PKI;

use Zend\InfoCard\Cipher\PKI\Adapter;

/**
 * The interface which defines the RSA Public-key encryption object
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Cipher
 */
interface RSAInterface
{
    /**
     * Decrypts RSA encrypted data using the given private key
     *
     * @throws \Zend\InfoCard\Cipher\Exception\ExceptionInterface
     * @param string $encryptedData The encrypted data in binary format
     * @param string $privateKey The private key in binary format
     * @param string $password The private key passphrase
     * @param integer $padding The padding to use during decryption (of not provided object value will be used)
     * @return string The decrypted data
     */
    public function decrypt($encryptedData, $privateKey, $password = null, $padding = Adapter\AbstractAdapter::NO_PADDING);
}
