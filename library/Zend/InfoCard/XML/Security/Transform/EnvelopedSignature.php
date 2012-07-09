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

/**
 * A object implementing the EnvelopedSignature XML Transform
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml_Security
 */
class EnvelopedSignature implements TransformInterface
{
    /**
     * Transforms the XML Document according to the EnvelopedSignature Transform
     *
     * @throws Exception\InvalidArgumentException
     * @param string $strXMLData The input XML data
     * @return string the transformed XML data
     */
    public function transform($strXMLData)
    {
        $sxe = simplexml_load_string($strXMLData);

        if(!$sxe->Signature) {
            throw new Exception\InvalidArgumentException("Unable to locate Signature Block for EnvelopedSignature Transform");
        }

        unset($sxe->Signature);

        return $sxe->asXML();
    }
}
