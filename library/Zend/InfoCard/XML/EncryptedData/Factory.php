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

/**
 * A factory class for producing Zend_InfoCard_Xml_EncryptedData objects based on
 * the type of XML document provided
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */
final class Factory
{
    /**
     * Constructor (disabled)
     *
     */
    private function __construct()
    {
    }

    /**
     * Returns an instance of the class
     *
     * @param string $xmlData The XML EncryptedData String
     * @return \Zend\InfoCard\XML\EncryptedData\AbstractEncryptedData
     * @throws XML\Exception\InvalidArgumentException
     */
    public static function getInstance($xmlData)
    {

        if($xmlData instanceof XML\AbstractElement) {
            $strXmlData = $xmlData->asXML();
        } elseif (is_string($xmlData)) {
            $strXmlData = $xmlData;
        } else {
            throw new XML\Exception\InvalidArgumentException("Invalid Data provided to create instance");
        }

        $sxe = simplexml_load_string($strXmlData);

        switch($sxe['Type']) {
            case 'http://www.w3.org/2001/04/xmlenc#Element':
                return simplexml_load_string($strXmlData, 'Zend\InfoCard\XML\EncryptedData\XMLEnc');
            default:
                throw new XML\Exception\InvalidArgumentException("Unknown EncryptedData type found");
                break;
        }
    }
}
