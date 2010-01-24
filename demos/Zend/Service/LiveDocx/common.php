<?php

// Turn up error reporting
error_reporting(E_ALL | E_STRICT);

// Set path to libraries
set_include_path(realpath(dirname(__FILE__) . '/../../../../library'));

// Demos_Zend_Service_LiveDocx_Helper
require_once dirname(__FILE__) . '/Helper.php';

// Set autoloader to autoload libraries
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

// Set default locale
Zend_Locale::setDefault(Demos_Zend_Service_LiveDocx_Helper::LOCALE);
$locale = new Zend_Locale(Zend_Locale::ZFDEFAULT);
Zend_Registry::set('Zend_Locale', $locale);

// Ensure LiveDocx credentials are available
if (false === Demos_Zend_Service_LiveDocx_Helper::credentialsAvailable()) {
    echo Demos_Zend_Service_LiveDocx_Helper::wrapLine(
            Demos_Zend_Service_LiveDocx_Helper::credentialsHowTo()
    );
    exit();
}