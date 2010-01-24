#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Uploading Locally Stored Templates to Server' .
    PHP_EOL .
    PHP_EOL)
);

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
            ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

print('Uploading template... ');
$phpLiveDocx->uploadTemplate('template-1.docx');
print('DONE.' . PHP_EOL);

print('Uploading template... ');
$phpLiveDocx->uploadTemplate('template-2.docx');
print('DONE.' . PHP_EOL);

print(PHP_EOL);

unset($phpLiveDocx);