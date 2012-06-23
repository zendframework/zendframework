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

namespace Zend\InfoCard\XML\KeyInfo;

use Zend\InfoCard\XML;

/**
 * Represents a Xml Digital Signature XML Data Block
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
