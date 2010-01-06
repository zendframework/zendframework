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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Dispatcher/Standard.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Plugin/ErrorHandler.php';
require_once 'Zend/Controller/Router/Rewrite.php';
require_once 'Zend/Registry.php';
$router     = new Zend_Controller_Router_Rewrite();
$dispatcher = new Zend_Controller_Dispatcher_Standard();
$plugin     = new Zend_Controller_Plugin_ErrorHandler();
$controller = Zend_Controller_Front::getInstance();
$controller->setParam('foo', 'bar')
           ->registerPlugin($plugin)
           ->setRouter($router)
           ->setDispatcher($dispatcher);
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
Zend_Registry::set('router', $router);
Zend_Registry::set('dispatcher', $dispatcher);
Zend_Registry::set('plugin', $plugin);
Zend_Registry::set('viewRenderer', $viewRenderer);

