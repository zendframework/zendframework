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

namespace Zend\InfoCard\XML\EncryptedData;
use Zend\InfoCard\XML\AbstractElement,
    Zend\InfoCard\XML;

/**
 * An XmlEnc formatted EncryptedData XML block
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class XMLEnc extends AbstractEncryptedData
{

    /**
     * Returns the Encrypted CipherValue block from the EncryptedData XML document
     *
     * @throws XML\Exception\RuntimeException
     * @return string The value of the CipherValue block base64 encoded
     */
    public function getCipherValue()
    {
        $this->registerXPathNamespace('enc', 'http://www.w3.org/2001/04/xmlenc#');

        list(,$cipherdata) = $this->xpath("//enc:CipherData");

        if(!($cipherdata instanceof AbstractElement)) {
            throw new XML\Exception\RuntimeException("Unable to find the enc:CipherData block");
        }
        $cipherdata->registerXPathNamespace('enc', 'http://www.w3.org/2001/04/xmlenc#');;
        list(,$ciphervalue) = $cipherdata->xpath("//enc:CipherValue");

        if(!($ciphervalue instanceof AbstractElement)) {
            throw new XML\Exception\RuntimeException("Unable to fidn the enc:CipherValue block");
        }

        return (string)$ciphervalue;
    }
}
