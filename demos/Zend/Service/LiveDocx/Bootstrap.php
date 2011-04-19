<?php

// Set used namespaces
use Zend\Locale\Locale;
use Zend\Registry;
use Zend\Service\LiveDocx\Helper;

// Turn up error reporting
error_reporting(E_ALL | E_STRICT);

// Set up autoloader
$base = dirname(dirname(dirname(dirname(__DIR__))));
require_once "{$base}/library/Zend/Loader/StandardAutoloader.php";
$loader = new \Zend\Loader\StandardAutoloader();
$loader->registerNamespace('Zend', "{$base}/library/Zend");
$loader->register();

// Include utility class
require_once "{$base}/demos/Zend/Service/LiveDocx/library/Zend/Service/LiveDocx/Helper.php";

// Set default locale
Locale::setDefault(Helper::LOCALE);
$locale = new Locale(Locale::ZFDEFAULT);
Registry::set('Zend_Locale', $locale);

// Ensure LiveDocx credentials are available
if (false === Helper::credentialsAvailable()) {
    Helper::printLine(Helper::credentialsHowTo());
    exit();
}

unset($base);
