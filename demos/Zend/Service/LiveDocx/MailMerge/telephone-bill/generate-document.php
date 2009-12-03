#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$date = new Zend_Date();

$date->setLocale(Demos_Zend_Service_LiveDocx_Helper::LOCALE);

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME);
$phpLiveDocx->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

/*
 * ALTERNATIVE: Specify username and password in constructor
 */
/*
$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge(
    array (
        'username' => Demos_Zend_Service_LiveDocx_Helper::USERNAME,
        'password' => Demos_Zend_Service_LiveDocx_Helper::PASSWORD
    )
);
*/

$phpLiveDocx->setLocalTemplate('template.doc');


$phpLiveDocx->assign('customer_number', sprintf("#%'10s",  rand(0,1000000000)));
$phpLiveDocx->assign('invoice_number',  sprintf("#%'10s",  rand(0,1000000000)));
$phpLiveDocx->assign('account_number',  sprintf("#%'10s",  rand(0,1000000000)));


$billData = array (  
    'phone'         => '+22 (0)333 444 555',
    'date'          => $date->get(Zend_Date::DATE_LONG),
    'name'          => 'James Henry Brown',
    'service_phone' => '+22 (0)333 444 559',
    'service_fax'   => '+22 (0)333 444 558',
    'month'         => sprintf('%s %s', $date->get(Zend_Date::MONTH_NAME), $date->get(Zend_Date::YEAR)),
    'monthly_fee'   =>  '15.00',
    'total_net'     =>  '19.60',
    'tax'           =>  '19.00',
    'tax_value'     =>   '3.72',
    'total'         =>  '23.32'
);

$phpLiveDocx->assign($billData);


$billConnections = array(
    array(
        'connection_number'   => '+11 (0)222 333 441',
        'connection_duration' => '00:01:01',
        'fee'                 => '1.15'
    ),
    array(
        'connection_number'   => '+11 (0)222 333 442',
        'connection_duration' => '00:01:02',
        'fee'                 => '1.15'
    ),
    array(
        'connection_number'   => '+11 (0)222 333 443', 
        'connection_duration' => '00:01:03', 
        'fee'                 => '1.15'
    ),
    array(
        'connection_number'   => '+11 (0)222 333 444',
        'connection_duration' => '00:01:04',
        'fee'                 => '1.15'
    )
);

$phpLiveDocx->assign('connection', $billConnections);


$documentProperties = array (
    'title'    => sprintf('Telephone Invoice (%s)', $billData['name']),
    'author'   => 'TIS Telecom', 
    'subject'  => sprintf('Your telephone invoice for %s', $billData['month']),
    'keywords' => sprintf('Telephone, Payment, Invoice, %s', $billData['month'])
);

$phpLiveDocx->setDocumentProperties($documentProperties);


$phpLiveDocx->createDocument();

$document = $phpLiveDocx->retrieveDocument('pdf');

unset($phpLiveDocx);

file_put_contents('document.pdf', $document);
