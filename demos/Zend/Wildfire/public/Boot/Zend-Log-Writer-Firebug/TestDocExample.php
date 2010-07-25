<?php

require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();

switch($_GET['Example']) {

    case 'WithController':

        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        Zend_Registry::set('logger',$logger);

        $controller = Zend_Controller_Front::getInstance();
        $controller->setParam('useDefaultControllerAlways',true);
        $controller->setParam('noViewRenderer', true);
        $controller->setControllerDirectory(dirname(dirname(dirname(__DIR__))).'/application/controllers/Boot/Zend-Log-Writer-Firebug');
        $controller->dispatch();

        print 'Test Doc Example with Controller';
        break;

    case 'WithoutController':

        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);

        $request = new Zend_Controller_Request_Http();
        $response = new Zend_Controller_Response_Http();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $channel->setRequest($request);
        $channel->setResponse($response);

        $logger->log('This is a log message!', Zend_Log::INFO);

        $channel->flush();
        $response->sendHeaders();

        print 'Test Doc Example without Controller';
        break;
}
