<?php

require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $logger = Zend_Registry::get('logger');

        $logger->log('This is a log message!', Zend_Log::INFO);
    }
}
