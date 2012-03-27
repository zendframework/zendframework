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
 * @package    Zend_Mvc
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc;

/**
 * Interface for bootstraps
 * 
 * @package   Zend_Mvc
 * @copyright Copyright (C) 2005-2011, Zend Technologies, Inc.
 * @license   New BSD {@link http://framework.zend.com/license}
 */
interface Bootstrapper
{
    /**
     * Bootstrap an application
     * 
     * @param  AppContext $application 
     * @return void
     */
    public function bootstrap(AppContext $application);
}
