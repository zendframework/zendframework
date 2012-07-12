<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Service\LiveDocx\MailMerge;
use Zend\Service\LiveDocx\Helper;

Helper::printLine(
    PHP_EOL . 'Document Access Options' .
    PHP_EOL .
    PHP_EOL . 'Documents can be protected using one or more document access option:' .
    PHP_EOL .
    PHP_EOL
);

$mailMerge = new MailMerge();

// Set WSDL of your *premium* service server
$mailMerge->setWsdl(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL);

// Set username and password of your *premium* service server
$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_PASSWORD);

Helper::printLine(
    implode(', ', $mailMerge->getDocumentAccessOptions()) . '.' .
    PHP_EOL .
    PHP_EOL
);

unset($mailMerge);