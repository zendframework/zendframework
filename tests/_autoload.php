<?php
/**
 * Setup autoloading
 */

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
} else {
    // if composer autoloader is missing, explicitly load the standard
    // autoloader by relativepath
    require_once __DIR__ . '/../library/Zend/Loader/StandardAutoloader.php';
}

$loader = new Zend\Loader\StandardAutoloader(array(
    Zend\Loader\StandardAutoloader::LOAD_NS => array(
        'Zend'     => __DIR__ . '/../library/Zend', // present in case composer autoloader missing
        'ZendTest' => __DIR__ . '/Zend',
    ),
));
$loader->register();
