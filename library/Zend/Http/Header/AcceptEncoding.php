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
 * @package    Zend\Http\Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace Zend\Http\Header;

/**
 * Accept Encoding Header
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3
 */
class AcceptEncoding extends AbstractAccept
{

    protected $regexAddType = '#^([a-zA-Z0-9+-]+|\*)$#';
    
    /**
     * Get field name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Accept-Encoding';
    }

    /**
     * Cast to string
     * 
     * @return string
     */
    public function toString()
    {
        return 'Accept-Encoding: ' . $this->getFieldValue();
    }
    
    /**
     * Add an encoding, with the given priority
     * 
     * @param  string $type 
     * @param  int|float $priority 
     * @return Accept
     */
    public function addEncoding($type, $priority = 1)
    {
        return $this->addType($type, $priority);
    }
    
    /**
     * Does the header have the requested encoding?
     * 
     * @param  string $type 
     * @return bool
     */
    public function hasEncoding($type)
    {
        return $this->hasType($type);
    }
}
