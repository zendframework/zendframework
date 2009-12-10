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
 * @package    Zend_XmlRpc
 * @subpackage Generator
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Client.php 17752 2009-08-22 15:49:54Z lars $
 */

/**
 * @var Zend_XmlRpc_Generator_Abstract
 */
require_once 'Zend/XmlRpc/Generator/Abstract.php';

/**
 * XML generator adapter based on XMLWriter
 */
class Zend_XmlRpc_Generator_XMLWriter extends Zend_XmlRpc_Generator_Abstract
{
    /**
     * XMLWriter instance
     *
     * @var XMLWriter
     */
    protected $_xmlWriter;

    /**
     * Initialized XMLWriter instance
     *
     * @return void
     */
    protected function _init()
    {
        $this->_xmlWriter = new XMLWriter();
        $this->_xmlWriter->openMemory();
        $this->_xmlWriter->startDocument('1.0', $this->_encoding);
    }

    /**
     * Start XML element
     *
     * Method opens a new XML element with an element name and an optional value
     *
     * @param string $name
     * @param string $value
     * @return Zend_XmlRpc_Generator_XMLWriter Fluent interface
     */
    public function startElement($name, $value = null)
    {
        $this->_xmlWriter->startElement($name);

        if ($value !== null) {
            $this->_xmlWriter->text($value);
        }

        return $this;
    }

    public function endElement($name)
    {
        $this->_xmlWriter->endElement();

        return $this;
    }

    public function saveXml()
    {
        return $this->_xmlWriter->flush(false);
    }
}