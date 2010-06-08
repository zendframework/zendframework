<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

$cacheId = md5(__FILE__);

$cacheFrontendOptions = array(
    'lifetime' => 2592000, // 30 days
    'automatic_serialization' => true
);

$cacheBackendOptions = array(
    'cache_dir' => dirname(__FILE__) . '/cache'
);

$cache = Zend_Cache::factory('Core', 'File', $cacheFrontendOptions, $cacheBackendOptions);

if (! $fonts = $cache->load($cacheId)) {
    
    // Cache miss. Connect to backend service (expensive).
    
    $mailMerge = new Zend_Service_LiveDocx_MailMerge();
    
    $mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
              ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);
    
    $fonts = $mailMerge->getFontNames();
    
    $cache->save($fonts, $cacheId);
    
    unset($mailMerge);
    
} else {
    
    // Cache hit. Continue.
    
}

unset($cache);

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Supported Fonts' .
    PHP_EOL . 
    PHP_EOL . 'The following fonts are installed on the backend server and may be used in templates. Fonts used in templates, which are NOT listed below, will be substituted. If you would like to use a font, which is not installed on the backend server, please contact your LiveDocx provider.' .
    PHP_EOL . 
    PHP_EOL . '(Note this method call is cached for maximum performance. The supported formats change very infrequently, hence, they are good candidates to be cached.)' .
    PHP_EOL . 
    PHP_EOL . Demos_Zend_Service_LiveDocx_Helper::arrayDecorator($fonts) . 
    PHP_EOL . 
    PHP_EOL)
);

print(PHP_EOL);

unset($mailMerge);
