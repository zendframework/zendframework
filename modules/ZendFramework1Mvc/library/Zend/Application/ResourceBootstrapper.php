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
 * @package    Zend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application;

use Zend\Loader\LazyLoadingBroker;

/**
 * Interface for bootstrap classes that utilize resource plugins
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ResourceBootstrapper
{
    /**
     * Set plugin broker to use to fetch resources
     *
     * @param  \Zend\Loader\LazyLoadingBroker $broker
     * @return \Zend\Application\ResourceBootstrapper
     */
    public function setBroker($broker);

    /**
     * Retrieve plugin broker for resources
     *
     * @return \Zend\Loader\LazyLoadingBroker
     */
    public function getBroker();
}
