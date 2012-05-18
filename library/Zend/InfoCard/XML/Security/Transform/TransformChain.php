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
 * @subpackage Zend_InfoCard_Xml_Security
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\InfoCard\XML\Security\Transform;

use Zend\InfoCard\XML\Security\Transform,
    Zend\InfoCard\XML\Security;

/**
 * A class to create a transform rule set based on XML URIs and then apply those rules
 * in the correct order to a given XML input
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml_Security
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TransformChain
{
    /**
     * A list of transforms to apply
     *
     * @var array
     */
    protected $_transformList = array();

    /**
     * Returns the name of the transform class based on a given URI
     *
     * @throws Security\Exception\InvalidArgumentException
     * @param string $uri The transform URI
     * @return string The transform implementation class name
     */
    protected function _findClassbyURI($uri)
    {
        switch($uri) {
            case 'http://www.w3.org/2000/09/xmldsig#enveloped-signature':
                return 'Zend\InfoCard\XML\Security\Transform\EnvelopedSignature';
            case 'http://www.w3.org/2001/10/xml-exc-c14n#':
                return 'Zend\InfoCard\XML\Security\Transform\XMLExcC14N';
            default:
                throw new Security\Exception\InvalidArgumentException("Unknown or Unsupported Transformation Requested");
        }
    }

    /**
     * Add a Transform URI to the list of transforms to perform
     *
     * @param string $uri The Transform URI
     * @return TransformChain
     */
    public function addTransform($uri)
    {
        $class = $this->_findClassbyURI($uri);

        $this->_transformList[] = array('uri' => $uri,
                                        'class' => $class);
        return $this;
    }

    /**
     * Return the list of transforms to perform
     *
     * @return array The list of transforms
     */
    public function getTransformList()
    {
        return $this->_transformList;
    }

    /**
     * Apply the transforms in the transform list to the input XML document
     *
     * @param string $strXmlDocument The input XML
     * @return string The XML after the transformations have been applied
     * @throws Security\Exception\RuntimeException
     */
    public function applyTransforms($strXmlDocument)
    {
        foreach($this->_transformList as $transform) {
            if (!class_exists($transform['class'])) {
                throw new Security\Exception\InvalidArgumentException('Transform Class not exist');
            }

            $transformer = new $transform['class'];

            // We can't really test this check because it would require logic changes in the component itself
            // @codeCoverageIgnoreStart
            if(!($transformer instanceof TransformInterface)) {
                throw new Security\Exception\RuntimeException("Transforms must implement the Transform Interface");
            }
            // @codeCoverageIgnoreEnd

            $strXmlDocument = $transformer->transform($strXmlDocument);
        }

        return $strXmlDocument;
    }
}
