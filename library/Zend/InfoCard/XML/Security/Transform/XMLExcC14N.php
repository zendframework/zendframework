<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\XML\Security\Transform;

use Zend\InfoCard\XML\Security\Exception;
use Zend\InfoCard\XML\Security\Transform;

/**
 * A Transform to perform C14n XML Exclusive Canonicalization
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml_Security
 */
class XMLExcC14N implements TransformInterface
{
    /**
     * Transform the input XML based on C14n XML Exclusive Canonicalization rules
     *
     * @throws Exception\RuntimeException
     * @param string $strXMLData The input XML
     * @return string The output XML
     */
    public function transform($strXMLData)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($strXMLData);

        if(method_exists($dom, 'C14N')) {
            return $dom->C14N(true, false);
        }

        throw new Exception\RuntimeException("This transform requires the C14N() method to exist in the DOM extension");
    }
}
