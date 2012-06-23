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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\InfoCard;

/**
 * @category   Zend
 * @package    Zend_InfoCard
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InfoCard
{
    /**
     * URI for XML Digital Signature SHA1 Digests
     */
    const DIGEST_SHA1        = 'http://www.w3.org/2000/09/xmldsig#sha1';

    /**
     * An array of certificate pair files and optional passwords for them to search
     * when trying to determine which certificate was used to encrypt the transient key
     *
     * @var Array
     */
    protected $_keyPairs;

    /**
     * The instance to use to decrypt public-key encrypted data
     *
     * @var \Zend\InfoCard\Cipher\PKI\PKIInterface
     */
    protected $_pkiCipherObj;

    /**
     * The instance to use to decrypt symmetric encrypted data
     *
     * @var \Zend\InfoCard\Cipher\Symmetric\SymmetricInterface
     */
    protected $_symCipherObj;

    /**
     * The InfoCard Adapter to use for callbacks into the application using the component
     * such as when storing assertions, etc.
     *
     * @var \Zend\InfoCard\Adapter\AdapterInterface
     */
    protected $_adapter;


    /**
     * InfoCard Constructor
     *
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct()
    {
        $this->_keyPairs = array();

        if(!extension_loaded('mcrypt')) {
            throw new Exception\ExtensionNotLoadedException("Use of the Zend_InfoCard component requires the mcrypt extension to be enabled in PHP");
        }

        if(!extension_loaded('openssl')) {
            throw new Exception\ExtensionNotLoadedException("Use of the Zend_InfoCard component requires the openssl extension to be enabled in PHP");
        }
    }

    /**
     * Sets the adapter uesd for callbacks into the application using the component, used
     * when doing things such as storing / retrieving assertions, etc.
     *
     * @param Adapter\AdapterInterface $a The Adapter instance
     * @return InfoCard The instance
     */
    public function setAdapter(Adapter\AdapterInterface $a)
    {
        $this->_adapter = $a;
        return $this;
    }

    /**
     * Retrieves the adapter used for callbacks into the application using the component.
     * If no adapter was set then an instance of Zend_InfoCard_Adapter_Default is used
     *
     * @return Adapter\AdapterInterface The Adapter instance
     */
    public function getAdapter()
    {
        if($this->_adapter === null) {
            $this->setAdapter(new Adapter\DefaultAdapter());
        }

        return $this->_adapter;
    }

    /**
     * Gets the Public Key Cipher object used in this instance
     *
     * @return \Zend\InfoCard\Cipher\PKI\PKIInterface
     */
    public function getPkiCipherObject()
    {
        return $this->_pkiCipherObj;
    }

    /**
     * Sets the Public Key Cipher Object used in this instance
     *
     * @param \Zend\InfoCard\Cipher\PKI\PKIInterface $cipherObj
     * @return \Zend\InfoCard\InfoCard
     */
    public function setPkiCipherObject(Cipher\PKI\PKIInterface $cipherObj)
    {
        $this->_pkiCipherObj = $cipherObj;
        return $this;
    }

    /**
     * Get the Symmetric Cipher Object used in this instance
     *
     * @return \Zend\InfoCard\Cipher\Symmetric\SymmetricInterface
     */
    public function getSymCipherObject()
    {
        return $this->_symCipherObj;
    }

    /**
     * Sets the Symmetric Cipher Object used in this instance
     *
     * @param \Zend\InfoCard\Cipher\Symmetric\SymmetricInterface $cipherObj
     * @return \Zend\InfoCard\InfoCard
     */
    public function setSymCipherObject($cipherObj)
    {
        $this->_symCipherObj = $cipherObj;
        return $this;
    }

    /**
     * Remove a Certificate Pair by Key ID from the search list
     *
     * @throws Exception\InvalidArgumentException
     * @param string $key_id The Certificate Key ID returned from adding the certificate pair
     * @return \Zend\InfoCard\InfoCard
     */
    public function removeCertificatePair($key_id)
    {

        if(!array_key_exists($key_id, $this->_keyPairs)) {
            throw new Exception\InvalidArgumentException("Attempted to remove unknown key id: $key_id");
        }

        unset($this->_keyPairs[$key_id]);
        return $this;
    }

    /**
     * Add a Certificate Pair to the list of certificates searched by the component
     *
     * @throws Exception\InvalidArgumentException
     * @param string $private_key_file The path to the private key file for the pair
     * @param string $public_key_file The path to the certificate / public key for the pair
     * @param string $type (optional) The URI for the type of key pair this is (default RSA with OAEP padding)
     * @param string $password (optional) The password for the private key file if necessary
     * @return string A key ID representing this key pair in the component
     */
    public function addCertificatePair($private_key_file, $public_key_file, $type = Cipher::ENC_RSA_OAEP_MGF1P, $password = null)
    {
        if(!file_exists($private_key_file) ||
           !file_exists($public_key_file)) {
            throw new Exception\InvalidArgumentException("Could not locate the public and private certificate pair files: $private_key_file, $public_key_file");
        }

        if(!is_readable($private_key_file) ||
           !is_readable($public_key_file)) {
            throw new Exception\InvalidArgumentException("Could not read the public and private certificate pair files (check permissions): $private_key_file, $public_key_file");
        }

        $key_id = md5($private_key_file.$public_key_file);

        if(array_key_exists($key_id, $this->_keyPairs)) {
            throw new Exception\InvalidArgumentException("Attempted to add previously existing certificate pair: $private_key_file, $public_key_file");
        }

        switch($type) {
            case Cipher::ENC_RSA:
            case Cipher::ENC_RSA_OAEP_MGF1P:
                $this->_keyPairs[$key_id] = array('private' => $private_key_file,
                                'public'      => $public_key_file,
                                'type_uri'    => $type);

                if($password !== null) {
                    $this->_keyPairs[$key_id]['password'] = $password;
                } else {
                    $this->_keyPairs[$key_id]['password'] = null;
                }

                return $key_id;
                break;
            default:
                throw new Exception\InvalidArgumentException("Invalid Certificate Pair Type specified: $type");
        }
    }

    /**
     * Return a Certificate Pair from a key ID
     *
     * @throws Exception\InvalidArgumentException
     * @param string $key_id The Key ID of the certificate pair in the component
     * @return array An array containing the path to the private/public key files,
     *               the type URI and the password if provided
     */
    public function getCertificatePair($key_id)
    {
        if(array_key_exists($key_id, $this->_keyPairs)) {
            return $this->_keyPairs[$key_id];
        }

        throw new Exception\InvalidArgumentException("Invalid Certificate Pair ID provided: $key_id");
    }

    /**
     * Retrieve the digest of a given public key / certificate using the provided digest
     * method
     *
     * @throws Exception\InvalidArgumentException
     * @param string $key_id The certificate key id in the component
     * @param string $digestMethod The URI of the digest method to use (default SHA1)
     * @return string The digest value in binary format
     */
    protected function _getPublicKeyDigest($key_id, $digestMethod = self::DIGEST_SHA1)
    {
        $certificatePair = $this->getCertificatePair($key_id);

        $temp = file($certificatePair['public']);
        unset($temp[count($temp)-1]);
        unset($temp[0]);
        $certificateData = base64_decode(implode("\n", $temp));

        switch($digestMethod) {
            case self::DIGEST_SHA1:
                $digest_retval = sha1($certificateData, true);
                break;
            default:
                throw new Exception\InvalidArgumentException("Invalid Digest Type Provided: $digestMethod");
        }

        return $digest_retval;
    }

    /**
     * Find a certificate pair based on a digest of its public key / certificate file
     *
     * @param string $digest The digest value of the public key wanted in binary form
     * @param string $digestMethod The URI of the digest method used to calculate the digest
     * @return mixed The Key ID of the matching certificate pair or false if not found
     */
    protected function _findCertifiatePairByDigest($digest, $digestMethod = self::DIGEST_SHA1)
    {

        foreach($this->_keyPairs as $key_id => $certificate_data) {

            $cert_digest = $this->_getPublicKeyDigest($key_id, $digestMethod);

            if($cert_digest == $digest) {
                return $key_id;
            }
        }

        return false;
    }

    /**
     * Extracts the Signed Token from an EncryptedData block
     *
     * @throws Exception\RuntimeException
     * @param string $strXmlToken The EncryptedData XML block
     * @return string The XML of the Signed Token inside of the EncryptedData block
     */
    protected function _extractSignedToken($strXmlToken)
    {
        $encryptedData = XML\EncryptedData\Factory::getInstance($strXmlToken);

        // Determine the Encryption Method used to encrypt the token

        switch($encryptedData->getEncryptionMethod()) {
            case Cipher::ENC_AES128CBC:
            case Cipher::ENC_AES256CBC:
                break;
            default:
                throw new Exception\RuntimeException("Unknown Encryption Method used in the secure token");
        }

        // Figure out the Key we are using to decrypt the token

        $keyinfo = $encryptedData->getKeyInfo();

        if(!($keyinfo instanceof XML\KeyInfo\XMLDSig)) {
            throw new Exception\RuntimeException("Expected a XML digital signature KeyInfo, but was not found");
        }


        $encryptedKey = $keyinfo->getEncryptedKey();

        switch($encryptedKey->getEncryptionMethod()) {
            case Cipher::ENC_RSA:
            case Cipher::ENC_RSA_OAEP_MGF1P:
                break;
            default:
                throw new Exception\RuntimeException("Unknown Key Encryption Method used in secure token");
        }

        $securityTokenRef = $encryptedKey->getKeyInfo()->getSecurityTokenReference();

        $key_id = $this->_findCertifiatePairByDigest($securityTokenRef->getKeyReference());

        if(!$key_id) {
            throw new Exception\RuntimeException("Unable to find key pair used to encrypt symmetric InfoCard Key");
        }

        $certificate_pair = $this->getCertificatePair($key_id);

        // Santity Check

        if($certificate_pair['type_uri'] != $encryptedKey->getEncryptionMethod()) {
            throw new Exception\RuntimeException("Certificate Pair which matches digest is not of same algorithm type as document, check addCertificate()");
        }

        $PKcipher = Cipher::getInstanceByURI($encryptedKey->getEncryptionMethod());

        $keyCipherValueBase64Decoded = base64_decode($encryptedKey->getCipherValue(), true);

        $symmetricKey = $PKcipher->decrypt(
            $keyCipherValueBase64Decoded,
            file_get_contents($certificate_pair['private']),
            $certificate_pair['password']
            );

        $symCipher = Cipher::getInstanceByURI($encryptedData->getEncryptionMethod());

        $dataCipherValueBase64Decoded = base64_decode($encryptedData->getCipherValue(), true);

        $signedToken = $symCipher->decrypt($dataCipherValueBase64Decoded, $symmetricKey);

        return $signedToken;
    }

    /**
     * Process an input Infomation Card EncryptedData block sent from the client,
     * validate it, and return the claims contained within it on success or an error message on error
     *
     * @param string $strXmlToken The XML token sent to the server from the client
     * @return Claims The Claims object containing the claims, or any errors which occurred
     */
    public function process($strXmlToken)
    {

        $retval = new Claims();

        try {
            $signedAssertionsXml = $this->_extractSignedToken($strXmlToken);
        } catch(Exception\RuntimeException $e) {
            $retval->setError('Failed to extract assertion document');
            $retval->setCode(Claims::RESULT_PROCESSING_FAILURE);
            return $retval;
        }

        try {
            $assertions = XML\Assertion\Factory::getInstance($signedAssertionsXml);
        } catch(Exception\InvalidArgumentException $e) {
            $retval->setError('Failure processing assertion document');
            $retval->setCode(Claims::RESULT_PROCESSING_FAILURE);
            return $retval;
        }

        if(!($assertions instanceof XML\Assertion\AssertionInterface)) {
            throw new Exception\RuntimeException("Invalid Assertion Object returned");
        }

        if(!($reference_id = XML\Security::validateXMLSignature($assertions->asXML()))) {
            $retval->setError("Failure Validating the Signature of the assertion document");
            $retval->setCode(Claims::RESULT_VALIDATION_FAILURE);
            return $retval;
        }

        // The reference id should be locally scoped as far as I know
        if($reference_id[0] == '#') {
            $reference_id = substr($reference_id, 1);
        } else {
            $retval->setError("Reference of document signature does not reference the local document");
            $retval->setCode(Claims::RESULT_VALIDATION_FAILURE);
            return $retval;
        }

        // Make sure the signature is in reference to the same document as the assertions
        if($reference_id != $assertions->getAssertionID()) {
            $retval->setError("Reference of document signature does not reference the local document");
            $retval->setCode(Claims::RESULT_VALIDATION_FAILURE);
        }

        // Validate we haven't seen this before and the conditions are acceptable
        $conditions = $this->getAdapter()->retrieveAssertion($assertions->getAssertionURI(), $assertions->getAssertionID());

        if($conditions === false) {
            $conditions = $assertions->getConditions();
        }


        if(is_array($condition_error = $assertions->validateConditions($conditions))) {
            $retval->setError("Conditions of assertion document are not met: {$condition_error[1]} ({$condition_error[0]})");
            $retval->setCode(Claims::RESULT_VALIDATION_FAILURE);
        }

        $attributes = $assertions->getAttributes();

        $retval->setClaims($attributes);

        if($retval->getCode() == 0) {
            $retval->setCode(Claims::RESULT_SUCCESS);
        }

        return $retval;
    }
}
