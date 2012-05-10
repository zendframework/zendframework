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

namespace Zend\InfoCard\XML;

use DOMElement,
    SimpleXMLElement;

/**
 * An abstract class representing a an XML data block
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractElement extends SimpleXMLElement implements ElementInterface
{
    /**
     * Convert the object to a string by displaying its XML content
     *
     * @return string an XML representation of the object
     */
    public function __toString()
    {
        return $this->asXML();
    }

    /**
     * Converts an XML Element object into a DOM object
     *
     * @throws Exception\RuntimeException
     * @param \Zend\InfoCard\XML\ElementInterface $e The object to convert
     * @return DOMElement A DOMElement representation of the same object
     */
    static public function convertToDOM(ElementInterface $e)
    {
        $dom = dom_import_simplexml($e);

        if(!($dom instanceof DOMElement)) {
            // Zend_InfoCard_Xml_Element extends SimpleXMLElement, so this should *never* fail
            // @codeCoverageIgnoreStart
            throw new Exception\RuntimeException("Failed to convert between SimpleXML and DOM");
            // @codeCoverageIgnoreEnd
        }

        return $dom;
    }

    /**
     * Converts a DOMElement object into the specific class
     *
     * @throws Exception\ExceptionInterface
     * @param DOMElement $e The DOMElement object to convert
     * @param string $classname The name of the class to convert it to (must inhert from \Zend\InfoCard\XML\Element)
     * @return \Zend\InfoCard\XML\ElementInterface a Xml Element object from the DOM element
     */
    static public function convertToObject(\DOMElement $e, $classname)
    {
        if (!class_exists($classname)) {
            throw new Exception\InvalidArgumentException('Class provided for converting does not exist');
        }

        if(!is_subclass_of($classname, 'Zend\InfoCard\XML\ElementInterface')) {
            throw new Exception\InvalidArgumentException("DOM element must be converted to an instance of Zend_InfoCard_Xml_Element");
        }

        $sxe = simplexml_import_dom($e, $classname);

        if(!($sxe instanceof ElementInterface)) {
            // Since we just checked to see if this was a subclass of Zend_infoCard_Xml_Element this shoudl never fail
            // @codeCoverageIgnoreStart
            throw new Exception\RuntimeException("Failed to convert between DOM and SimpleXML");
            // @codeCoverageIgnoreEnd
        }

        return $sxe;
    }
}
