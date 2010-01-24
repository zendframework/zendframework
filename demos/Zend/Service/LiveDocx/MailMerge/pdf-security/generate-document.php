<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

// Set WSDL of your premium service server
$phpLiveDocx->setWsdl('https://api.example.com/1.2/mailmerge.asmx?WSDL');

$phpLiveDocx->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
            ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

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
    array(
        'AllowHighLevelPrinting' ,  // getDocumentAccessOptions() returns
        'AllowExtractContents'      // array of permitted values
    ),   
    'myDocumentAccessPassword'
);

$phpLiveDocx->createDocument();

$document = $phpLiveDocx->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

unset($phpLiveDocx);
