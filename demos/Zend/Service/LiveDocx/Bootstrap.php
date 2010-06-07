<?php

// Set used namespaces
use Zend\Loader\Autoloader;
use Zend\Locale\Locale;
use Zend\Registry;
use Zend\Service\LiveDocx\Helper;

// Turn up error reporting
error_reporting(E_ALL | E_STRICT);

// Set path to libraries
set_include_path(
    __DIR__ . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR .
    dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'library'
);

// Set autoloader to autoload libraries
require_once 'Zend/Loader/Autoloader.php';
Autoloader::getInstance();

// Set default locale
Locale::setDefault(Helper::LOCALE);
$locale = new Locale(Locale::ZFDEFAULT);
Registry::set('Zend_Locale', $locale);

// Ensure LiveDocx credentials are available
if (false === Helper::credentialsAvailable()) {
    Helper::printLine(Helper::credentialsHowTo());
    exit();
}