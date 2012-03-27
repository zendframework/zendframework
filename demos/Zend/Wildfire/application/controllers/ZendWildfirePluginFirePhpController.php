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
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Tests for Zend_Wildfire_Plugin_FirePhp
 *
 * @category   Zend
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendWildfirePluginFirePhpController extends Zend_Controller_Action
{
    public function testgroupsAction()
    {
        Zend_Wildfire_Plugin_FirePhp::group('Group 1');
        Zend_Wildfire_Plugin_FirePhp::send('Test Message 1');
        Zend_Wildfire_Plugin_FirePhp::group('Group 2');
        Zend_Wildfire_Plugin_FirePhp::send('Test Message 2', 'Label', Zend_Wildfire_Plugin_FirePhp::INFO);
        Zend_Wildfire_Plugin_FirePhp::groupEnd();
        Zend_Wildfire_Plugin_FirePhp::send('Test Message 3');
        Zend_Wildfire_Plugin_FirePhp::groupEnd();
    }
}
