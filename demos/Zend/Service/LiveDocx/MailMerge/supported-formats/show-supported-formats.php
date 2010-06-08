<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Template, Document and Image Formats' .
    PHP_EOL . 
    PHP_EOL . 'The following formats are supported by LiveDocx:' .
    PHP_EOL .
    PHP_EOL)
);

$mailMerge = new Zend_Service_LiveDocx_MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

printf("Supported TEMPLATE file formats (input)  : %s%s",
    Demos_Zend_Service_LiveDocx_Helper::arrayDecorator($mailMerge->getTemplateFormats()), PHP_EOL);

printf("Supported DOCUMENT file formats (output) : %s%s",
    Demos_Zend_Service_LiveDocx_Helper::arrayDecorator($mailMerge->getDocumentFormats()), PHP_EOL);

printf("Supported IMAGE file formats (output)    : %s%s",
    Demos_Zend_Service_LiveDocx_Helper::arrayDecorator($mailMerge->getImageFormats()), PHP_EOL);

print PHP_EOL;
    
unset($mailMerge);