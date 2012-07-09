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

/**
 * The Interface used to represent an XML Data Type
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */
interface ElementInterface
{
    /**
     * Return the data within the object as an XML document
     */
    public function asXML();

    /**
     * Magic function which allows us to treat the object as a string to return XML
     * (same as the asXML() method)
     */
    public function __toString();
}
