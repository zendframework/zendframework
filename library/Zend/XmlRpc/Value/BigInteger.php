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
 * @uses       \Zend\Crypt\Math\BigInteger
 * @uses       \Zend\XmlRpc\Value\Integer
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BigInteger extends Integer
{
    /**
     * @var \Zend\Crypt\Math\BigInteger
     */
    protected $_integer;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->_integer = new \Zend\Crypt\Math\BigInteger();
        $this->_value   = $this->_integer->init($this->_value);
        $this->_type    = self::XMLRPC_TYPE_I8;
    }

    /**
     * Return bigint value object
     *
     * @return \Zend\Crypt\Math\BigInteger
     */
    public function getValue()
    {
        return $this->_integer;
    }
}
