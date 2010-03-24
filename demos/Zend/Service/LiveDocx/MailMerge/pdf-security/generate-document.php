<?php

require_once dirname(__FILE__) . '/../../common.php';


$mailMerge = new Zend_Service_LiveDocx_MailMerge();

// Set WSDL of your premium service server
$mailMerge->setWsdl('https://api.example.com/1.2/mailmerge.asmx?WSDL');

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

$mailMerge->setLocalTemplate('template.docx');

$mailMerge->assign('software', 'Magic Graphical Compression Suite v1.9')
          ->assign('licensee', 'Henry Döner-Meyer')
          ->assign('company',  'Co-Operation')
          ->assign('date',     Zend_Date::now()->toString(Zend_Date::DATE_LONG))
          ->assign('time',     Zend_Date::now()->toString(Zend_Date::TIME_LONG))
          ->assign('city',     'Berlin')
          ->assign('country',  'Germany');

// Available on premium service only 
$mailMerge->setDocumentPassword('aaaaaaaaaa');

// Available on premium service only
$mailMerge->setDocumentAccessPermissions(
    array(
        'AllowHighLevelPrinting' ,  // getDocumentAccessOptions() returns
        'AllowExtractContents'      // array of permitted values
    ),   
    'myDocumentAccessPassword'
);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

unset($mailMerge);
