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

/**
 * Interface for XML Security Transforms
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml_Security
 */
interface TransformInterface
{
    /**
     * Transform the given XML string according to the transform rules
     * implemented by the object using this interface
     *
     * @throws \Zend\InfoCard\XML\Security\Exception\ExceptionInterface
     * @param string $strXmlData the input XML
     * @return string the output XML
     */
    public function transform($strXmlData);
}
