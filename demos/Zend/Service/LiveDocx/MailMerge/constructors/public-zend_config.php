<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Using the Public LiveDocx Service with Zend_Config' .
    PHP_EOL . 
    PHP_EOL . 'This sample application illustrates how to use Zend_Service_LiveDocx_MailMerge with a Zend_Config object. This is useful, for example, to store connection credentials in an external ini file.' .
    PHP_EOL .
    PHP_EOL)
);

$options = new Zend_Config_Ini('credentials.ini');

$mailMerge = new Zend_Service_LiveDocx_MailMerge($options);

$mailMerge->getTemplateFormats(); // then call methods as usual

printf('Username : %s%sPassword : %s%s    WSDL : %s%s%s',
    $mailMerge->getUsername(),
    PHP_EOL,
    $mailMerge->getPassword(),
    PHP_EOL,
    $mailMerge->getWsdl(),
    PHP_EOL,
    PHP_EOL
);

unset($mailMerge, $options);