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
 * An XmlEnc formatted EncryptedData XML block
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
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
