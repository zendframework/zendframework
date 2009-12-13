#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Using Hosted Solution and Fully-Licensed Servers' .
    PHP_EOL . 
    PHP_EOL . 'This sample application illustrates how to use Zend_Service_LiveDocx_MailMerge with hosted and fully-licensed LiveDocx servers, by specifying an alternative SOAP client (must be instance of Zend_Soap_Client).' .
    PHP_EOL .
    PHP_EOL)
);


$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setWsdl('https://api.example.com/1.2/mailmerge.asmx?WSDL')
            ->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME)
            ->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

$phpLiveDocx->getTemplateFormats(); // then call methods as usual

printf('Username : %s%sPassword : %s%s    WSDL : %s%s%s',
    $phpLiveDocx->getUsername(),
    PHP_EOL,
    $phpLiveDocx->getPassword(),
    PHP_EOL,
    $phpLiveDocx->getWsdl(),
    PHP_EOL,
    PHP_EOL
);

unset($phpLiveDocx, $mySoapClient);