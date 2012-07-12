<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\XML\Assertion;

use Zend\InfoCard\XML;

/**
 * Factory object to retrieve an Assertion object based on the type of XML document provided
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */
final class Factory
{
    /**
     * The namespace for a SAML-formatted Assertion document
     */
    const TYPE_SAML = 'urn:oasis:names:tc:SAML:1.0:assertion';

    /**
     * Constructor (disabled)
     *
     */
    private function __construct()
    {
    }

    /**
     * Returns an instance of a InfoCard Assertion object based on the XML data provided
     *
     * @throws XML\Exception\InvalidArgumentException
     * @param string $xmlData The XML-Formatted Assertion
     * @return object
     */
    static public function getInstance($xmlData)
    {

        if($xmlData instanceof XML\AbstractElement) {
            $strXmlData = $xmlData->asXML();
        } elseif (is_string($xmlData)) {
            $strXmlData = $xmlData;
        } else {
            throw new XML\Exception\InvalidArgumentException("Invalid Data provided to create instance");
        }

        $sxe = simplexml_load_string($strXmlData);

        $namespaces = $sxe->getDocNameSpaces();

        foreach($namespaces as $namespace) {
            switch($namespace) {
                case self::TYPE_SAML:
                    return simplexml_load_string($strXmlData, 'Zend\InfoCard\XML\Assertion\SAML', null);
            }
        }

        throw new XML\Exception\InvalidArgumentException("Unable to determine Assertion type by Namespace");
    }
}
