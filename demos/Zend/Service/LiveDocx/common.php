<?php

// Turn up error reporting
error_reporting (E_ALL|E_STRICT);

// Set path to libraries
set_include_path ('/usr/share/php-libs/zf/ZendFramework-1.9.5/library' . PATH_SEPARATOR .
                  '/usr/share/ZendFramework-Incubator/library'         . PATH_SEPARATOR);

// Demos_Zend_Service_LiveDocx_Helper
require_once dirname(__FILE__) . '/Helper.php';

// Set autoloader to autoload libraries
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();