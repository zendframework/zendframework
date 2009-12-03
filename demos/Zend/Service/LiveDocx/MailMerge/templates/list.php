#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Remotely Stored Templates' .
    PHP_EOL . 
    PHP_EOL . 'The following templates are currently stored on the LiveDocx server:' .
    PHP_EOL .
    PHP_EOL)
);

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME)
            ->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

print(Demos_Zend_Service_LiveDocx_Helper::listDecorator($phpLiveDocx->listTemplates()));

unset($phpLiveDocx);