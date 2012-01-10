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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Test\PHPUnit\_files;

$router     = new \Zend\Controller\Router\Rewrite();
$dispatcher = new \Zend\Controller\Dispatcher\Standard();
$plugin     = new \Zend\Controller\Plugin\ErrorHandler();
$controller = \Zend\Controller\Front::getInstance();
$controller->setParam('foo', 'bar')
           ->registerPlugin($plugin)
           ->setRouter($router)
           ->setDispatcher($dispatcher);
$broker       = $controller->getHelperBroker();
$viewRenderer = $broker->load('ViewRenderer');
\Zend\Registry::set('router', $router);
\Zend\Registry::set('dispatcher', $dispatcher);
\Zend\Registry::set('plugin', $plugin);
\Zend\Registry::set('viewRenderer', $viewRenderer);

