<?php

require_once __DIR__ . '/common.php';


use Zend\Service\LiveDocx\MailMerge;

// -----------------------------------------------------------------------------

$mailMerge = new MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

// -----------------------------------------------------------------------------

foreach ($templateFilesnames as $_templateFilesname) {
    
    if ($mailMerge->templateExists($_templateFilesname)) {
        $mailMerge->deleteTemplate($_templateFilesname);
    }
    
    $mailMerge->uploadTemplate($_templateFilesname);
}

// -----------------------------------------------------------------------------

$mailMerge->setIgnoreSubTemplates(true);            // <-- Does NOT include any sub-templates.

$mailMerge->setRemoteTemplate($templateFilename);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document1.pdf', $document);

// -----------------------------------------------------------------------------

$mailMerge->setIgnoreSubTemplates(false);           // <-- Includes all sub-templates.
                                                    //     Default, when setIgnoreSubTemplates is not called.
$mailMerge->setRemoteTemplate($templateFilename);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document2.pdf', $document);

// -----------------------------------------------------------------------------

unset($mailMerge);