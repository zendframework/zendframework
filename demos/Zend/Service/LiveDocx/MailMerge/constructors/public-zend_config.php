<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Config\Factory as ConfigFactory;
use Zend\Service\LiveDocx\Helper;
use Zend\Service\LiveDocx\MailMerge;

Helper::printLine(
    PHP_EOL . 'Using the Public LiveDocx Service with \Zend\Config\Config' .
    PHP_EOL . 
    PHP_EOL . 'This sample application illustrates how to use the Zend Framework LiveDocx component with a \Zend\Config\Config object. This is useful, for example, to store connection credentials in an external ini file.' .
    PHP_EOL .
    PHP_EOL
);

$options = ConfigFactory::fromFile('credentials.ini', true);

$mailMerge = new MailMerge($options);

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
