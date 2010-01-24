#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
            ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

$phpLiveDocx->setLocalTemplate('template.docx');
            
$phpLiveDocx->setFieldValue('software', 'Magic Graphical Compression Suite v1.9')
            ->setFieldValue('licensee', 'Henry Döner-Meyer')
            ->setFieldValue('company',  'Megasoft Co-operation')
            ->setFieldValue('date',     Zend_Date::now()->toString(Zend_Date::DATE_LONG))
            ->setFieldValue('time',     Zend_Date::now()->toString(Zend_Date::TIME_LONG))
            ->setFieldValue('city',     'Bremen')
            ->setFieldValue('country',  'Germany');

$phpLiveDocx->createDocument();

// Get all metafiles
$metaFiles = $phpLiveDocx->getAllMetafiles();

// Get just metafiles in specified range
//$metaFiles = $phpLiveDocx->getMetafiles(1, 2);    // fromPage, toPage

foreach ($metaFiles as $pageNumber => $metaFileData) {
    $filename = sprintf('document-page-%d.wmf', $pageNumber);
    file_put_contents($filename, $metaFileData);
}

unset($phpLiveDocx);