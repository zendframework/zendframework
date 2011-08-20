<?php

// Set used namespaces
use \Zend\Loader\StandardAutoloader;
use \Zend\Locale\Locale;
use \Zend\Service\LiveDocx\Helper;

// Turn up error reporting
error_reporting(E_ALL | E_STRICT);

// Library base
$base = dirname(dirname(dirname(dirname(__DIR__))));

// Set up autoloader
require_once "{$base}/library/Zend/Loader/StandardAutoloader.php";
$loader = new StandardAutoloader();
$loader->registerNamespace('Zend', "{$base}/library/Zend");
$loader->register();

// Include utility class
require_once "{$base}/demos/Zend/Service/LiveDocx/library/Zend/Service/LiveDocx/Helper.php";

// Set fallback locale
Locale::setFallback(Helper::LOCALE);

// Ensure LiveDocx credentials are available
if (false === Helper::credentialsAvailable()) {
    Helper::printLine(Helper::credentialsHowTo());
    exit();
}

unset($base);