<?php

require_once dirname(__FILE__) . '/../../common.php';


$mailMerge = new Zend_Service_LiveDocx_MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Supported Fonts' .
    PHP_EOL . 
    PHP_EOL . 'The following fonts are installed on the backend server and may be used in templates. Fonts used in templates, which are NOT listed below, will be substituted. If you would like to use a font, which is not installed on the backend server, please contact your LiveDocx provider.' .
    PHP_EOL . 
    PHP_EOL . Demos_Zend_Service_LiveDocx_Helper::arrayDecorator($mailMerge->getFontNames()) . 
    PHP_EOL . 
    PHP_EOL)
);

unset($mailMerge);