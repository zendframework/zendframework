<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Date\Date;
use Zend\Service\LiveDocx\MailMerge;

$mailMerge = new MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

/**
 * Image Source:
 * iStock_000003413016Medium_business-man-with-hands-up.jpg
 */
$photoFilename = 'dailemaitre.jpg';

if (!$mailMerge->imageExists($photoFilename)) {
    $mailMerge->uploadImage($photoFilename);
}

$mailMerge->setLocalTemplate('template.docx');

$mailMerge->assign('name',        'Daï Lemaitre')
          ->assign('company',     'Megasoft Co-operation')
          ->assign('date',        Date::now()->toString(Date::DATE_LONG))
          ->assign('image:photo', $photoFilename);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

$mailMerge->deleteImage($photoFilename);

unset($mailMerge);