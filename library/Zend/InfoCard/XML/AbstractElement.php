<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\XML;

use DOMElement;
use ReflectionClass;
use SimpleXMLElement;

/**
 * An abstract class representing a an XML data block
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
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
    public static function convertToDOM(ElementInterface $e)
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
    public static function convertToObject(\DOMElement $e, $classname)
    {
        if (!class_exists($classname)) {
            throw new Exception\InvalidArgumentException('Class provided for converting does not exist');
        }

        if (!self::isSubclassOf($classname, 'Zend\InfoCard\XML\ElementInterface')) {
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

    /**
     * Checks if the object has this class as one of its parents
     *
     * @see https://bugs.php.net/bug.php?id=53727
     * @see https://github.com/zendframework/zf2/pull/1807
     *
     * @param string $className
     * @param string $type
     */
    protected static function isSubclassOf($className, $type)
    {
        if (version_compare(PHP_VERSION, '5.3.7', '>=')) {
            return is_subclass_of($className, $type);
        }
        if (is_subclass_of($className, $type)) {
            return true;
        }
        if (!interface_exists($type)) {
            return false;
        }
        $r = new ReflectionClass($className);
        return $r->implementsInterface($type);
    }
}
