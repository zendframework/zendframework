#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

// Set WSDL of your premium service server
$phpLiveDocx->setWsdl('https://api.example.com/1.2/mailmerge.asmx?WSDL');

$phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME)
            ->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

$phpLiveDocx->setLocalTemplate('template.docx');

$phpLiveDocx->assign('software', 'Magic Graphical Compression Suite v1.9')
            ->assign('licensee', 'Henry Döner-Meyer')
            ->assign('company',  'Co-Operation')
            ->assign('date',     Zend_Date::now()->toString(Zend_Date::DATE_LONG))
            ->assign('time',     Zend_Date::now()->toString(Zend_Date::TIME_LONG))
            ->assign('city',     'Berlin')
            ->assign('country',  'Germany');

// Available on premium service only 
$phpLiveDocx->setDocumentPassword('aaaaaaaaaa');

// Available on premium service only
$phpLiveDocx->setDocumentAccessPermissions(
    array (
        Zend_Service_LiveDocx_MailMerge::DOCUMENT_ACCESS_PERMISSION_ALLOW_PRINTING_HIGH_LEVEL,
        Zend_Service_LiveDocx_MailMerge::DOCUMENT_ACCESS_PERMISSION_ALLOW_EXTRACT_CONTENTS
    ),
    'myDocumentAccessPassword'
);

$phpLiveDocx->createDocument();

$document = $phpLiveDocx->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

unset($phpLiveDocx);
