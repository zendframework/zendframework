<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Service\LiveDocx\Helper;
use Zend\Service\LiveDocx\MailMerge;

Helper::printLine(
    PHP_EOL . 'Using Hosted Solution and Fully-Licensed Servers' .
    PHP_EOL .
    PHP_EOL . 'This sample application illustrates how to use the Zend Framework LiveDocx component with hosted and fully-licensed LiveDocx servers, by specifying the server\'s WSDL.' .
    PHP_EOL .
    PHP_EOL
);

$mailMerge = new MailMerge();

$mailMerge->setWsdl(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL)
          ->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_PASSWORD);

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

unset($mailMerge);
