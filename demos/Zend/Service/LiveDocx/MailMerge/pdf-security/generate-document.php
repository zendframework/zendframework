<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Date\Date;
use Zend\Service\LiveDocx\MailMerge;

$mailMerge = new MailMerge();

// Set WSDL of your *premium* service server
$mailMerge->setWsdl(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL);

// Set username and password of your *premium* service server
$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_PASSWORD);

$mailMerge->setLocalTemplate('template.docx');

$mailMerge->assign('software', 'Magic Graphical Compression Suite v1.9')
          ->assign('licensee', 'Henry Döner-Meyer')
          ->assign('company',  'Co-Operation')
          ->assign('date',     Date::now()->toString(Date::DATE_LONG))
          ->assign('time',     Date::now()->toString(Date::TIME_LONG))
          ->assign('city',     'Berlin')
          ->assign('country',  'Germany');

// Available on premium service only 
$mailMerge->setDocumentPassword('aaaaaaaaaa');

// Available on premium service only
$mailMerge->setDocumentAccessPermissions(
    array(
        'AllowHighLevelPrinting',  // getDocumentAccessOptions() returns
        'AllowExtractContents'     // array of permitted values
    ),   
    'myDocumentAccessPassword'
);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

unset($mailMerge);
