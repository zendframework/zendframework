#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Template, Document and Image Formats' .
    PHP_EOL . 
    PHP_EOL . 'The following formats are supported by LiveDocx:' .
    PHP_EOL .
    PHP_EOL . '(Note these method calls are cached for maximum performance. The supported formats change very infrequently, hence, they are good candidates to be cached.)' .
    PHP_EOL .
    PHP_EOL)
);

$cacheId = md5(__FILE__);

$cacheFrontendOptions = array(
    'lifetime' => 2592000, // 30 days
    'automatic_serialization' => true
);

$cacheBackendOptions = array(
    'cache_dir' => dirname(__FILE__) . '/cache'
);

$cache = Zend_Cache::factory('Core', 'File', $cacheFrontendOptions, $cacheBackendOptions);

if (! $formats = $cache->load($cacheId)) {
    
    // Cache miss. Connect to backend service (expensive).
    
    $phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();
    
    $phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME)
                ->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);
    
    $formats = new StdClass();
    
    $formats->template = $phpLiveDocx->getTemplateFormats();
    $formats->document = $phpLiveDocx->getDocumentFormats();
    $formats->image    = $phpLiveDocx->getImageFormats();
    
    $cache->save($formats, $cacheId);
    
    unset($phpLiveDocx);
    
} else {
    
    // Cache hit. Continue.
    
}

unset($cache);

printf("Supported TEMPLATE file formats (input)  : %s%s",
    Demos_Zend_Service_LiveDocx_Helper::arrayDecorator($formats->template), PHP_EOL);

printf("Supported DOCUMENT file formats (output) : %s%s",
    Demos_Zend_Service_LiveDocx_Helper::arrayDecorator($formats->document), PHP_EOL);

printf("Supported IMAGE file formats (output)    : %s%s",
    Demos_Zend_Service_LiveDocx_Helper::arrayDecorator($formats->image), PHP_EOL);

print(PHP_EOL);