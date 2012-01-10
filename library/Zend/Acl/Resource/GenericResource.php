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
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Acl\Resource;

use Zend\Acl\Resource;

/**
 * @uses       \Zend\Acl\Resource\ResourceInterface
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GenericResource implements Resource
{
    /**
     * Unique id of Resource
     *
     * @var string
     */
    protected $_resourceId;

    /**
     * Sets the Resource identifier
     *
     * @param  string $resourceId
     * @return void
     */
    public function __construct($resourceId)
    {
        $this->_resourceId = (string) $resourceId;
    }

    /**
     * Defined by Zend\Acl\Resource; returns the Resource identifier
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->_resourceId;
    }

    /**
     * Defined by Zend\Acl\Resource; returns the Resource identifier
     * Proxies to getResourceId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getResourceId();
    }
}
