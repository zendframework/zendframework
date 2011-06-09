<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


/**
 * The following methods on the backend server are currently in active development:
 *
 * - SetIgnoreSubTemplates
 * - SetSubTemplateIgnoreList
 *
 * They may not be used at the moment.
 */

use Zend\Service\LiveDocx\MailMerge;


$mailMerge = new MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

$mailMerge->uploadTemplate('maintemplate.docx');
$mailMerge->uploadTemplate('subtemplate1.docx');
$mailMerge->uploadTemplate('subtemplate2.docx');

$mailMerge->setRemoteTemplate('maintemplate.docx');

$mailMerge->setIgnoreSubTemplates(true);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

unset($mailMerge);
