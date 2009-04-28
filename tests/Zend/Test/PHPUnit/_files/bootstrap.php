<?php
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

