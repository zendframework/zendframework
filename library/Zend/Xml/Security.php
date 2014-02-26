<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Xml;

use DOMDocument;
use SimpleXMLElement;

class Security
{
    /**
     * Scan XML string for potential XXE and XEE attacks 
     *
     * @param   string $xml
     * @param   DomDocument $dom
     * @throws  Exception\RuntimeException
     * @return  SimpleXMLElement|DomDocument|boolean
     */
    public static function scan($xml, DOMDocument $dom = null)
    {
        if (null === $dom) {
            $simpleXml = true;
            $dom = new DOMDocument();
        } 

        // Disable entity load
        $loadEntities = libxml_disable_entity_loader(true);
        $useInternalXmlErrors = libxml_use_internal_errors(true);

        if (!$dom->loadXml($xml)) {
            // Entity load to previous setting
            libxml_disable_entity_loader($loadEntities);
            libxml_use_internal_errors($useInternalXmlErrors);
            return false;
        }

        // Scan for potential XEE attacks using Entity
        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                if ($child->entities->length > 0) {
                    throw new Exception\RuntimeException(
                        'Detected use of ENTITY_NODE in XML, disabled to prevent XEE attacks'
                    );
                }
            }
        }

        // Entity load to previous setting
        libxml_disable_entity_loader($loadEntities);
        libxml_use_internal_errors($useInternalXmlErrors);

        if (isset($simpleXml)) {
            $result = simplexml_import_dom($dom); 
            if (!$result instanceof SimpleXMLElement) {
                return false;
            }
            return $result;
        }
        return $dom;
    }

    /**
     * Scan XML file for potential XXE/XEE attacks
     *
     * @param  string $file
     * @param  DOMDocument $dom
     * @throws Exception\InvalidArgumentException
     * @return SimpleXMLElement|DomDocument
     */
    public static function scanFile($file, DOMDocument $dom = null)
    {
        if (!file_exists($file)) {
            throw new Exception\InvalidArgumentException(
                "The file $file specified doesn't exist"
            );
        }
        return self::scan(file_get_contents($file), $dom);
    }
}
