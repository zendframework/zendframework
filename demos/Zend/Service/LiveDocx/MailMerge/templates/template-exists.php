#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Checking For Remotely Stored Templates' .
    PHP_EOL .
    PHP_EOL)
);

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
            ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

print('Checking whether a template is available... ');
if (true === $phpLiveDocx->templateExists('template-1.docx')) {
    print('EXISTS. ');
} else {
    print('DOES NOT EXIST. ');
}
print('DONE' . PHP_EOL);

print(PHP_EOL);

unset($phpLiveDocx);