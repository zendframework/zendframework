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
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\InfoCard\XML;

/**
 * An object representing an Xml EncryptedKEy block
 *
 * @uses       \Zend\InfoCard\XML\AbstractElement
 * @uses       \Zend\InfoCard\XML\EncryptedKey
 * @uses       \Zend\InfoCard\XML\Exception
 * @uses       \Zend\InfoCard\XML\KeyInfo\Factory
 * @uses       \Zend\InfoCard\XML\KeyInfo
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class EncryptedKey extends AbstractElement implements KeyInfo
{
    /**
     * Return an instance of the object based on input XML Data
     *
     * @throws \Zend\InfoCard\XML\Exception
     * @param string $xmlData The EncryptedKey XML Block
     * @return \Zend\InfoCard\XML\EncryptedKey
     */
    static public function getInstance($xmlData)
    {
        if($xmlData instanceof AbstractElement) {
            $strXmlData = $xmlData->asXML();
        } else if (is_string($xmlData)) {
            $strXmlData = $xmlData;
        } else {
            throw new Exception\InvalidArgumentException("Invalid Data provided to create instance");
        }

        $sxe = simplexml_load_string($strXmlData);

        if($sxe->getName() != "EncryptedKey") {
            throw new Exception\InvalidArgumentException("Invalid XML Block provided for EncryptedKey");
        }

        return simplexml_load_string($strXmlData, 'Zend\InfoCard\XML\EncryptedKey');
    }

    /**
     * Returns the Encyption Method Algorithm URI of the block
     *
     * @throws \Zend\InfoCard\XML\Exception
     * @return string the Encryption method algorithm URI
     */
    public function getEncryptionMethod()
    {

        $this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
        list($encryption_method) = $this->xpath("//e:EncryptionMethod");

        if(!($encryption_method instanceof AbstractElement)) {
            throw new Exception\RuntimeException("Unable to find the e:EncryptionMethod KeyInfo encryption block");
        }

        $dom = self::convertToDOM($encryption_method);

        if(!$dom->hasAttribute('Algorithm')) {
            throw new Exception\RuntimeException("Unable to determine the encryption algorithm in the Symmetric enc:EncryptionMethod XML block");
        }

        return $dom->getAttribute('Algorithm');

    }

    /**
     * Returns the Digest Method Algorithm URI used
     *
     * @throws \Zend\InfoCard\XML\Exception
     * @return string the Digest Method Algorithm URI
     */
    public function getDigestMethod()
    {
        $this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
        list($encryption_method) = $this->xpath("//e:EncryptionMethod");

        if(!($encryption_method instanceof AbstractElement)) {
            throw new Exception\RuntimeException("Unable to find the e:EncryptionMethod KeyInfo encryption block");
        }

        if(!($encryption_method->DigestMethod instanceof AbstractElement)) {
            throw new Exception\RuntimeException("Unable to find the DigestMethod block");
        }

        $dom = self::convertToDOM($encryption_method->DigestMethod);

        if(!$dom->hasAttribute('Algorithm')) {
            throw new Exception\RuntimeException("Unable to determine the digest algorithm for the symmetric Keyinfo");
        }

        return $dom->getAttribute('Algorithm');

    }

    /**
     * Returns the KeyInfo block object
     *
     * @throws \Zend\InfoCard\XML\Exception
     * @return \Zend\InfoCard\XML\KeyInfo\AbstractKeyInfo
     */
    public function getKeyInfo()
    {

        if(isset($this->KeyInfo)) {
            return KeyInfo\Factory::getInstance($this->KeyInfo);
        }

        throw new Exception\RuntimeException("Unable to locate a KeyInfo block");
    }

    /**
     * Return the encrypted value of the block in base64 format
     *
     * @throws \Zend\InfoCard\XML\Exception
     * @return string The Value of the CipherValue block in base64 format
     */
    public function getCipherValue()
    {

        $this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');

        list($cipherdata) = $this->xpath("//e:CipherData");

        if(!($cipherdata instanceof AbstractElement)) {
            throw new Exception\RuntimeException("Unable to find the e:CipherData block");
        }

        $cipherdata->registerXPathNameSpace('enc', 'http://www.w3.org/2001/04/xmlenc#');
        list($ciphervalue) = $cipherdata->xpath("//enc:CipherValue");

        if(!($ciphervalue instanceof AbstractElement)) {
            throw new Exception\RuntimeException("Unable to fidn the enc:CipherValue block");
        }

        return (string)$ciphervalue;
    }
}
