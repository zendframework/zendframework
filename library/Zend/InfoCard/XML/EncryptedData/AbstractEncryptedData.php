<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\XML\EncryptedData;

use Zend\InfoCard\XML;
use Zend\InfoCard\XML\AbstractElement;

/**
 * An abstract class representing a generic EncryptedData XML block. This class is extended
 * into a specific type of EncryptedData XML block (i.e. XmlEnc) as necessary
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */
abstract class AbstractEncryptedData extends AbstractElement
{

    /**
     * Returns the KeyInfo Block
     *
     * @return \Zend\InfoCard\XML\KeyInfo\AbstractKeyInfo
     */
    public function getKeyInfo()
    {
        return XML\KeyInfo\Factory::getInstance($this->KeyInfo[0]);
    }

    /**
     * Return the Encryption method used to encrypt the assertion document
     * (the symmetric cipher)
     *
     * @throws XML\Exception\RuntimeException
     * @return string The URI of the Symmetric Encryption Method used
     */
    public function getEncryptionMethod()
    {

        /**
         * @todo This is pretty hacky unless we can always be confident that the first
         * EncryptionMethod block is the correct one (the AES or compariable symetric algorithm)..
         * the second is the PK method if provided.
         */
        list($encryption_method) = $this->xpath("//enc:EncryptionMethod");

        if(!($encryption_method instanceof AbstractElement)) {
            throw new XML\Exception\RuntimeException("Unable to find the enc:EncryptionMethod symmetric encryption block");
        }

        $dom = self::convertToDOM($encryption_method);

        if(!$dom->hasAttribute('Algorithm')) {
            throw new XML\Exception\RuntimeException("Unable to determine the encryption algorithm in the Symmetric enc:EncryptionMethod XML block");
        }

        return $dom->getAttribute('Algorithm');
    }

    /**
     * Returns the value of the encrypted block
     *
     * @return string the value of the encrypted CipherValue block
     */
    abstract public function getCipherValue();
}
