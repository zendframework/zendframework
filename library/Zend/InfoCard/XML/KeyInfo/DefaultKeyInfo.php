<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\XML\KeyInfo;

use Zend\InfoCard\XML;

/**
 * An object representation of a XML <KeyInfo> block which doesn't provide a namespace
 * In this context, it is assumed to mean that it is the type of KeyInfo block which
 * contains the SecurityTokenReference
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */
class DefaultKeyInfo extends AbstractKeyInfo
{
    /**
     * Returns the object representation of the SecurityTokenReference block
     *
     * @throws XML\Exception\RuntimeException
     * @return XML\SecurityTokenReference
     */
    public function getSecurityTokenReference()
    {
        $this->registerXPathNamespace('o', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');

        list($sectokenref) = $this->xpath('//o:SecurityTokenReference');

        if(!($sectokenref instanceof XML\AbstractElement)) {
            throw new XML\Exception\RuntimeException('Could not locate the Security Token Reference');
        }

        return XML\SecurityTokenReference::getInstance($sectokenref);
    }
}
