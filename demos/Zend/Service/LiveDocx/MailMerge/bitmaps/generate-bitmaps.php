<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Date\Date;
use Zend\Service\LiveDocx\MailMerge;

$mailMerge = new MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

$mailMerge->setLocalTemplate('template.docx');

$mailMerge->assign('software', 'Magic Graphical Compression Suite v1.9')
          ->assign('licensee', 'Daï Lemaitre')
          ->assign('company',  'Megasoft Co-operation')
          ->assign('date',     Date::now()->toString(Date::DATE_LONG))
          ->assign('time',     Date::now()->toString(Date::TIME_LONG))
          ->assign('city',     'Lyon')
          ->assign('country',  'France');

$mailMerge->createDocument();

// Get all bitmaps
$bitmaps = $mailMerge->getAllBitmaps(100, 'png');      // zoomFactor, format

// Get just bitmaps in specified range
//$bitmaps = $mailMerge->getBitmaps(2, 2, 100, 'png');   // fromPage, toPage, zoomFactor, format

foreach ($bitmaps as $pageNumber => $bitmapData) {
    $filename = sprintf('document-page-%d.png', $pageNumber);
    file_put_contents($filename, $bitmapData);
}

unset($mailMerge);
