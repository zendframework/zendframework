<?php

require_once __DIR__ . '/common.php';


use Zend\Service\LiveDocx\MailMerge;

// -----------------------------------------------------------------------------

$mailMerge = new MailMerge();

$mailMerge->setWsdl(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL)
          ->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_PASSWORD);

// -----------------------------------------------------------------------------

foreach ($templateFilesnames as $_templateFilesname) {

    if ($mailMerge->templateExists($_templateFilesname)) {
        $mailMerge->deleteTemplate($_templateFilesname);
    }

    $mailMerge->uploadTemplate($_templateFilesname);
}

// -----------------------------------------------------------------------------

$mailMerge->setSubTemplateIgnoreList(array($subTemplate1Filename, $subTemplate2Filename));

$mailMerge->setRemoteTemplate($templateFilename);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document1.pdf', $document);

// -----------------------------------------------------------------------------

$mailMerge->setSubTemplateIgnoreList(array($subTemplate1Filename));

$mailMerge->setRemoteTemplate($templateFilename);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document2.pdf', $document);

// -----------------------------------------------------------------------------

$mailMerge->setSubTemplateIgnoreList(array($subTemplate2Filename));

$mailMerge->setRemoteTemplate($templateFilename);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document3.pdf', $document);

// -----------------------------------------------------------------------------

unset($mailMerge);