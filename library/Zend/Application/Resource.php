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
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Application;

/**
 * Interface for bootstrap resources
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Resource
{
    /**
     * Constructor
     *
     * Must take an optional single argument, $options.
     *
     * @param  mixed $options
     * @return void
     */
    public function __construct($options = null);

    /**
     * Set the bootstrap to which the resource is attached
     *
     * @param  \Zend\Application\Bootstrapper $bootstrap
     * @return \Zend\Application\Resource
     */
    public function setBootstrap(Bootstrapper $bootstrap);

    /**
     * Retrieve the bootstrap to which the resource is attached
     *
     * @return \Zend\Application\Bootstrapper
     */
    public function getBootstrap();

    /**
     * Set resource options
     *
     * @param  array $options
     * @return \Zend\Application\Resource
     */
    public function setOptions(array $options);

    /**
     * Retrieve resource options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Strategy pattern: initialize resource
     *
     * @return mixed
     */
    public function init();
}
