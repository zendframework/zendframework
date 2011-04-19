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
 * @uses       \Zend\XmlRpc\Value\Scalar
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Nil extends Scalar
{

    /**
     * Set the value of a nil native type
     *
     */
    public function __construct()
    {
        $this->_type = self::XMLRPC_TYPE_NIL;
        $this->_value = null;
    }

    /**
     * Return the value of this object, convert the XML-RPC native nill value into a PHP NULL
     *
     * @return null
     */
    public function getValue()
    {
        return null;
    }
}

