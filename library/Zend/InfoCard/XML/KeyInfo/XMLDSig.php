<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\XML\KeyInfo;

use Zend\InfoCard\XML;

/**
 * Represents a Xml Digital Signature XML Data Block
 *
 * @category   Zend
 * @package    Zend_InfoCard
 */
class XMLDSig extends AbstractKeyInfo implements KeyInfoInterface
{
    /**
     * Returns an instance of the EncryptedKey Data Block
     *
     * @throws XML\Exception\RuntimeException
     * @return XML\EncryptedKey
     */
    public function getEncryptedKey()
    {
        $this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
        list($encryptedkey) = $this->xpath('//e:EncryptedKey');

        if(!($encryptedkey instanceof XML\AbstractElement)) {
            throw new XML\Exception\RuntimeException("Failed to retrieve encrypted key");
        }

        return XML\EncryptedKey::getInstance($encryptedkey);
    }

    /**
     * Returns the KeyInfo Block within the encrypted key
     *
     * @return DefaultKeyInfo
     */
    public function getKeyInfo()
    {
        return $this->getEncryptedKey()->getKeyInfo();
    }
}
