<?php

require_once dirname(__FILE__) . '/../../common.php';


$mailMerge = new Zend_Service_LiveDocx_MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

$mailMerge->setLocalTemplate('template.docx');
            
$mailMerge->setFieldValue('software', 'Magic Graphical Compression Suite v1.9')
          ->setFieldValue('licensee', 'Henry Döner-Meyer')
          ->setFieldValue('company',  'Megasoft Co-operation')
          ->setFieldValue('date',     Zend_Date::now()->toString(Zend_Date::DATE_LONG))
          ->setFieldValue('time',     Zend_Date::now()->toString(Zend_Date::TIME_LONG))
          ->setFieldValue('city',     'Bremen')
          ->setFieldValue('country',  'Germany');

$mailMerge->createDocument();

// Get all metafiles
$metaFiles = $mailMerge->getAllMetafiles();

// Get just metafiles in specified range
//$metaFiles = $mailMerge->getMetafiles(1, 2);    // fromPage, toPage

foreach ($metaFiles as $pageNumber => $metaFileData) {
    $filename = sprintf('document-page-%d.wmf', $pageNumber);
    file_put_contents($filename, $metaFileData);
}

unset($mailMerge);