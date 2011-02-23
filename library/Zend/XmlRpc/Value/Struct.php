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
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\XmlRpc\Value;

/**
 * @uses       \Zend\XmlRpc\Value\Collection
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Struct extends Collection
{
    /**
     * Set the value of an struct native type
     *
     * @param array $value
     */
    public function __construct($value)
    {
        $this->_type = self::XMLRPC_TYPE_STRUCT;
        parent::__construct($value);
    }


    /**
     * Generate the XML code that represent struct native MXL-RPC value
     *
     * @return void
     */
    protected function _generateXML()
    {
        $generator = $this->getGenerator();
        $generator->openElement('value')
                  ->openElement('struct');

        if (is_array($this->_value)) {
            foreach ($this->_value as $name => $val) {
                $generator->openElement('member')
                          ->openElement('name', $name)
                          ->closeElement('name');
                $val->generateXml();
                $generator->closeElement('member');
            }
        }
        $generator->closeElement('struct')
                  ->closeElement('value');
    }
}
