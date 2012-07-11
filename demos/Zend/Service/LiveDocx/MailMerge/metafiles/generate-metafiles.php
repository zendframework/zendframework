<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

use DateTime;
use Zend\Service\LiveDocx\MailMerge;

$mailMerge = new MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

$mailMerge->setLocalTemplate('template.docx');

$date = new DateTime();

$mailMerge->setFieldValue('software', 'Magic Graphical Compression Suite v1.9')
          ->setFieldValue('licensee', 'Henry Döner-Meyer')
          ->setFieldValue('company',  'Megasoft Co-operation')
          ->setFieldValue('date',     $date->format('Y-m-d'))
          ->setFieldValue('time',     $date->format('H:i:s'))
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
