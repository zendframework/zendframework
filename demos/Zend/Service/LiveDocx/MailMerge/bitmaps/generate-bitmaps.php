#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME)
            ->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

$phpLiveDocx->setLocalTemplate('template.docx');

$phpLiveDocx->assign('software', 'Magic Graphical Compression Suite v1.9')
            ->assign('licensee', 'Daï Lemaitre')
            ->assign('company',  'Megasoft Co-operation')
            ->assign('date',     Zend_Date::now()->toString(Zend_Date::DATE_LONG))
            ->assign('time',     Zend_Date::now()->toString(Zend_Date::TIME_LONG))
            ->assign('city',     'Lyon')
            ->assign('country',  'France');

$phpLiveDocx->createDocument();

// Get all bitmaps
$bitmaps = $phpLiveDocx->getAllBitmaps(100, 'png');      // zoomFactor, format

// Get just bitmaps in specified range
//$bitmaps = $phpLiveDocx->getBitmaps(2, 2, 100, 'png');   // fromPage, toPage, zoomFactor, format

foreach ($bitmaps as $pageNumber => $bitmapData) {
    $filename = sprintf('document-page-%d.png', $pageNumber);
    file_put_contents($filename, $bitmapData);
}

unset($phpLiveDocx);
