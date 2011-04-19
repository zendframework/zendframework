<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Service\LiveDocx\Helper;
use Zend\Service\LiveDocx\MailMerge;

Helper::printLine(
    PHP_EOL . 'Template, Document and Image Formats' .
    PHP_EOL . 
    PHP_EOL . 'The following formats are supported by LiveDocx:' .
    PHP_EOL .
    PHP_EOL
);

$mailMerge = new MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

printf("Supported TEMPLATE file formats (input)  : %s%s",
    Helper::arrayDecorator($mailMerge->getTemplateFormats()), PHP_EOL);

printf("Supported DOCUMENT file formats (output) : %s%s",
    Helper::arrayDecorator($mailMerge->getDocumentFormats()), PHP_EOL);

printf("Supported IMAGE file formats (output)    : %s%s",
    Helper::arrayDecorator($mailMerge->getImageExportFormats()), PHP_EOL);

print PHP_EOL;
    
unset($mailMerge);
