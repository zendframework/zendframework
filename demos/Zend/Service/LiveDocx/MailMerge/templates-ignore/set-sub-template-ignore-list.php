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

$mailMerge->setWsdl(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL)
          ->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_PASSWORD);

$mailMerge->uploadTemplate('maintemplate.docx');
$mailMerge->uploadTemplate('subtemplate1.docx');
$mailMerge->uploadTemplate('subtemplate2.docx');

$mailMerge->setRemoteTemplate('maintemplate.docx');

$mailMerge->setSubTemplateIgnoreList(array('subtemplate1.docx', 'subtemplate2.docx'));

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

unset($mailMerge);
