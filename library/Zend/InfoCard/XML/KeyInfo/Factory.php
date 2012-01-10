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
namespace Zend\InfoCard\XML\KeyInfo;
use Zend\InfoCard\XML;

/**
 * Factory class to return a XML KeyInfo block based on input XML
 *
 * @uses       \Zend\InfoCard\XML\AbstractElement
 * @uses       \Zend\InfoCard\XML\Exception
 * @uses       \Zend\InfoCard\XML\KeyInfo\Default
 * @uses       \Zend\InfoCard\XML\KeyInfo\XMLDSig
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Factory
{
    /**
     * Constructor (disabled)
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Returns an instance of KeyInfo object based on the input KeyInfo XML block
     *
     * @param string $xmlData The KeyInfo XML Block
     * @return \Zend\InfoCard\XML\KeyInfo\AbstractKeyInfo
     * @throws \Zend\InfoCard\XML\Exception
     */
    static public function getInstance($xmlData)
    {

        if($xmlData instanceof XML\AbstractElement) {
            $strXmlData = $xmlData->asXML();
        } else if (is_string($xmlData)) {
            $strXmlData = $xmlData;
        } else {
            throw new XML\Exception\InvalidArgumentException("Invalid Data provided to create instance");
        }

        $sxe = simplexml_load_string($strXmlData);

        $namespaces = $sxe->getDocNameSpaces();

        if(!empty($namespaces)) {
            foreach($sxe->getDocNameSpaces() as $namespace) {
                switch($namespace) {
                    case 'http://www.w3.org/2000/09/xmldsig#':
                        return simplexml_load_string($strXmlData, 'Zend\InfoCard\XML\KeyInfo\XMLDSig');
                    default:
                        throw new XML\Exception\RuntimeException("Unknown KeyInfo Namespace provided");
                    // We are ignoring these lines, as XDebug reports each as a "non executed" line
                    // which breaks my coverage %
                    // @codeCoverageIgnoreStart
                }
            }
        }
        // @codeCoverageIgnoreEnd

        return simplexml_load_string($strXmlData, 'Zend\InfoCard\XML\KeyInfo\DefaultKeyInfo');
    }
}
