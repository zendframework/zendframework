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