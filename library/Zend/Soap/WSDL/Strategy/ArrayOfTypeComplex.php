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
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Soap\WSDL\Strategy;

use Zend\Soap;

use Zend\Soap\WSDL;
use Zend\Soap\WSDLException;

/**
 * ArrayOfTypeComplex strategy
 *
 * @uses       \Zend\Soap\WSDL\Exception
 * @uses       \Zend\Soap\WSDL\Strategy\DefaultComplexType
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ArrayOfTypeComplex extends DefaultComplexType
{
    protected $_inProcess = array();

    /**
     * Add an ArrayOfType based on the xsd:complexType syntax if type[] is detected in return value doc comment.
     *
     * @param string $type
     * @return string tns:xsd-type
     */
    public function addComplexType($type)
    {
        if(isset($this->_inProcess[$type])) {
            throw new WSDLException("Infinite recursion, cannot nest '$type' into itself.");
        }
        $this->_inProcess[$type] = $type;

        $nestingLevel = $this->_getNestedCount($type);

        if($nestingLevel > 1) {
            throw new WSDLException(
                'ArrayOfTypeComplex cannot return nested ArrayOfObject deeper than '
              . 'one level. Use array object properties to return deep nested data.'
            );
        }

        $singularType = $this->_getSingularPhpType($type);

        if(!class_exists($singularType)) {
            throw new WSDLException(sprintf(
                'Cannot add a complex type %s that is not an object or where '
              . 'class could not be found in \'DefaultComplexType\' strategy.', $type
            ));
        }

        if($nestingLevel == 1) {
            // The following blocks define the Array of Object structure
            $xsdComplexTypeName = $this->_addArrayOfComplexType($singularType, $type);
        } else {
            $xsdComplexTypeName = WSDL::translateType($singularType);
        }

        // The array for the objects has been created, now build the object definition:
        if(!array_key_exists($singularType, $this->getContext()->getTypes())) {
            parent::addComplexType($singularType);
        }

        unset($this->_inProcess[$type]);
        return 'tns:' . $xsdComplexTypeName;
    }

    protected function _addArrayOfComplexType($singularType, $type)
    {
        $dom = $this->getContext()->toDomDocument();

        $xsdComplexTypeName = $this->_getXsdComplexTypeName($singularType);

        if(!array_key_exists($singularType . '[]', $this->getContext()->getTypes())) {
            $complexType = $dom->createElement('xsd:complexType');
            $complexType->setAttribute('name', $xsdComplexTypeName);

            $complexContent = $dom->createElement('xsd:complexContent');
            $complexType->appendChild($complexContent);

            $xsdRestriction = $dom->createElement('xsd:restriction');
            $xsdRestriction->setAttribute('base', 'soap-enc:Array');
            $complexContent->appendChild($xsdRestriction);

            $xsdAttribute = $dom->createElement('xsd:attribute');
            $xsdAttribute->setAttribute('ref', 'soap-enc:arrayType');
            $xsdAttribute->setAttribute('wsdl:arrayType',
                                        'tns:' . WSDL::translateType($singularType) . '[]');
            $xsdRestriction->appendChild($xsdAttribute);

            $this->getContext()->getSchema()->appendChild($complexType);
            $this->getContext()->addType($singularType . '[]', $xsdComplexTypeName);
        }

        return $xsdComplexTypeName;
    }

    protected function _getXsdComplexTypeName($type)
    {
        return 'ArrayOf' . WSDL::translateType($type);
    }

    /**
     * From a nested definition with type[], get the singular PHP Type
     *
     * @param  string $type
     * @return string
     */
    protected function _getSingularPhpType($type)
    {
        return str_replace('[]', '', $type);
    }

    /**
     * Return the array nesting level based on the type name
     *
     * @param  string $type
     * @return integer
     */
    protected function _getNestedCount($type)
    {
        return substr_count($type, '[]');
    }
}
