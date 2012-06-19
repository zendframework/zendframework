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
 * @package    Zend_Authentication
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Authentication\Adapter;

use Zend\Authentication\Result as AuthenticationResult;
use Zend\InfoCard as ZendInfoCard;

/**
 * A Zend_Auth Authentication Adapter allowing the use of Information Cards as an
 * authentication mechanism
 *
 * @category   Zend
 * @package    Zend_Authentication
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InfoCard implements AdapterInterface
{
    /**
     * The XML Token being authenticated
     *
     * @var string
     */
    protected $_xmlToken;

    /**
     * The instance of Zend\InfoCard
     *
     * @var \Zend\InfoCard\InfoCard
     */
    protected $_infoCard;

    /**
     * Constructor
     *
     * @param  string $strXmlDocument The XML Token provided by the client
     */
    public function __construct($strXmlDocument)
    {
        $this->_xmlToken = $strXmlDocument;
        $this->_infoCard = new ZendInfoCard\InfoCard();
    }

    /**
     * Sets the InfoCard component Adapter to use
     *
     * @param  ZendInfoCard\Adapter\AdapterInterface $a
     * @return InfoCard Provides a fluent interface
     */
    public function setAdapter(ZendInfoCard\Adapter\AdapterInterface $a)
    {
        $this->_infoCard->setAdapter($a);
        return $this;
    }

    /**
     * Retrieves the InfoCard component adapter being used
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->_infoCard->getAdapter();
    }

    /**
     * Retrieves the InfoCard public key cipher object being used
     *
     * @return ZendInfoCard\Cipher\PKI\PKIInterface
     */
    public function getPKCipherObject()
    {
        return $this->_infoCard->getPKCipherObject();
    }

    /**
     * Sets the InfoCard public key cipher object to use
     *
     * @param  ZendInfoCard\Cipher\PKI\PKIInterface $cipherObj
     * @return InfoCard Provides a fluent interface
     */
    public function setPKICipherObject(ZendInfoCard\Cipher\PKI\PKIInterface $cipherObj)
    {
        $this->_infoCard->setPKICipherObject($cipherObj);
        return $this;
    }

    /**
     * Retrieves the Symmetric cipher object being used
     *
     * @return ZendInfoCard\Cipher\Symmetric\AES128CBCInterface
     */
    public function getSymCipherObject()
    {
        return $this->_infoCard->getSymCipherObject();
    }

    /**
     * Sets the InfoCard symmetric cipher object to use
     *
     * @param  ZendInfoCard\Cipher\Symmetric\AES128CBCInterface $cipherObj
     * @return InfoCard Provides a fluent interface
     */
    public function setSymCipherObject(ZendInfoCard\Cipher\Symmetric\AES128CBCInterface $cipherObj)
    {
        $this->_infoCard->setSymCipherObject($cipherObj);
        return $this;
    }

    /**
     * Remove a Certificate Pair by Key ID from the search list
     *
     * @param  string $keyId The Certificate Key ID returned from adding the certificate pair
     * @throws ZendInfoCard\Exception\ExceptionInterface
     * @return InfoCard Provides a fluent interface
     */
    public function removeCertificatePair($keyId)
    {
        $this->_infoCard->removeCertificatePair($keyId);
        return $this;
    }

    /**
     * Add a Certificate Pair to the list of certificates searched by the component
     *
     * @param  string $privateKeyFile    The path to the private key file for the pair
     * @param  string $publicKeyFile     The path to the certificate / public key for the pair
     * @param  string $type                (optional) The URI for the type of key pair this is (default RSA with OAEP padding)
     * @param  string $password            (optional) The password for the private key file if necessary
     * @throws ZendInfoCard\Exception\ExceptionInterface
     * @return string A key ID representing this key pair in the component
     */
    public function addCertificatePair($privateKeyFile, $publicKeyFile, $type = ZendInfoCard\Cipher::ENC_RSA_OAEP_MGF1P, $password = null)
    {
        return $this->_infoCard->addCertificatePair($privateKeyFile, $publicKeyFile, $type, $password);
    }

    /**
     * Return a Certificate Pair from a key ID
     *
     * @param  string $keyId The Key ID of the certificate pair in the component
     * @throws ZendInfoCard\Exception\ExceptionInterface
     * @return array An array containing the path to the private/public key files,
     *               the type URI and the password if provided
     */
    public function getCertificatePair($keyId)
    {
        return $this->_infoCard->getCertificatePair($keyId);
    }

    /**
     * Set the XML Token to be processed
     *
     * @param  string $strXmlToken The XML token to process
     * @return \Zend\Authentication\Adapter\InfoCard Provides a fluent interface
     */
    public function setXmlToken($strXmlToken)
    {
        $this->_xmlToken = $strXmlToken;
        return $this;
    }

    /**
     * Get the XML Token being processed
     *
     * @return string The XML token to be processed
     */
    public function getXmlToken()
    {
        return $this->_xmlToken;
    }

    /**
     * Authenticates the XML token
     *
     * @return AuthenticationResult The result of the authentication
     */
    public function authenticate()
    {
        try {
            $claims = $this->_infoCard->process($this->getXmlToken());
        } catch(\Exception $e) {
            return new AuthenticationResult(
                AuthenticationResult::FAILURE,
                null,
                array('Exception Thrown',
                      $e->getMessage(),
                      $e->getTraceAsString(),
                      serialize($e)));
        }

        if (!$claims->isValid()) {
            switch($claims->getCode()) {
                case ZendInfoCard\Claims::RESULT_PROCESSING_FAILURE:
                    return new AuthenticationResult(
                        AuthenticationResult::FAILURE,
                        $claims,
                        array(
                            'Processing Failure',
                            $claims->getErrorMsg()
                        )
                    );
                    break;
                case ZendInfoCard\Claims::RESULT_VALIDATION_FAILURE:
                    return new AuthenticationResult(
                        AuthenticationResult::FAILURE_CREDENTIAL_INVALID,
                        $claims,
                        array(
                            'Validation Failure',
                            $claims->getErrorMsg()
                        )
                    );
                    break;
                default:
                    return new AuthenticationResult(
                        AuthenticationResult::FAILURE,
                        $claims,
                        array(
                            'Unknown Failure',
                            $claims->getErrorMsg()
                        )
                    );
                    break;
            }
        }

        return new AuthenticationResult(
            AuthenticationResult::SUCCESS,
            $claims
        );
    }
}
