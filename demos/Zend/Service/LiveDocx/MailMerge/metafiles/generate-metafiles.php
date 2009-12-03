#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$date = new Zend_Date();

$date->setLocale(Demos_Zend_Service_LiveDocx_Helper::LOCALE);

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME);
$phpLiveDocx->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

$phpLiveDocx->setFieldValue('software', 'Magic Graphical Compression Suite v1.9');
$phpLiveDocx->setFieldValue('licensee', 'Henry Döner-Meyer');
$phpLiveDocx->setFieldValue('company',  'Megasoft Co-operation');
$phpLiveDocx->setFieldValue('date',     $date->get(Zend_Date::DATE_LONG));
$phpLiveDocx->setFieldValue('time',     $date->get(Zend_Date::TIME_LONG));
$phpLiveDocx->setFieldValue('city',     'Bremen');
$phpLiveDocx->setFieldValue('country',  'Germany');

$phpLiveDocx->createDocument();

// Get all metafiles
$metaFiles = $phpLiveDocx->getAllMetafiles();

// Get just metafiles in specified range
//$metaFiles = $phpLiveDocx->getMetafiles(1, 2);    // fromPage, toPage

foreach ($metaFiles as $pageNumber => $metaFileData) {
    $filename = sprintf('documentPage%d.wmf', $pageNumber);
    file_put_contents($filename, $metaFileData);
}

unset($phpLiveDocx);