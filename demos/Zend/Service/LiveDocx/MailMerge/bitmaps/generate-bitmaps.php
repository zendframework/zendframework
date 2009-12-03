#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$date = new Zend_Date();

$date->setLocale(Demos_Zend_Service_LiveDocx_Helper::LOCALE);

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME);
$phpLiveDocx->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

$phpLiveDocx->setLocalTemplate('template.docx');

$phpLiveDocx->assign('software', 'Magic Graphical Compression Suite v1.9');
$phpLiveDocx->assign('licensee', 'Daï Lemaitre');
$phpLiveDocx->assign('company',  'Megasoft Co-operation');
$phpLiveDocx->assign('date',     $date->get(Zend_Date::DATE_LONG));
$phpLiveDocx->assign('time',     $date->get(Zend_Date::TIME_LONG));
$phpLiveDocx->assign('city',     'Lyon');
$phpLiveDocx->assign('country',  'France');

$phpLiveDocx->createDocument();

// Get all bitmaps
$bitmaps = $phpLiveDocx->getAllBitmaps(100, 'png');      // zoomFactor, format

// Get just bitmaps in specified range
//$bitmaps = $phpLiveDocx->getBitmaps(2, 2, 100, 'png');   // fromPage, toPage, zoomFactor, format

foreach ($bitmaps as $pageNumber => $bitmapData) {
    $filename = sprintf('documentPage%d.png', $pageNumber);
    file_put_contents($filename, $bitmapData);
}

unset($phpLiveDocx);
